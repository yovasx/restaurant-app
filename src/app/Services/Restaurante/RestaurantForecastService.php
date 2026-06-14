<?php

namespace App\Services\Restaurante;

use App\Models\Restaurante;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RestaurantForecastService
{
    const TRAINING_DAYS = 84;
    const MIN_OBS_HIGH = 12;
    const MIN_OBS_MED = 6;
    const SMOOTHING_ALPHA = 1;
    const SMOOTHING_BETA = 2;

    const MARKOV_MIN_OBS = 5;
    const MARKOV_MIN_TRANSITIONS = 4;
    const LAPLACE_ALPHA = 1;

    public function generate(int|array $restauranteId): array
    {
        $ids = is_array($restauranteId) ? $restauranteId : [$restauranteId];
        if (empty($ids)) return $this->empty();

        $since = now()->subDays(static::TRAINING_DAYS)->startOfDay();

        $hourlyRows = $this->queryHourlyOrders($ids, $since);

        if ($hourlyRows->isEmpty()) {
            return $this->empty();
        }

        $businessHours = $this->resolveBusinessHours($ids);
        $denseRows = $this->densifyHourlySeries($hourlyRows, $businessHours, $since);

        $markovProbs = $this->computeMarkovProbabilities($denseRows);
        $fallbackProbs = $this->computeHistoricalProbabilities($hourlyRows);
        $probabilities = $this->mergeProbabilities($markovProbs, $fallbackProbs);

        $probabilities = $this->filterByBusinessHours($probabilities, $businessHours);

        $forecast = $this->buildForecast($probabilities);

        return [
            'heatmap'         => $this->buildHeatmap($probabilities, $businessHours),
            'forecast'        => $forecast,
            'highlights'      => $this->buildHighlights($probabilities),
            'weekly_insights' => $this->buildWeeklyInsights($forecast),
            'probabilities'   => $probabilities,
            'business_hours'  => $businessHours,
        ];
    }

    public function empty(): array
    {
        return [
            'heatmap'         => ['series' => [], 'categories' => []],
            'forecast'        => [],
            'highlights'      => [],
            'weekly_insights' => [],
            'probabilities'   => collect(),
            'business_hours'  => [],
        ];
    }

    public function summary(int|array $restauranteId): array
    {
        $data = $this->generate($restauranteId);
        $hl = $data['highlights'] ?? [];
        $todayDow = (int) now()->format('N') - 1;
        $todayProbs = $data['probabilities']
            ->where('dow', $todayDow)
            ->sortByDesc('pct')
            ->take(3)
            ->values()
            ->toArray();

        return [
            'best_today'         => $hl['best_today'] ?? null,
            'top_peak'           => $hl['top_peak'] ?? null,
            'today_peaks'        => $todayProbs,
            'overall_confidence' => $hl['overall_confidence'] ?? 'baja',
            'has_data'           => $hl['today_has_data'] ?? false,
        ];
    }

    // ==================== MARKOV ENGINE ====================

    private function getHourRangeForDow(int $dow, array $hours): array
    {
        $open  = match ($dow) { 5 => $hours['apertura_sab'], 6 => $hours['apertura_dom'], default => $hours['apertura'] };
        $close = match ($dow) { 5 => $hours['cierre_sab'], 6 => $hours['cierre_dom'], default => $hours['cierre'] };
        [$openH] = array_map('intval', explode(':', $open));
        [$closeH, $closeM] = array_map('intval', explode(':', $close));
        return ['open_hour' => $openH, 'close_hour' => $closeH, 'close_minute' => $closeM];
    }

    private function densifyHourlySeries(Collection $rows, array $businessHours, Carbon $since): Collection
    {
        $since = $since->copy()->startOfDay();
        $today = now()->startOfDay();
        $densified = collect();
        $date = $since->copy();

        while ($date->lte($today)) {
            $dow = (int) $date->format('N') - 1;
            $dateStr = $date->format('Y-m-d');
            $range = $this->getHourRangeForDow($dow, $businessHours);

            $hours = [];
            if ($range['close_hour'] < $range['open_hour'] || ($range['close_hour'] === $range['open_hour'] && $range['close_minute'] === 0)) {
                for ($h = $range['open_hour']; $h < 24; $h++) $hours[] = $h;
                for ($h = 0; $h < $range['close_hour']; $h++) $hours[] = $h;
            } else {
                $last = $range['close_minute'] > 0 ? $range['close_hour'] : $range['close_hour'] - 1;
                for ($h = $range['open_hour']; $h <= $last; $h++) $hours[] = $h;
            }

            $rowsForDate = $rows->filter(fn($r) => $r->date === $dateStr);

            foreach ($hours as $hour) {
                $match = $rowsForDate->first(fn($r) => (int)$r->hour === $hour);
                if ($match) {
                    $densified->push($match);
                } else {
                    $densified->push((object)[
                        'day_of_week' => $dow,
                        'hour'        => $hour,
                        'date'        => $dateStr,
                        'order_count' => 0,
                    ]);
                }
            }

            $date->addDay();
        }

        return $densified;
    }

    private function computeHistoricalProbabilities(Collection $rows): Collection
    {
        $thresholds = $this->computeDayThresholds($rows);
        return $this->computeProbabilities($rows, $thresholds);
    }

    private function buildSlotSeries(Collection $denseRows): Collection
    {
        $series = collect();
        foreach ($denseRows->groupBy(fn($r) => $r->day_of_week . '-' . $r->hour) as $key => $rows) {
            $sorted = $rows->sortBy('date')->values();
            [$dow, $hour] = explode('-', $key);
            $series->push([
                'key'  => $key,
                'dow'  => (int) $dow,
                'hour' => (int) $hour,
                'rows' => $sorted,
            ]);
        }
        return $series;
    }

    private function computeSlotThresholds(array $slotData): array
    {
        $values = $slotData['rows']->pluck('order_count')->map(fn($v) => (int) $v)->sort()->values();
        $n = $values->count();
        if ($n === 0) return ['p33' => 0, 'p66' => 0];

        $p33 = $values->get((int) floor($n * 0.33)) ?? 0;
        $p66 = $values->get((int) floor($n * 0.66)) ?? ($values->last() ?? 0);
        if ($p66 <= $p33) $p66 = $p33 + 1;

        return ['p33' => $p33, 'p66' => $p66];
    }

    private function classifySlotStates(array $slotData, array $thresholds): array
    {
        $states = [];
        foreach ($slotData['rows'] as $r) {
            $v = (int) $r->order_count;
            if ($v <= $thresholds['p33']) {
                $states[] = 'baja';
            } elseif ($v >= $thresholds['p66']) {
                $states[] = 'alta';
            } else {
                $states[] = 'media';
            }
        }
        return $states;
    }

    private function buildTransitionMatrix(array $states): array
    {
        $statesList = ['baja', 'media', 'alta'];
        $matrix = [];
        foreach ($statesList as $s) {
            $matrix[$s] = ['baja' => 0, 'media' => 0, 'alta' => 0];
        }

        for ($i = 0; $i < count($states) - 1; $i++) {
            $from = $states[$i];
            $to = $states[$i + 1];
            $matrix[$from][$to]++;
        }

        return $matrix;
    }

    private function computeNextStateDistribution(array $matrix, string $currentState): array
    {
        $statesList = ['baja', 'media', 'alta'];
        $total = array_sum($matrix[$currentState]);
        $dist = [];

        foreach ($statesList as $s) {
            $count = $matrix[$currentState][$s];
            $dist[$s] = $total > 0
                ? round(($count + static::LAPLACE_ALPHA) / ($total + 3 * static::LAPLACE_ALPHA), 4)
                : round(1 / 3, 4);
        }

        return $dist;
    }

    private function slotHasVariation(array $states): bool
    {
        return count(array_unique($states)) > 1;
    }

    private function computeMarkovProbabilities(Collection $denseRows): Collection
    {
        $dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $results = collect();
        $series = $this->buildSlotSeries($denseRows);

        foreach ($series as $slot) {
            $thresholds = $this->computeSlotThresholds($slot);
            $states = $this->classifySlotStates($slot, $thresholds);
            $matrix = $this->buildTransitionMatrix($states);
            $currentState = $states[count($states) - 1] ?? 'baja';
            $transitions = max(0, count($states) - 1);
            $observations = count($states);
            $hasVariation = $this->slotHasVariation($states);
            $distribution = $this->computeNextStateDistribution($matrix, $currentState);
            $predictedState = array_search(max($distribution), $distribution);
            $probAlta = $distribution['alta'];
            $totalOrders = $slot['rows']->sum('order_count');

            $results->push([
                'dow'                 => $slot['dow'],
                'day_name'            => $dayNames[$slot['dow']] ?? '?',
                'hour'                => $slot['hour'],
                'hour_label'          => sprintf('%02d:00', $slot['hour']),
                'prob'                => $probAlta,
                'pct'                 => round($probAlta * 100, 1),
                'confidence'          => $this->slotConfidenceLabel($observations, $transitions),
                'observations'        => $observations,
                'current_state'       => $currentState,
                'predicted_state'     => $predictedState,
                'state_probabilities' => $distribution,
                'transitions_count'   => $transitions,
                'has_variation'       => $hasVariation,
                'total_orders'        => (int) $totalOrders,
            ]);
        }

        return $results;
    }

    private function slotConfidenceLabel(int $observations, int $transitions): string
    {
        if ($observations >= static::MIN_OBS_HIGH && $transitions >= 6) return 'alta';
        if ($observations >= static::MIN_OBS_MED && $transitions >= 3) return 'media';
        return 'baja';
    }

    private function useMarkovForSlot(array $slot): bool
    {
        return $slot['observations'] >= static::MARKOV_MIN_OBS
            && $slot['transitions_count'] >= static::MARKOV_MIN_TRANSITIONS
            && $slot['has_variation'];
    }

    private function mergeProbabilities(Collection $markov, Collection $fallback): Collection
    {
        $result = collect();
        $fallbackIndexed = [];
        foreach ($fallback as $fb) {
            $key = $fb['dow'] . '-' . $fb['hour'];
            $fallbackIndexed[$key] = $fb;
        }

        foreach ($markov as $m) {
            $key = $m['dow'] . '-' . $m['hour'];
            if ($this->useMarkovForSlot($m)) {
                $result->push($m);
            } elseif (isset($fallbackIndexed[$key])) {
                $result->push($fallbackIndexed[$key]);
            } else {
                $m['confidence'] = 'baja';
                $result->push($m);
            }
        }

        return $result;
    }

    // ==================== QUERY & HISTORICAL (FALLBACK) ====================

    private function queryHourlyOrders(array $ids, Carbon $since): Collection
    {
        return DB::table('pedidos')
            ->whereIn('restaurante_id', $ids)
            ->where('estado', 'completado')
            ->where('fecha_pedido', '>=', $since)
            ->select(
                DB::raw('(EXTRACT(ISODOW FROM fecha_pedido) - 1) as day_of_week'),
                DB::raw('EXTRACT(HOUR FROM fecha_pedido) as hour'),
                DB::raw('fecha_pedido::date as date'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('day_of_week', 'hour', 'date')
            ->orderBy('day_of_week')
            ->orderBy('hour')
            ->orderBy('date')
            ->get();
    }

    private function computeDayThresholds(Collection $rows): array
    {
        $thresholds = [];
        foreach ($rows->groupBy('day_of_week') as $dow => $dayRows) {
            $values = $dayRows->pluck('order_count')->toArray();
            $thresholds[$dow] = empty($values) ? 0 : $this->percentile($values, 75);
        }
        return $thresholds;
    }

    private function computeProbabilities(Collection $rows, array $thresholds): Collection
    {
        $dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $results = collect();

        foreach ($rows->groupBy('day_of_week') as $dow => $dayRows) {
            $threshold = $thresholds[$dow] ?? 0;
            foreach ($dayRows->groupBy('hour') as $hour => $hourRows) {
                $total = $hourRows->count();
                $weightedPeaks = 0.0;
                $weightedTotal = 0.0;
                foreach ($hourRows as $r) {
                    $w = $this->recencyWeight($r->date);
                    $weightedTotal += $w;
                    if ($r->order_count >= $threshold) {
                        $weightedPeaks += $w;
                    }
                }
                $prob = $weightedTotal > 0
                    ? ($weightedPeaks + static::SMOOTHING_ALPHA)
                        / ($weightedTotal + static::SMOOTHING_BETA)
                    : 0;

                $results->push([
                    'dow'         => (int) $dow,
                    'day_name'    => $dayNames[$dow] ?? '?',
                    'hour'        => (int) $hour,
                    'hour_label'  => sprintf('%02d:00', $hour),
                    'prob'        => round($prob, 4),
                    'pct'         => round($prob * 100, 1),
                    'confidence'  => $this->confidenceLabel($total),
                    'observations'=> $total,
                    'threshold'   => $threshold,
                ]);
            }
        }

        return $results;
    }

    private function recencyWeight(string $date): float
    {
        $weeksAgo = (int) floor(now()->startOfDay()->diffInDays(Carbon::parse($date)) / 7);
        if ($weeksAgo <= 2)  return 1.5;
        if ($weeksAgo <= 6)  return 1.0;
        return 0.7;
    }

    private function confidenceLabel(int $n): string
    {
        if ($n >= static::MIN_OBS_HIGH) return 'alta';
        if ($n >= static::MIN_OBS_MED)  return 'media';
        return 'baja';
    }

    private function percentile(array $values, int $p): float
    {
        if (empty($values)) return 0;
        sort($values);
        $idx = ($p / 100) * (count($values) - 1);
        $lower = (int) floor($idx);
        $upper = (int) ceil($idx);
        if ($lower === $upper) return $values[$lower];
        $frac = $idx - $lower;
        return $values[$lower] * (1 - $frac) + $values[$upper] * $frac;
    }

    // ==================== BUSINESS HOURS ====================

    private function resolveBusinessHours(array $ids): array
    {
        $rest = Restaurante::whereIn('id', $ids)->first();
        if (! $rest) {
            return ['apertura'=>'08:00','cierre'=>'23:00','apertura_sab'=>'08:00','cierre_sab'=>'23:00','apertura_dom'=>'09:00','cierre_dom'=>'22:00'];
        }
        return [
            'apertura'    => $rest->horario_apertura ?? '08:00',
            'cierre'      => $rest->horario_cierre ?? '23:00',
            'apertura_sab'=> $rest->hora_apertura_sabado ?? $rest->horario_apertura ?? '08:00',
            'cierre_sab'  => $rest->hora_cierre_sabado ?? $rest->horario_cierre ?? '23:00',
            'apertura_dom'=> $rest->hora_apertura_domingo ?? $rest->horario_apertura ?? '09:00',
            'cierre_dom'  => $rest->hora_cierre_domingo ?? $rest->horario_cierre ?? '22:00',
        ];
    }

    private function filterByBusinessHours(Collection $probs, array $hours): Collection
    {
        return $probs->filter(function ($p) use ($hours) {
            $hour = $p['hour'];
            $dow = $p['dow'];
            $open  = match ($dow) { 5 => $hours['apertura_sab'], 6 => $hours['apertura_dom'], default => $hours['apertura'] };
            $close = match ($dow) { 5 => $hours['cierre_sab'], 6 => $hours['cierre_dom'], default => $hours['cierre'] };

            [$openH] = array_map('intval', explode(':', $open));
            [$closeH, $closeM] = array_map('intval', explode(':', $close));

            if ($closeH < $openH || ($closeH === $openH && $closeM === 0)) {
                return $hour >= $openH || $hour < $closeH;
            }

            $last = $closeM > 0 ? $closeH : $closeH - 1;
            return $hour >= $openH && $hour <= $last;
        })->values();
    }

    // ==================== OUTPUT BUILDERS ====================

    private function buildHeatmap(Collection $probs, array $businessHours): array
    {
        $dayNames = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

        $allHours = collect();
        for ($dow = 0; $dow < 7; $dow++) {
            $open  = match ($dow) { 5 => $businessHours['apertura_sab'], 6 => $businessHours['apertura_dom'], default => $businessHours['apertura'] };
            $close = match ($dow) { 5 => $businessHours['cierre_sab'], 6 => $businessHours['cierre_dom'], default => $businessHours['cierre'] };
            [$openH] = array_map('intval', explode(':', $open));
            [$closeH, $closeM] = array_map('intval', explode(':', $close));

            if ($closeH < $openH || ($closeH === $openH && $closeM === 0)) {
                for ($h = $openH; $h < 24; $h++) $allHours->push($h);
                for ($h = 0; $h < $closeH; $h++) $allHours->push($h);
            } else {
                $last = $closeM > 0 ? $closeH : $closeH - 1;
                for ($h = $openH; $h <= $last; $h++) $allHours->push($h);
            }
        }
        $allHours = $allHours->unique()->sort()->values()->toArray();
        $categories = array_map(fn($h) => sprintf('%02d:00', $h), $allHours);

        $series = [];
        foreach ($dayNames as $dow => $name) {
            $dayProbs = $probs->where('dow', $dow);
            $data = [];
            foreach ($allHours as $hour) {
                $match = $dayProbs->firstWhere('hour', $hour);
                $data[] = $match ? [
                    'x'            => $match['hour_label'],
                    'y'            => $match['prob'],
                    'pct'          => $match['pct'],
                    'confidence'   => $match['confidence'],
                    'observations' => $match['observations'],
                ] : [
                    'x'            => sprintf('%02d:00', $hour),
                    'y'            => 0,
                    'pct'          => 0,
                    'confidence'   => 'baja',
                    'observations' => 0,
                ];
            }
            $series[] = ['name' => $name, 'data' => $data];
        }

        return compact('series', 'categories');
    }

    private function buildForecast(Collection $probs): array
    {
        $today = now()->startOfDay();
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = (clone $today)->addDays($i);
            $dow = (int) $date->format('N') - 1;
            $dayProbs = $probs->where('dow', $dow)->sortBy('hour');
            $hasData = $dayProbs->isNotEmpty();

            $peaks   = $dayProbs->filter(fn($p) => $p['pct'] >= 60)->values()->toArray();
            $mediums = $dayProbs->filter(fn($p) => $p['pct'] >= 30 && $p['pct'] < 60)->values()->toArray();
            $lows    = $dayProbs->filter(fn($p) => $p['pct'] < 30)->values()->toArray();
            $best    = $dayProbs->sortByDesc('pct')->first();
            $obsTotal = $dayProbs->sum('observations');
            $avgObs  = $dayProbs->avg('observations');

            if (!$hasData || !$best) {
                $status        = 'sin_evidencia';
                $statusLabel   = 'Sin evidencia suficiente';
                $summary       = 'Aún no hay datos suficientes para este día.';
                $recommendation = 'Tómalo con cautela y valida con la operación real.';
                $primary       = null;
                $secondary     = null;
                $confidenceLabel = '—';
            } else {
                $bestPct = $best['pct'];
                $bestHour = $best['hour'];

                if ($bestPct >= 60) {
                    $status      = 'alta';
                    $statusLabel = 'Alta afluencia';
                    $summary     = $bestHour >= 5 && $bestHour <= 15
                        ? 'Se espera mayor movimiento al mediodía.'
                        : 'La noche sería la franja más activa.';
                    $recommendation = $bestHour >= 5 && $bestHour <= 15
                        ? 'Refuerza cocina, caja y atención en salón.'
                        : 'Prepara salón, despacho y reposición para la noche.';
                } elseif ($bestPct >= 35) {
                    $status      = 'media';
                    $statusLabel = 'Demanda media';
                    $summary     = 'Se espera una demanda estable con picos moderados.';
                    $recommendation = 'Mantén operación normal con vigilancia en horas clave.';
                } else {
                    $status      = 'baja';
                    $statusLabel = 'Día tranquilo';
                    $summary     = 'Día con afluencia baja o dispersa.';
                    $recommendation = 'Buen momento para activar promociones o combos.';
                }

                $confidenceLabel = $avgObs >= 12 ? 'Alta' : ($avgObs >= 6 ? 'Media' : 'Baja');
                $windows = $this->buildWindows($dayProbs);
                $primary = $windows[0];
                $secondary = $windows[1];
            }

            $days[] = [
                'date'               => $date->format('Y-m-d'),
                'day_name'           => ucfirst($date->locale('es')->isoFormat('dddd D [de] MMMM')),
                'dow'                => $dow,
                'has_data'           => $hasData,
                'status'             => $status,
                'status_label'       => $statusLabel,
                'summary'            => $summary,
                'primary_window'     => $primary,
                'secondary_window'   => $secondary,
                'recommendation'     => $recommendation,
                'observations_total' => $obsTotal,
                'confidence_label'   => $confidenceLabel,
                'peaks'              => $peaks,
                'mediums'            => $mediums,
                'lows'               => $lows,
            ];
        }
        return $days;
    }

    private function buildWindows(Collection $dayProbs): array
    {
        if ($dayProbs->isEmpty()) return [null, null];

        $sorted = $dayProbs->sortBy('hour')->values();

        $groups = [];
        $current = [$sorted->first()];
        for ($i = 1; $i < $sorted->count(); $i++) {
            $prev = $sorted->get($i - 1);
            $curr = $sorted->get($i);
            if ($curr['hour'] === $prev['hour'] + 1) {
                $current[] = $curr;
            } else {
                $groups[] = $current;
                $current = [$curr];
            }
        }
        $groups[] = $current;

        usort($groups, function ($a, $b) {
            return max(array_column($b, 'pct')) <=> max(array_column($a, 'pct'));
        });

        $result = [];
        foreach (array_slice($groups, 0, 2) as $group) {
            $hours = array_column($group, 'hour');
            $maxPct = max(array_column($group, 'pct'));
            $start = min($hours);
            $end = max($hours);
            $label = $start === $end
                ? sprintf('%02d:00 - %02d:00', $start, $start + 1)
                : sprintf('%02d:00 - %02d:00', $start, $end + 1);
            $result[] = ['label' => $label, 'pct' => round($maxPct, 1), 'hours' => $hours];
        }

        return [$result[0] ?? null, $result[1] ?? null];
    }

    private function buildWeeklyInsights(array $forecast): array
    {
        $busiestDay = null;
        $bestPctSeen = 0;
        $nextAlert = null;
        $bestOpportunity = null;
        $lowestPct = 100;

        foreach ($forecast as $day) {
            if (!$day['has_data'] || !$day['primary_window']) continue;

            $wpct = $day['primary_window']['pct'] ?? 0;

            if ($wpct > $bestPctSeen) {
                $bestPctSeen = $wpct;
                $busiestDay = [
                    'day_name' => $day['day_name'],
                    'pct'      => $wpct,
                    'window'   => $day['primary_window']['label'] ?? '',
                ];
            }

            if (!$nextAlert && $day['status'] === 'alta') {
                $nextAlert = [
                    'day_name' => $day['day_name'],
                    'window'   => $day['primary_window']['label'] ?? '',
                    'pct'      => $wpct,
                ];
            }

            if ($day['status'] === 'baja' && $wpct < $lowestPct) {
                $lowestPct = $wpct;
                $bestOpportunity = [
                    'day_name' => $day['day_name'],
                    'window'   => $day['primary_window']['label'] ?? '',
                    'pct'      => $wpct,
                ];
            }
        }

        return compact('busiestDay', 'nextAlert', 'bestOpportunity');
    }

    private function buildHighlights(Collection $probs): array
    {
        $todayDow = (int) now()->format('N') - 1;
        $todayProbs = $probs->where('dow', $todayDow);
        $nowHour = (int) now()->format('G');

        return [
            'best_today'         => ($todayProbs->filter(fn($p) => $p['hour'] >= $nowHour)->sortByDesc('pct')->first()) ?: $todayProbs->sortByDesc('pct')->first(),
            'worst_today'        => $todayProbs->sortBy('pct')->first(),
            'upcoming_hours'     => $todayProbs->filter(fn($p) => $p['hour'] > $nowHour && $p['hour'] <= $nowHour + 4)->sortBy('hour')->values()->toArray(),
            'top_peak'           => $probs->sortByDesc('pct')->first(),
            'today_has_data'     => $todayProbs->isNotEmpty(),
            'overall_confidence' => $probs->isEmpty() ? 'baja' : ($probs->avg('observations') >= 12 ? 'alta' : ($probs->avg('observations') >= 6 ? 'media' : 'baja')),
        ];
    }
}
