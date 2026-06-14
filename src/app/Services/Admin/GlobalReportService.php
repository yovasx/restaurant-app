<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GlobalReportService
{
    public function generate(array $filters): array
    {
        $from = Carbon::parse($filters['from'] ?? now()->startOfMonth());
        $to = Carbon::parse($filters['to'] ?? now());

        $restauranteId = $filters['restaurante_id'] ?? null;
        $estadoRestaurante = $filters['estado_restaurante'] ?? null;
        $scoreFilter = $filters['score'] ?? null;

        return [
            'filters' => compact('from', 'to', 'restauranteId', 'estadoRestaurante', 'scoreFilter'),
            'kpis' => $this->kpis($from, $to, $restauranteId, $estadoRestaurante, $scoreFilter),
            'series' => [
                'altas_restaurantes' => $this->altasRestaurantes($from, $to),
                'altas_comensales' => $this->altasComensales($from, $to),
                'visitas_por_dia' => $this->visitasPorDia($from, $to, $restauranteId, $estadoRestaurante),
                'resenas_por_dia' => $this->resenasPorDia($from, $to, $restauranteId, $estadoRestaurante, $scoreFilter),
            ],
            'tables' => [
                'top_restaurantes_visitas' => $this->topRestaurantesVisitas($from, $to, $restauranteId, $estadoRestaurante),
                'top_restaurantes_rating' => $this->topRestaurantesRating($from, $to, $restauranteId, $estadoRestaurante, $scoreFilter),
                'bottom_restaurantes_rating' => $this->bottomRestaurantesRating($from, $to, $restauranteId, $estadoRestaurante, $scoreFilter),
                'promociones_por_estado' => $this->promocionesPorEstado($restauranteId, $estadoRestaurante),
                'productos_por_estado' => $this->productosPorEstado($restauranteId, $estadoRestaurante),
                'productos_sin_stock' => $this->productosSinStock($restauranteId, $estadoRestaurante),
                'resenas_por_score' => $this->resenasPorScore($from, $to, $restauranteId, $estadoRestaurante),
            ],
        ];
    }

    private function kpis(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante, ?int $score): array
    {
        $cuentasRestaurante = DB::table('usuarios')
            ->where('rol_id', 2)
            ->when($estadoRestaurante, function ($q, $v) {
                return $q->whereExists(function ($sub) use ($v) {
                    $sub->select(DB::raw(1))
                        ->from('restaurantes')
                        ->whereColumn('restaurantes.usuario_id', 'usuarios.id')
                        ->where('restaurantes.estado', $v);
                });
            })
            ->count();

        $sucursalesActivas = DB::table('restaurantes')
            ->where('estado', 'activo')
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->count();

        $totalComensales = DB::table('comensales')->count();

        $visitasQuery = DB::table('visitas')
            ->join('restaurantes', 'restaurantes.id', '=', 'visitas.restaurante_id')
            ->whereBetween('visitas.fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->when($restauranteId, fn($q, $v) => $q->where('visitas.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v));
        $visitasRango = (clone $visitasQuery)->count();

        $resenasQuery = DB::table('resenas')
            ->leftJoin('menus', 'menus.id', '=', 'resenas.menu_id')
            ->join('restaurantes', function ($j) {
                $j->on('restaurantes.id', '=', DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'));
            })
            ->whereBetween('resenas.created_at', [$from, $to])
            ->when($restauranteId, fn($q, $v) => $q->where(DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'), $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->when($score, fn($q, $v) => $q->where('resenas.score', $v));
        $resenasRango = (clone $resenasQuery)->count();
        $promedioScore = (clone $resenasQuery)->avg('resenas.score');

        $promocionesActivas = DB::table('promociones')
            ->join('restaurantes', 'restaurantes.id', '=', 'promociones.restaurante_id')
            ->where('promociones.estado', 'activo')
            ->where(function ($q) {
                $q->whereNull('promociones.fecha_fin')->orWhere('promociones.fecha_fin', '>=', now()->toDateString());
            })
            ->when($restauranteId, fn($q, $v) => $q->where('promociones.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->count();

        return compact(
            'cuentasRestaurante', 'sucursalesActivas', 'totalComensales',
            'visitasRango', 'resenasRango', 'promedioScore', 'promocionesActivas'
        );
    }

    private function altasRestaurantes(Carbon $from, Carbon $to): array
    {
        return DB::table('usuarios')
            ->where('rol_id', 2)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function altasComensales(Carbon $from, Carbon $to): array
    {
        return DB::table('comensales')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function visitasPorDia(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante): array
    {
        return DB::table('visitas')
            ->join('restaurantes', 'restaurantes.id', '=', 'visitas.restaurante_id')
            ->whereBetween('visitas.fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->when($restauranteId, fn($q, $v) => $q->where('visitas.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->selectRaw('visitas.fecha_visita as fecha, COUNT(*) as total')
            ->groupByRaw('visitas.fecha_visita')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function resenasPorDia(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante, ?int $score): array
    {
        return DB::table('resenas')
            ->leftJoin('menus', 'menus.id', '=', 'resenas.menu_id')
            ->join('restaurantes', function ($j) {
                $j->on('restaurantes.id', '=', DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'));
            })
            ->whereBetween('resenas.created_at', [$from, $to])
            ->when($restauranteId, fn($q, $v) => $q->where(DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'), $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->when($score, fn($q, $v) => $q->where('resenas.score', $v))
            ->selectRaw('DATE(resenas.created_at) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(resenas.created_at)')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function topRestaurantesVisitas(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante): array
    {
        return DB::table('visitas')
            ->join('restaurantes', 'restaurantes.id', '=', 'visitas.restaurante_id')
            ->whereBetween('visitas.fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->when($restauranteId, fn($q, $v) => $q->where('visitas.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->selectRaw('restaurantes.id, restaurantes.nombre, COUNT(visitas.id) as total')
            ->groupBy('restaurantes.id', 'restaurantes.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function topRestaurantesRating(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante, ?int $score): array
    {
        return DB::table('resenas')
            ->leftJoin('menus', 'menus.id', '=', 'resenas.menu_id')
            ->join('restaurantes', function ($j) {
                $j->on('restaurantes.id', '=', DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'));
            })
            ->whereBetween('resenas.created_at', [$from, $to])
            ->when($restauranteId, fn($q, $v) => $q->where(DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'), $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->when($score, fn($q, $v) => $q->where('resenas.score', $v))
            ->selectRaw('restaurantes.id, restaurantes.nombre, AVG(resenas.score) as promedio, COUNT(resenas.id) as total_resenas')
            ->groupBy('restaurantes.id', 'restaurantes.nombre')
            ->havingRaw('COUNT(resenas.id) >= 3')
            ->orderByDesc('promedio')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function bottomRestaurantesRating(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante, ?int $score): array
    {
        return DB::table('resenas')
            ->leftJoin('menus', 'menus.id', '=', 'resenas.menu_id')
            ->join('restaurantes', function ($j) {
                $j->on('restaurantes.id', '=', DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'));
            })
            ->whereBetween('resenas.created_at', [$from, $to])
            ->when($restauranteId, fn($q, $v) => $q->where(DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'), $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->when($score, fn($q, $v) => $q->where('resenas.score', $v))
            ->selectRaw('restaurantes.id, restaurantes.nombre, AVG(resenas.score) as promedio, COUNT(resenas.id) as total_resenas')
            ->groupBy('restaurantes.id', 'restaurantes.nombre')
            ->havingRaw('COUNT(resenas.id) >= 3')
            ->orderBy('promedio')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function promocionesPorEstado(?int $restauranteId, ?string $estadoRestaurante): array
    {
        $hoy = now()->toDateString();
        $todas = DB::table('promociones')
            ->join('restaurantes', 'restaurantes.id', '=', 'promociones.restaurante_id')
            ->when($restauranteId, fn($q, $v) => $q->where('promociones.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->get(['promociones.id', 'promociones.nombre', 'promociones.estado', 'promociones.fecha_fin']);

        return [
            'activas' => $todas->filter(fn($item) =>
                $item->estado === 'activo' && ($item->fecha_fin === null || $item->fecha_fin >= $hoy)
            )->values()->toArray(),
            'inactivas' => $todas->where('estado', 'inactivo')->values()->toArray(),
            'vencidas' => $todas->filter(fn($item) =>
                $item->estado === 'activo' && $item->fecha_fin !== null && $item->fecha_fin < $hoy
            )->values()->toArray(),
        ];
    }

    private function productosPorEstado(?int $restauranteId, ?string $estadoRestaurante): array
    {
        $productos = DB::table('productos')
            ->join('restaurantes', 'restaurantes.id', '=', 'productos.restaurante_id')
            ->when($restauranteId, fn($q, $v) => $q->where('productos.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->selectRaw("
                SUM(CASE WHEN productos.activo IS TRUE THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN productos.activo IS FALSE THEN 1 ELSE 0 END) as inactivos,
                SUM(CASE WHEN productos.stock = 0 AND productos.activo IS TRUE THEN 1 ELSE 0 END) as sin_stock
            ")
            ->first();

        return [
            'activos' => (int) ($productos->activos ?? 0),
            'inactivos' => (int) ($productos->inactivos ?? 0),
            'sin_stock' => (int) ($productos->sin_stock ?? 0),
        ];
    }

    private function productosSinStock(?int $restauranteId, ?string $estadoRestaurante): array
    {
        return DB::table('productos')
            ->join('restaurantes', 'restaurantes.id', '=', 'productos.restaurante_id')
            ->where('productos.activo', true)
            ->where('productos.stock', 0)
            ->when($restauranteId, fn($q, $v) => $q->where('productos.restaurante_id', $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->select('productos.id', 'productos.nombre', 'restaurantes.nombre as restaurante')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function resenasPorScore(Carbon $from, Carbon $to, ?int $restauranteId, ?string $estadoRestaurante): array
    {
        $rows = DB::table('resenas')
            ->leftJoin('menus', 'menus.id', '=', 'resenas.menu_id')
            ->join('restaurantes', function ($j) {
                $j->on('restaurantes.id', '=', DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'));
            })
            ->whereBetween('resenas.created_at', [$from, $to])
            ->when($restauranteId, fn($q, $v) => $q->where(DB::raw('COALESCE(resenas.restaurante_id, menus.restaurante_id)'), $v))
            ->when($estadoRestaurante, fn($q, $v) => $q->where('restaurantes.estado', $v))
            ->selectRaw('resenas.score, COUNT(*) as total')
            ->groupBy('resenas.score')
            ->orderBy('resenas.score')
            ->get()
            ->keyBy('score');

        $distribucion = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribucion[$i] = (int) ($rows->get($i)->total ?? 0);
        }
        return $distribucion;
    }
}
