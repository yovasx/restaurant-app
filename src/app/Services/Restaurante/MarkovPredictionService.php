<?php

namespace App\Services\Restaurante;

use App\Models\Pedido;
use App\Models\Menu;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MarkovPredictionService
{
    public function peakHourPredictions(array $restauranteIds, int $days = 45): array
    {
        $slots = [
            ['label' => '12:00-14:00', 'start' => 11, 'end' => 14],
            ['label' => '19:00-21:00', 'start' => 18, 'end' => 21],
        ];

        $since = now()->subDays($days)->startOfDay();
        $results = [];

        $menuIds = Menu::whereIn('restaurante_id', $restauranteIds)->pluck('id', 'id');

        foreach ($slots as $slot) {
            $orderCounts = DB::table('pedidos')
                ->join('detalle_pedido', 'detalle_pedido.pedido_id', '=', 'pedidos.id')
                ->whereIn('pedidos.restaurante_id', $restauranteIds)
                ->where('pedidos.fecha_pedido', '>=', $since)
                ->whereRaw('EXTRACT(HOUR FROM pedidos.fecha_pedido) >= ?', [$slot['start']])
                ->whereRaw('EXTRACT(HOUR FROM pedidos.fecha_pedido) <= ?', [$slot['end']])
                ->select('detalle_pedido.menu_id', DB::raw('SUM(detalle_pedido.cantidad) as total'))
                ->groupBy('detalle_pedido.menu_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $totalInSlot = DB::table('pedidos')
                ->whereIn('restaurante_id', $restauranteIds)
                ->where('fecha_pedido', '>=', $since)
                ->whereRaw('EXTRACT(HOUR FROM fecha_pedido) >= ?', [$slot['start']])
                ->whereRaw('EXTRACT(HOUR FROM fecha_pedido) <= ?', [$slot['end']])
                ->count();

            $platos = [];
            foreach ($orderCounts as $row) {
                if ($totalInSlot > 0) {
                    $menu = $menuIds->has($row->menu_id) ? Menu::find($row->menu_id) : null;
                    $platos[] = [
                        'id' => $row->menu_id,
                        'nombre' => $menu?->nombre ?? 'Desconocido',
                        'probabilidad' => round($row->total / $totalInSlot, 4),
                        'pedidos' => (int) $row->total,
                    ];
                }
            }

            $results[] = [
                'franja' => $slot['label'],
                'platos' => $platos,
            ];
        }

        return $results;
    }

    public function dailyDemandPredictions(array $restauranteIds, int $days = 45): array
    {
        $since = now()->subDays($days)->startOfDay();

        $dailyCounts = DB::table('pedidos')
            ->join('detalle_pedido', 'detalle_pedido.pedido_id', '=', 'pedidos.id')
            ->whereIn('pedidos.restaurante_id', $restauranteIds)
            ->where('pedidos.fecha_pedido', '>=', $since)
            ->select(
                'detalle_pedido.menu_id',
                DB::raw('DATE(pedidos.fecha_pedido) as fecha'),
                DB::raw('SUM(detalle_pedido.cantidad) as total')
            )
            ->groupBy('detalle_pedido.menu_id', DB::raw('DATE(pedidos.fecha_pedido)'))
            ->orderBy('detalle_pedido.menu_id')
            ->orderBy('fecha')
            ->get()
            ->groupBy('menu_id');

        $menuIds = Menu::whereIn('restaurante_id', $restauranteIds)->pluck('nombre', 'id');
        $results = [];

        foreach ($dailyCounts as $menuId => $daysData) {
            $values = $daysData->pluck('total')->map(fn($v) => (int) $v)->values();
            if ($values->count() < 5) continue;

            $sorted = $values->sort()->values();
            $lowThreshold = $sorted->get(floor($sorted->count() * 0.33)) ?? 0;
            $highThreshold = $sorted->get(floor($sorted->count() * 0.66)) ?? ($sorted->last() ?? 0);
            if ($highThreshold == $lowThreshold) $highThreshold = $lowThreshold + 1;

            $states = $values->map(fn($v) => $this->classify($v, $lowThreshold, $highThreshold));

            $transitions = ['alta' => ['alta' => 0, 'media' => 0, 'baja' => 0], 'media' => ['alta' => 0, 'media' => 0, 'baja' => 0], 'baja' => ['alta' => 0, 'media' => 0, 'baja' => 0]];
            for ($i = 0; $i < $states->count() - 1; $i++) {
                $from = $states[$i];
                $to = $states[$i + 1];
                $transitions[$from][$to] = ($transitions[$from][$to] ?? 0) + 1;
            }

            $currentState = $states->last();
            $nextProb = [];
            $totalFrom = array_sum($transitions[$currentState]);
            foreach (['alta', 'media', 'baja'] as $s) {
                $nextProb[$s] = $totalFrom > 0 ? round($transitions[$currentState][$s] / $totalFrom, 2) : 0;
            }

            $predictedState = array_search(max($nextProb), $nextProb);

            $results[] = [
                'id' => $menuId,
                'nombre' => $menuIds[$menuId] ?? 'Desconocido',
                'estado_actual' => $currentState,
                'prediccion' => $predictedState,
                'confianza' => max($nextProb),
                'tendencia' => $nextProb,
            ];
        }

        usort($results, fn($a, $b) => $b['confianza'] <=> $a['confianza']);

        return $results;
    }

    private function classify(int $value, int $low, int $high): string
    {
        if ($value <= $low) return 'baja';
        if ($value >= $high) return 'alta';
        return 'media';
    }
}
