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

        $days = $from->diffInDays($to) ?: 30;
        $prevFrom = (clone $from)->subDays($days);
        $prevTo = (clone $from)->subSecond();

        $currentKpis = $this->kpis($restauranteIds, $from, $to);
        $prevKpis = $this->kpis($restauranteIds, $prevFrom, $prevTo);

        $seriesVisitas = $this->visitasPorDia($restauranteIds, $from, $to);
        $seriesResenas = $this->resenasPorDia($restauranteIds, $from, $to);
        $seriesScore = $this->promedioScorePorDia($restauranteIds, $from, $to);

        $delta = fn($curr, $prev) => $prev > 0 ? round((($curr - $prev) / $prev) * 100) : ($curr > 0 ? 100 : 0);

        $temporalKeys = ['visitas', 'resenas', 'promedio_score'];
        $kpiCards = [];
        foreach ($currentKpis as $key => $value) {
            $prevVal = $prevKpis[$key] ?? 0;
            $kpiCards[$key] = [
                'current' => $value,
                'previous' => $prevVal,
                'delta' => in_array($key, $temporalKeys, true) ? $delta($value, $prevVal) : 0,
            ];
        }

        $sparklines = $this->buildSparklines($seriesVisitas, $seriesResenas, $seriesScore);

        $insights = [];
        $vDelta = $kpiCards['visitas']['delta'] ?? 0;
        $rDelta = $kpiCards['resenas']['delta'] ?? 0;
        $score = $kpiCards['promedio_score']['current'] ?? 0;
        if ($vDelta > 5) $insights[] = 'Las visitas subieron respecto al periodo anterior';
        elseif ($vDelta < -5) $insights[] = 'Las visitas bajaron respecto al periodo anterior';
        if ($rDelta > 5) $insights[] = 'Más reseñas que en el periodo anterior';
        elseif ($rDelta < -5) $insights[] = 'Menos reseñas que en el periodo anterior';
        if ($score >= 4.5) $insights[] = 'Excelente calificación promedio';
        elseif ($score < 3 && $score > 0) $insights[] = 'Calificación promedio por debajo de 3';
        if (($kpiCards['productos_sin_stock']['current'] ?? 0) > 0) {
            $insights[] = $kpiCards['productos_sin_stock']['current'] . ' producto(s) sin stock requieren atención';
        }

        $categories = collect($seriesVisitas)->pluck('fecha')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();

        return [
            'filters' => [
                'from' => $from,
                'to' => $to,
                'scope' => $filters['scope'] ?? 'sucursal_activa',
                'restaurante_id' => $filters['restaurante_id'] ?? null,
                'total_sucursales' => count($restauranteIds),
            ],
            'range' => [
                'days' => $days,
                'from' => $from->format('d/m/Y'),
                'to' => $to->format('d/m/Y'),
                'prev_from' => $prevFrom->format('d/m/Y'),
                'prev_to' => $prevTo->format('d/m/Y'),
            ],
            'kpis' => $currentKpis,
            'kpiCards' => $kpiCards,
            'sparklines' => $sparklines,
            'insights' => $insights,
            'chartCategories' => $categories,
            'series' => [
                'visitas_por_dia' => $seriesVisitas,
                'resenas_por_dia' => $seriesResenas,
                'promedio_score_por_dia' => $seriesScore,
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

    public function resolveIds(int $usuarioId, array $filters): array
    {
        $query = Restaurante::where('usuario_id', $usuarioId);

        $scope = $filters['scope'] ?? 'sucursal_activa';
        $restauranteId = $filters['restaurante_id'] ?? null;

        if ($scope === 'sucursal_activa' && $restauranteId) {
            $query->where('id', $restauranteId);
        }

        return $query->pluck('id')->toArray();
    }

    private function buildSparklines(array $visitas, array $resenas, array $score): array
    {
        $val = fn($i, string $field): int => (int) (is_array($i) ? ($i[$field] ?? 0) : ($i->$field ?? 0));
        $last = fn(array $items, string $field, int $n = 7): array => array_values(
            array_map(fn($i) => $val($i, $field), array_slice($items, -$n))
        );
        return [
            'visitas' => $last($visitas, 'total'),
            'resenas' => $last($resenas, 'total'),
            'promedio_score' => $last($score, 'total'),
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
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy))
            ->where(fn($q) => $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy))
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
