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

class RestaurantDashboardService
{
    public function generate(int|array $restauranteId): array
    {
        $ids = is_array($restauranteId) ? $restauranteId : [$restauranteId];

        if (empty($ids)) {
            return $this->empty();
        }

        $single = count($ids) === 1 ? Restaurante::find($ids[0]) : null;

        $from = now()->subDays(30)->startOfDay();
        $to = now()->endOfDay();

        $multi = count($ids) > 1;

        return [
            'kpis' => $this->kpis($ids, $from, $to),
            'alerts' => $multi ? $this->multiAlerts($ids, $from) : $this->alerts($single, $from),
            'latestReviews' => $this->latestReviews($ids),
            'scoreDistribution' => $this->scoreDistribution($ids, $from, $to),
            'promoSummary' => $this->promoSummary($ids),
            'branchSummary' => $single,
            'series' => [
                'visitas_por_dia' => $this->visitasPorDia($ids, $from, $to),
                'resenas_por_dia' => $this->resenasPorDia($ids, $from, $to),
            ],
            'tables' => [
                'top_platos_resenas' => $this->topPlatosResenas($ids, $from, $to),
                'top_platos_score' => $this->topPlatosScore($ids, $from, $to),
                'productos_sin_stock' => $this->productosSinStock($ids),
            ],
            'breakdowns' => [
                'promociones_por_estado' => $this->promocionesPorEstado($ids),
                'productos_por_estado' => $this->productosPorEstado($ids),
            ],
        ];
    }

    public function empty(): array
    {
        return [
            'kpis' => [
                'productos_activos' => 0,
                'productos_sin_stock' => 0,
                'promociones_activas' => 0,
                'visitas_30d' => 0,
                'resenas_30d' => 0,
                'promedio_score_30d' => 0,
            ],
            'alerts' => [],
            'latestReviews' => [],
            'scoreDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'promoSummary' => ['activas' => 0, 'vencidas' => 0, 'inactivas' => 0],
            'branchSummary' => null,
            'series' => ['visitas_por_dia' => [], 'resenas_por_dia' => []],
            'tables' => [
                'top_platos_resenas' => [],
                'top_platos_score' => [],
                'productos_sin_stock' => [],
            ],
            'breakdowns' => [
                'promociones_por_estado' => ['activas' => [], 'inactivas' => [], 'vencidas' => []],
                'productos_por_estado' => ['activos' => 0, 'inactivos' => 0, 'sin_stock' => 0],
            ],
        ];
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
            'visitas_30d' => $visitas,
            'resenas_30d' => $resenas,
            'promedio_score_30d' => $promedio ? round($promedio, 1) : 0,
        ];
    }

    private function alerts(?Restaurante $r, Carbon $since): array
    {
        if (!$r) {
            return [];
        }

        $alerts = [];

        $sinStock = Producto::where('restaurante_id', $r->id)
            ->where('activo', true)->where('stock', 0)->count();
        if ($sinStock > 0) {
            $alerts[] = [
                'type' => 'sin_stock',
                'label' => 'Productos sin stock',
                'count' => $sinStock,
                'severity' => 'warning',
            ];
        }

        $promosVencidas = Promocion::where('restaurante_id', $r->id)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now()->toDateString())
            ->count();
        if ($promosVencidas > 0) {
            $alerts[] = [
                'type' => 'promos_vencidas',
                'label' => 'Promociones vencidas',
                'count' => $promosVencidas,
                'severity' => 'warning',
            ];
        }

        $incomplete = !$r->direccion || !$r->zona || !$r->telefono || !$r->email_reservas || !$r->latitud || !$r->longitud;
        if ($incomplete) {
            $alerts[] = [
                'type' => 'perfil_incompleto',
                'label' => 'Perfil del local incompleto',
                'count' => 1,
                'severity' => 'info',
            ];
        }

        $sinVisitas = Visita::where('restaurante_id', $r->id)
            ->where('fecha_visita', '>=', $since->toDateString())
            ->count() === 0;
        if ($sinVisitas) {
            $alerts[] = [
                'type' => 'sin_visitas',
                'label' => 'Sin visitas en 30 días',
                'count' => 1,
                'severity' => 'info',
            ];
        }

        $menuIds = Menu::where('restaurante_id', $r->id)->pluck('id');
        $sinResenas = Resena::whereIn('menu_id', $menuIds)
            ->where('created_at', '>=', $since)
            ->count() === 0;
        if ($sinResenas) {
            $alerts[] = [
                'type' => 'sin_resenas',
                'label' => 'Sin reseñas en 30 días',
                'count' => 1,
                'severity' => 'info',
            ];
        }

        return $alerts;
    }

    private function multiAlerts(array $ids, Carbon $since): array
    {
        $alerts = [];

        $sinStock = Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->where('stock', 0)->count();
        if ($sinStock > 0) {
            $alerts[] = [
                'type' => 'sin_stock',
                'label' => 'Productos sin stock',
                'count' => $sinStock,
                'severity' => 'warning',
            ];
        }

        $promosVencidas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now()->toDateString())
            ->count();
        if ($promosVencidas > 0) {
            $alerts[] = [
                'type' => 'promos_vencidas',
                'label' => 'Promociones vencidas',
                'count' => $promosVencidas,
                'severity' => 'warning',
            ];
        }

        $visitasCount = Visita::whereIn('restaurante_id', $ids)
            ->where('fecha_visita', '>=', $since->toDateString())
            ->count();
        if ($visitasCount === 0) {
            $alerts[] = [
                'type' => 'sin_visitas',
                'label' => 'Sin visitas en 30 días',
                'count' => 1,
                'severity' => 'info',
            ];
        }

        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        $resenasCount = Resena::whereIn('menu_id', $menuIds)
            ->where('created_at', '>=', $since)
            ->count();
        if ($resenasCount === 0) {
            $alerts[] = [
                'type' => 'sin_resenas',
                'label' => 'Sin reseñas en 30 días',
                'count' => 1,
                'severity' => 'info',
            ];
        }

        return $alerts;
    }

    private function latestReviews(array $ids): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        return Resena::whereIn('menu_id', $menuIds)
            ->with(['comensal:id,nombre,apellido_paterno', 'menu:id,nombre'])
            ->latest()
            ->limit(5)
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

    private function promoSummary(array $ids): array
    {
        $hoy = now()->toDateString();

        $activas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy);
            })
            ->count();

        $vencidas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', $hoy)
            ->count();

        $inactivas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'inactivo')
            ->count();

        return compact('activas', 'vencidas', 'inactivas');
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
