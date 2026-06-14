<?php

namespace App\Services\Restaurante;

use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Visita;
use App\Models\Resena;
use App\Models\Menu;
use App\Models\Restaurante;
use App\Services\AnalyticsRangeService;
use App\Services\TimeSeriesNormalizer;
use App\Services\Restaurante\RestaurantForecastService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RestaurantDashboardService
{
    public function generate(int|array $restauranteId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $ids = is_array($restauranteId) ? $restauranteId : [$restauranteId];

        if (empty($ids)) {
            return $this->empty($from, $to);
        }

        $from ??= now()->subDays(30)->startOfDay();
        $to ??= now()->endOfDay();

        $days = $from->diffInDays($to) ?: 30;
        $prevFrom = (clone $from)->subDays($days);
        $prevTo = (clone $from)->subSecond();

        $single = count($ids) === 1 ? Restaurante::find($ids[0]) : null;
        $multi = count($ids) > 1;

        $currentKpis = $this->kpis($ids, $from, $to);
        $prevKpis = $this->kpis($ids, $prevFrom, $prevTo);

        $seriesVisitas = $this->visitasPorDia($ids, $from, $to);
        $seriesResenas = $this->resenasPorDia($ids, $from, $to);
        $seriesScore = $this->promedioScorePorDia($ids, $from, $to);

        $kpis = $this->buildKpis($currentKpis, $prevKpis);
        $sparklines = $this->buildSparklines($seriesVisitas, $seriesResenas, $seriesScore);
        $insights = $this->buildInsights($kpis);

        $predicciones = $this->predicciones($ids, $from, $to);

        return [
            'kpis' => $kpis,
            'range' => [
                'days' => $days,
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'prev_from' => $prevFrom->format('d/m/Y'),
                'prev_to' => $prevTo->format('d/m/Y'),
            ],
            'insights' => $insights,
            'sparklines' => $sparklines,
            'alerts' => $multi ? $this->multiAlerts($ids, $from) : $this->alerts($single, $from),
            'latestReviews' => $this->latestReviews($ids),
            'scoreDistribution' => $this->scoreDistribution($ids, $from, $to),
            'promoSummary' => $this->promoSummary($ids),
            'branchSummary' => $single,
            'series' => [
                'visitas_por_dia' => $seriesVisitas,
                'resenas_por_dia' => $seriesResenas,
                'promedio_score_por_dia' => $seriesScore,
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
            'timeline' => $this->timeline($ids),
            'predicciones' => $predicciones,
        ];
    }

    public function empty(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from ??= now()->subDays(30)->startOfDay();
        $to ??= now()->endOfDay();
        $days = $from->diffInDays($to) ?: 30;

        return [
            'kpis' => [
                'platosActivos' => ['current' => 0, 'previous' => 0, 'delta' => 0],
                'sinStock' => ['current' => 0, 'previous' => 0, 'delta' => 0],
                'promocionesActivas' => ['current' => 0, 'previous' => 0, 'delta' => 0],
                'visitas' => ['current' => 0, 'previous' => 0, 'delta' => 0],
                'resenas' => ['current' => 0, 'previous' => 0, 'delta' => 0],
                'promedioScore' => ['current' => 0, 'previous' => 0, 'delta' => 0],
            ],
            'range' => [
                'days' => $days,
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'prev_from' => (clone $from)->subDays($days)->format('d/m/Y'),
                'prev_to' => (clone $from)->subSecond()->format('d/m/Y'),
            ],
            'insights' => [],
            'sparklines' => ['visitas' => [], 'resenas' => [], 'promedio_score' => []],
            'alerts' => [],
            'latestReviews' => [],
            'scoreDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'promoSummary' => ['activas' => 0, 'vencidas' => 0, 'inactivas' => 0],
            'branchSummary' => null,
            'series' => ['visitas_por_dia' => [], 'resenas_por_dia' => [], 'promedio_score_por_dia' => []],
            'tables' => [
                'top_platos_resenas' => [],
                'top_platos_score' => [],
                'productos_sin_stock' => [],
            ],
            'breakdowns' => [
                'promociones_por_estado' => ['activas' => [], 'inactivas' => [], 'vencidas' => []],
                'productos_por_estado' => ['activos' => 0, 'inactivos' => 0, 'sin_stock' => 0],
            ],
            'timeline' => [],
            'predicciones' => [
                'forecast_summary' => null,
            ],
        ];
    }

    private function buildKpis(array $current, array $previous): array
    {
        $temporalKeys = ['visitas', 'resenas', 'promedio_score'];

        $result = [];
        foreach ($current as $key => $value) {
            $prevVal = $previous[$key] ?? 0;
            $d = in_array($key, $temporalKeys, true) ? AnalyticsRangeService::delta($value, $prevVal) : 0;

            $mapKey = match ($key) {
                'productos_activos' => 'platosActivos',
                'productos_sin_stock' => 'sinStock',
                'promociones_activas' => 'promocionesActivas',
                'visitas_30d' => 'visitas',
                'resenas_30d' => 'resenas',
                'promedio_score_30d' => 'promedioScore',
                default => $key,
            };

            $result[$mapKey] = [
                'current' => $value,
                'previous' => $prevVal,
                'delta' => $d,
            ];
        }
        return $result;
    }

    private function buildSparklines(array $visitas, array $resenas, array $score): array
    {
        return [
            'visitas' => TimeSeriesNormalizer::lastValues($visitas, 7),
            'resenas' => TimeSeriesNormalizer::lastValues($resenas, 7),
            'promedio_score' => TimeSeriesNormalizer::lastValues($score, 7),
        ];
    }

    private function buildInsights(array $kpis): array
    {
        $insights = [];

        $up = fn($k) => ($kpis[$k]['delta'] ?? 0) > 5;
        $down = fn($k) => ($kpis[$k]['delta'] ?? 0) < -5;

        if ($up('visitas')) {
            $insights[] = 'Las visitas subieron respecto al periodo anterior';
        } elseif ($down('visitas')) {
            $insights[] = 'Las visitas bajaron respecto al periodo anterior';
        }

        if ($up('resenas')) {
            $insights[] = 'Más reseñas que en el periodo anterior';
        } elseif ($down('resenas')) {
            $insights[] = 'Menos reseñas que en el periodo anterior';
        }

        $score = $kpis['promedioScore']['current'] ?? 0;
        if ($score >= 4.5) {
            $insights[] = 'Excelente calificación promedio';
        } elseif ($score < 3) {
            $insights[] = 'Calificación promedio por debajo de 3';
        }

        if (($kpis['sinStock']['current'] ?? 0) > 0) {
            $insights[] = "{$kpis['sinStock']['current']} producto(s) sin stock requieren atención";
        }

        return $insights;
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
        if (!$r) return [];

        $alerts = [];

        $sinStock = Producto::where('restaurante_id', $r->id)
            ->where('activo', true)->where('stock', 0)->count();
        if ($sinStock > 0) {
            $alerts[] = ['type' => 'sin_stock', 'label' => 'Productos sin stock', 'count' => $sinStock, 'severity' => 'warning'];
        }

        $promosVencidas = Promocion::where('restaurante_id', $r->id)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now()->toDateString())
            ->count();
        if ($promosVencidas > 0) {
            $alerts[] = ['type' => 'promos_vencidas', 'label' => 'Promociones vencidas', 'count' => $promosVencidas, 'severity' => 'warning'];
        }

        $incomplete = !$r->direccion || !$r->zona || !$r->telefono || !$r->email_reservas || !$r->latitud || !$r->longitud;
        if ($incomplete) {
            $alerts[] = ['type' => 'perfil_incompleto', 'label' => 'Perfil del local incompleto', 'count' => 1, 'severity' => 'info'];
        }

        $sinVisitas = Visita::where('restaurante_id', $r->id)
            ->where('fecha_visita', '>=', $since->toDateString())->count() === 0;
        if ($sinVisitas) {
            $alerts[] = ['type' => 'sin_visitas', 'label' => 'Sin visitas en 30 días', 'count' => 1, 'severity' => 'info'];
        }

        $menuIds = Menu::where('restaurante_id', $r->id)->pluck('id');
        $sinResenas = Resena::whereIn('menu_id', $menuIds)
            ->where('created_at', '>=', $since)->count() === 0;
        if ($sinResenas) {
            $alerts[] = ['type' => 'sin_resenas', 'label' => 'Sin reseñas en 30 días', 'count' => 1, 'severity' => 'info'];
        }

        return $alerts;
    }

    private function multiAlerts(array $ids, Carbon $since): array
    {
        $alerts = [];

        $sinStock = Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->where('stock', 0)->count();
        if ($sinStock > 0) {
            $alerts[] = ['type' => 'sin_stock', 'label' => 'Productos sin stock', 'count' => $sinStock, 'severity' => 'warning'];
        }

        $promosVencidas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now()->toDateString())
            ->count();
        if ($promosVencidas > 0) {
            $alerts[] = ['type' => 'promos_vencidas', 'label' => 'Promociones vencidas', 'count' => $promosVencidas, 'severity' => 'warning'];
        }

        $visitasCount = Visita::whereIn('restaurante_id', $ids)
            ->where('fecha_visita', '>=', $since->toDateString())->count();
        if ($visitasCount === 0) {
            $alerts[] = ['type' => 'sin_visitas', 'label' => 'Sin visitas en 30 días', 'count' => 1, 'severity' => 'info'];
        }

        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        $resenasCount = Resena::whereIn('menu_id', $menuIds)
            ->where('created_at', '>=', $since)->count();
        if ($resenasCount === 0) {
            $alerts[] = ['type' => 'sin_resenas', 'label' => 'Sin reseñas en 30 días', 'count' => 1, 'severity' => 'info'];
        }

        return $alerts;
    }

    private function latestReviews(array $ids): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        return Resena::whereIn('menu_id', $menuIds)
            ->with(['comensal:id,nombre,apellido_paterno', 'menu:id,nombre'])
            ->latest()->limit(5)->get()->toArray();
    }

    private function scoreDistribution(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        $scores = DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('score, COUNT(*) as total')
            ->groupBy('score')->orderBy('score')
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
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy))
            ->where(fn($q) => $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy))
            ->count();
        $vencidas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')->whereNotNull('fecha_fin')->where('fecha_fin', '<', $hoy)
            ->count();
        $inactivas = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'inactivo')->count();
        return compact('activas', 'vencidas', 'inactivas');
    }

    private function visitasPorDia(array $ids, Carbon $from, Carbon $to): array
    {
        return Visita::whereIn('restaurante_id', $ids)
            ->whereBetween('fecha_visita', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('fecha_visita as fecha, COUNT(*) as total')
            ->groupByRaw('fecha_visita')->orderBy('fecha')
            ->get()->toArray();
    }

    private function resenasPorDia(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        return DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')->orderBy('fecha')
            ->get()->toArray();
    }

    private function promedioScorePorDia(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        return DB::table('resenas')
            ->whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as fecha, ROUND(AVG(score), 1) as total')
            ->groupByRaw('DATE(created_at)')->orderBy('fecha')
            ->get()->toArray();
    }

    private function topPlatosResenas(array $ids, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');
        return DB::table('resenas')
            ->join('menus', 'menus.id', '=', 'resenas.menu_id')
            ->whereIn('resenas.menu_id', $menuIds)
            ->whereBetween('resenas.created_at', [$from, $to])
            ->selectRaw('menus.id, menus.nombre, COUNT(resenas.id) as total')
            ->groupBy('menus.id', 'menus.nombre')->orderByDesc('total')->limit(10)
            ->get()->toArray();
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
            ->orderByDesc('promedio')->limit(10)
            ->get()->toArray();
    }

    private function productosSinStock(array $ids): array
    {
        return Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->where('stock', 0)
            ->select('id', 'nombre')->limit(20)
            ->get()->toArray();
    }

    private function promocionesPorEstado(array $ids): array
    {
        $hoy = now()->toDateString();
        $todas = Promocion::whereIn('restaurante_id', $ids)
            ->get(['id', 'nombre', 'estado', 'fecha_fin']);
        return [
            'activas' => $todas->filter(fn($i) => $i->estado === 'activo' && ($i->fecha_fin === null || $i->fecha_fin >= $hoy))->values()->toArray(),
            'inactivas' => $todas->where('estado', 'inactivo')->values()->toArray(),
            'vencidas' => $todas->filter(fn($i) => $i->estado === 'activo' && $i->fecha_fin !== null && $i->fecha_fin < $hoy)->values()->toArray(),
        ];
    }

    private function timeline(array $ids): array
    {
        $menuIds = Menu::whereIn('restaurante_id', $ids)->pluck('id');

        $recentResenas = Resena::whereIn('menu_id', $menuIds)
            ->with(['menu:id,nombre', 'comensal:id,nombre,apellido_paterno'])
            ->latest()->limit(5)->get()
            ->map(fn($r) => [
                'type' => 'resena',
                'label' => "Nueva reseña de {$r->comensal->nombre}",
                'detail' => "{$r->score}★ · {$r->menu->nombre}",
                'icon' => 'rate_review',
                'iconColor' => 'text-amber-500',
                'time' => $r->created_at,
            ]);

        $sinStock = Producto::whereIn('restaurante_id', $ids)
            ->where('activo', true)->where('stock', 0)
            ->latest('updated_at')->limit(3)->get()
            ->map(fn($p) => [
                'type' => 'sin_stock',
                'label' => "{$p->nombre} sin stock",
                'detail' => 'Requiere reposición',
                'icon' => 'inventory_2',
                'iconColor' => 'text-red-500',
                'time' => $p->updated_at,
            ]);

        $promoExpiring = Promocion::whereIn('restaurante_id', $ids)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '>=', now()->toDateString())
            ->where('fecha_fin', '<=', now()->addDays(7)->toDateString())
            ->limit(3)->get()
            ->map(fn($p) => [
                'type' => 'promo_expira',
                'label' => "{$p->nombre} por vencer",
                'detail' => 'Vence ' . \Carbon\Carbon::parse($p->fecha_fin)->diffForHumans(),
                'icon' => 'local_offer',
                'iconColor' => 'text-orange-500',
                'time' => $p->fecha_fin,
            ]);

        $combined = collect($recentResenas)
            ->concat($sinStock)
            ->concat($promoExpiring)
            ->sortByDesc('time')
            ->values()
            ->take(8)
            ->toArray();

        return $combined;
    }

    private function predicciones(array $ids, Carbon $from, Carbon $to): array
    {
        $forecast = app(RestaurantForecastService::class)->summary($ids);

        return [
            'forecast_summary' => $forecast,
        ];
    }

    private function productosPorEstado(array $ids): array
    {
        $p = Producto::whereIn('restaurante_id', $ids)
            ->selectRaw("SUM(CASE WHEN activo IS TRUE THEN 1 ELSE 0 END) as activos, SUM(CASE WHEN activo IS FALSE THEN 1 ELSE 0 END) as inactivos, SUM(CASE WHEN stock = 0 AND activo IS TRUE THEN 1 ELSE 0 END) as sin_stock")
            ->first();
        return [
            'activos' => (int) ($p->activos ?? 0),
            'inactivos' => (int) ($p->inactivos ?? 0),
            'sin_stock' => (int) ($p->sin_stock ?? 0),
        ];
    }
}
