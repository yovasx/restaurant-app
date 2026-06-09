<?php

namespace App\Services\Restaurante;

use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Visita;
use App\Models\Resena;
use App\Models\Menu;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RestaurantReportService
{
    public function generate(int $usuarioId, array $filters): array
    {
        $from = Carbon::parse($filters['from'] ?? now()->subDays(30));
        $to = Carbon::parse($filters['to'] ?? now());
        $restauranteIds = $this->resolveIds($usuarioId, $filters);

        return [
            'filters' => [
                'from' => $from,
                'to' => $to,
                'scope' => $filters['scope'] ?? 'sucursal_activa',
                'restaurante_id' => $filters['restaurante_id'] ?? null,
                'total_sucursales' => count($restauranteIds),
            ],
            'kpis' => $this->kpis($restauranteIds, $from, $to),
            'series' => [
                'visitas_por_dia' => $this->visitasPorDia($restauranteIds, $from, $to),
                'resenas_por_dia' => $this->resenasPorDia($restauranteIds, $from, $to),
                'promedio_score_por_dia' => $this->promedioScorePorDia($restauranteIds, $from, $to),
            ],
            'tables' => [
                'top_platos_resenas' => $this->topPlatosResenas($restauranteIds, $from, $to),
                'top_platos_score' => $this->topPlatosScore($restauranteIds, $from, $to),
                'productos_sin_stock' => $this->productosSinStock($restauranteIds),
            ],
            'breakdowns' => [
                'score_distribution' => $this->scoreDistribution($restauranteIds, $from, $to),
                'promociones_por_estado' => $this->promocionesPorEstado($restauranteIds),
                'productos_por_estado' => $this->productosPorEstado($restauranteIds),
            ],
        ];
    }

    private function resolveIds(int $usuarioId, array $filters): array
    {
        $query = Restaurante::where('usuario_id', $usuarioId);

        $scope = $filters['scope'] ?? 'sucursal_activa';
        $restauranteId = $filters['restaurante_id'] ?? null;

        if ($scope === 'sucursal_activa' && $restauranteId) {
            $query->where('id', $restauranteId);
        }

        return $query->pluck('id')->toArray();
    }

    private function kpis(array $ids, Carbon $from, Carbon $to): array
    {
        $productosActivos = Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->count();

        $productosSinStock = Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->where('stock', 0)->count();

        $hoy = now()->toDateString();

        $promocionesActivas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy);
            })
            ->count();

        $visitas = Visita::whereIn('restaurante_id', $ids)
            ->whereBetween('fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->count();

        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        $resenasQuery = Resena::whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to]);

        $resenas = (clone $resenasQuery)->count();
        $promedio = (clone $resenasQuery)->avg('score');

        return [
            'productos_activos' => $productosActivos,
            'productos_sin_stock' => $productosSinStock,
            'promociones_activas' => $promocionesActivas,
            'visitas' => $visitas,
            'resenas' => $resenas,
            'promedio_score' => $promedio ? round($promedio, 1) : 0,
        ];
    }

    private function visitasPorDia(array $ids, Carbon $from, Carbon $to): array
    {
        return Visita::whereIn('restaurante_id', $ids)
            ->whereBetween('fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('fecha_visita as fecha, COUNT(*) as total')
            ->groupByRaw('fecha_visita')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function resenasPorDia(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        return DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function promedioScorePorDia(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        return DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, ROUND(AVG(score), 1) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function topPlatosResenas(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        return DB::table('resenas')
            ->join('menus', 'menus.id', '=', 'resenas.menu_id')
            ->whereIn('resenas.menu_id', $menuIds)
            ->whereBetween('resenas.created_at', [$from, $to])
            ->selectRaw('menus.id, menus.nombre, COUNT(resenas.id) as total')
            ->groupBy('menus.id', 'menus.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function topPlatosScore(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        return DB::table('resenas')
            ->join('menus', 'menus.id', '=', 'resenas.menu_id')
            ->whereIn('resenas.menu_id', $menuIds)
            ->whereBetween('resenas.created_at', [$from, $to])
            ->selectRaw('menus.id, menus.nombre, AVG(resenas.score) as promedio, COUNT(resenas.id) as total_resenas')
            ->groupBy('menus.id', 'menus.nombre')
            ->havingRaw('COUNT(resenas.id) >= 2')
            ->orderByDesc('promedio')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function productosSinStock(array $ids): array
    {
        return Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)
            ->where('stock', 0)
            ->select('id', 'nombre')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function scoreDistribution(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        $scores = DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('score, COUNT(*) as total')
            ->groupBy('score')
            ->orderBy('score')
            ->pluck('total', 'score');

        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $result[$i] = $scores[$i] ?? 0;
        }
        return $result;
    }

    private function promocionesPorEstado(array $ids): array
    {
        $hoy = now()->toDateString();
        $todas = Promocion::whereIn('restaurante_id', $ids)
            ->get(['id', 'nombre', 'estado', 'fecha_fin']);

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

    private function productosPorEstado(array $ids): array
    {
        $productos = Producto::whereIn('restaurante_id', $ids)
            ->selectRaw("
                SUM(CASE WHEN activo IS TRUE THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN activo IS FALSE THEN 1 ELSE 0 END) as inactivos,
                SUM(CASE WHEN stock = 0 AND activo IS TRUE THEN 1 ELSE 0 END) as sin_stock
            ")
            ->first();

        return [
            'activos' => (int) ($productos->activos ?? 0),
            'inactivos' => (int) ($productos->inactivos ?? 0),
            'sin_stock' => (int) ($productos->sin_stock ?? 0),
        ];
    }
}
