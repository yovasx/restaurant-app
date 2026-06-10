<?php

namespace App\Services\Admin;

use App\Models\Restaurante;
use App\Models\Comensal;
use App\Models\Resena;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Visita;
use App\Models\Auditoria;
use App\Services\AnalyticsRangeService;
use App\Services\TimeSeriesNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class AdminDashboardService
{
    public function generate(?Carbon $from = null, ?Carbon $to = null, ?string $estadoFilter = null): array
    {
        $to = $to ?? now()->endOfDay();
        $from = $from ?? now()->subDays(30)->startOfDay();

        $lenDays = $from->diffInDays($to) ?: 1;
        $prevTo = $from->copy()->subDay()->endOfDay();
        $prevFrom = $from->copy()->subDays($lenDays)->startOfDay();

        $current = $this->reportData($from, $to, $estadoFilter);
        $previous = $this->reportData($prevFrom, $prevTo, $estadoFilter);

        $kpis = $this->kpis($from, $to, $prevFrom, $prevTo, $estadoFilter);
        $alerts = $this->alerts($from);
        $insights = $this->insights($kpis, $alerts);
        $sparklines = $this->sparklines($current['series']);

        return [
            'range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'days' => $lenDays,
                'prev_from' => $prevFrom->toDateString(),
                'prev_to' => $prevTo->toDateString(),
            ],
            'kpis' => $kpis,
            'sparklines' => $sparklines,
            'alerts' => $alerts,
            'insights' => $insights,
            'rankings' => $current['rankings'],
            'series' => $current['series'],
            'latestAudits' => $this->latestAudits(),
        ];
    }

    public function kpis(Carbon $from, Carbon $to, Carbon $prevFrom, Carbon $prevTo, ?string $estadoFilter): array
    {
        $restaurantesActivos = Restaurante::where('estado', 'activo')->count();
        $comensalesActivos = Comensal::where('estado', 'activo')->count();

        $prevRestaurantes = Restaurante::where('estado', 'activo')
            ->where('created_at', '<=', $prevTo)->count();
        $prevComensales = Comensal::where('estado', 'activo')
            ->where('created_at', '<=', $prevTo)->count();

        $visitasActual = Visita::whereBetween('fecha_visita', [$from->toDateString(), $to->toDateString()])->count();
        $visitasPrevia = Visita::whereBetween('fecha_visita', [$prevFrom->toDateString(), $prevTo->toDateString()])->count();

        $resenasQuery = Resena::whereBetween('created_at', [$from, $to]);
        $resenasActual = (clone $resenasQuery)->count();
        $promedioActual = (clone $resenasQuery)->avg('score');

        $resenasPrevQuery = Resena::whereBetween('created_at', [$prevFrom, $prevTo]);
        $resenasPrevia = (clone $resenasPrevQuery)->count();
        $promedioPrevio = (clone $resenasPrevQuery)->avg('score');

        $promocionesActivas = Promocion::where('estado', 'activo')
            ->where(function ($q) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString());
            })->count();

        $prevPromociones = Promocion::where('estado', 'activo')
            ->where('created_at', '<=', $prevTo)
            ->where(function ($q) use ($prevTo) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $prevTo->toDateString());
            })->count();

        $backupDir = storage_path('app/backups/database');
        $backups = is_dir($backupDir) ? count(File::files($backupDir)) : 0;

        $kpiDefs = [
            'restaurantesActivos' => [$restaurantesActivos, $prevRestaurantes],
            'comensalesActivos' => [$comensalesActivos, $prevComensales],
            'visitas' => [$visitasActual, $visitasPrevia],
            'resenas' => [$resenasActual, $resenasPrevia],
            'promedioScore' => [round($promedioActual ?? 0, 1), round($promedioPrevio ?? 0, 1)],
            'promocionesActivas' => [$promocionesActivas, $prevPromociones],
            'backups' => [$backups, $backups],
        ];

        return array_map(fn ($pair) => [
            'current' => $pair[0],
            'previous' => $pair[1],
            'delta' => AnalyticsRangeService::delta($pair[0], $pair[1]),
        ], $kpiDefs);
    }

    private function sparklines(array $series): array
    {
        $takeLast = 7;
        return [
            'visitas' => collect($series['visitas_por_dia'] ?? [])->pluck('total')->take(-$takeLast)->values()->toArray(),
            'resenas' => collect($series['resenas_por_dia'] ?? [])->pluck('total')->take(-$takeLast)->values()->toArray(),
            'altas_restaurantes' => collect($series['altas_restaurantes'] ?? [])->pluck('total')->take(-$takeLast)->values()->toArray(),
            'altas_comensales' => collect($series['altas_comensales'] ?? [])->pluck('total')->take(-$takeLast)->values()->toArray(),
        ];
    }

    private function insights(array $kpis, array $alerts): array
    {
        $lines = [];

        $vDelta = $kpis['visitas']['delta'];
        $rDelta = $kpis['resenas']['delta'];
        if (abs($vDelta) >= 10) {
            $dir = $vDelta > 0 ? 'subieron' : 'cayeron';
            $emoji = $vDelta > 0 ? '▲' : '▼';
            $lines[] = "{$emoji} Las visitas {$dir} un {$vDelta}% vs periodo anterior.";
        }
        if (abs($rDelta) >= 10) {
            $dir = $rDelta > 0 ? 'crecieron' : 'cayeron';
            $emoji = $rDelta > 0 ? '▲' : '▼';
            $lines[] = "{$emoji} Las reseñas {$dir} un {$rDelta}% vs periodo anterior.";
        }

        if (($alerts['sinStock']['total'] ?? 0) > 0) {
            $lines[] = "⚠️ {$alerts['sinStock']['total']} producto(s) activos sin stock.";
        }
        if (($alerts['promocionesVencidas']['total'] ?? 0) > 0) {
            $lines[] = "⚠️ {$alerts['promocionesVencidas']['total']} promoción(es) vencidas siguen activas.";
        }
        if (($alerts['baneados']['total'] ?? 0) > 0) {
            $lines[] = "🚫 {$alerts['baneados']['total']} restaurante(s) baneados.";
        }
        if (($alerts['sinVisitas']['total'] ?? 0) > 0) {
            $lines[] = "🕳️ {$alerts['sinVisitas']['total']} restaurante(s) sin visitas en el periodo.";
        }

        return $lines;
    }

    private function alerts(Carbon $since): array
    {
        $sinStock = Producto::where('activo', true)->where('stock', 0)
            ->select('id', 'nombre', 'stock')
            ->limit(20)->get();

        $promocionesVencidas = Promocion::where('estado', 'activo')
            ->whereDate('fecha_fin', '<', now()->toDateString())
            ->whereNotNull('fecha_fin')
            ->select('id', 'nombre', 'fecha_fin')
            ->limit(20)->get();

        $baneados = Restaurante::where('estado', 'baneado')
            ->select('id', 'nombre')
            ->limit(20)->get();

        $sinVisitasIds = DB::table('restaurantes')
            ->leftJoin('visitas', 'visitas.restaurante_id', '=', 'restaurantes.id')
            ->where('restaurantes.estado', 'activo')
            ->where(function ($q) use ($since) {
                $q->whereNull('visitas.id')
                    ->orWhere('visitas.fecha_visita', '<', $since->toDateString());
            })
            ->select('restaurantes.id', 'restaurantes.nombre')
            ->groupBy('restaurantes.id', 'restaurantes.nombre')
            ->limit(20)->get();

        return [
            'sinStock' => [
                'total' => $sinStock->count(),
                'items' => $sinStock->take(5),
            ],
            'promocionesVencidas' => [
                'total' => $promocionesVencidas->count(),
                'items' => $promocionesVencidas->take(5),
            ],
            'baneados' => [
                'total' => $baneados->count(),
                'items' => $baneados->take(5),
            ],
            'sinVisitas' => [
                'total' => $sinVisitasIds->count(),
                'items' => $sinVisitasIds->take(5),
            ],
        ];
    }

    private function latestAudits(): array
    {
        return Auditoria::with('usuario:id,nombre')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function reportData(Carbon $from, Carbon $to, ?string $estadoFilter): array
    {
        $reportService = app(GlobalReportService::class);
        $filters = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
        if ($estadoFilter) {
            $filters['estado_restaurante'] = $estadoFilter;
        }
        $data = $reportService->generate($filters);

        return [
            'rankings' => [
                'top_visitas' => $data['tables']['top_restaurantes_visitas'] ?? [],
                'top_rating' => $data['tables']['top_restaurantes_rating'] ?? [],
                'peor_rating' => $data['tables']['bottom_restaurantes_rating'] ?? [],
                'distribucion_score' => $data['tables']['resenas_por_score'] ?? [],
                'promociones' => $data['tables']['promociones_por_estado'] ?? [],
                'productos' => $data['tables']['productos_por_estado'] ?? [],
                'productos_sin_stock' => $data['tables']['productos_sin_stock'] ?? [],
            ],
            'series' => [
                'visitas_por_dia' => $this->fillDateGaps(
                    $data['series']['visitas_por_dia'] ?? [],
                    $from, $to
                ),
                'resenas_por_dia' => $this->fillDateGaps(
                    $data['series']['resenas_por_dia'] ?? [],
                    $from, $to
                ),
                'altas_restaurantes' => $this->fillDateGaps(
                    $data['series']['altas_restaurantes'] ?? [],
                    $from, $to
                ),
                'altas_comensales' => $this->fillDateGaps(
                    $data['series']['altas_comensales'] ?? [],
                    $from, $to
                ),
            ],
        ];
    }

    private function fillDateGaps(array $rows, Carbon $from, Carbon $to): array
    {
        $filled = TimeSeriesNormalizer::fillDateGaps($rows, $from, $to);
        return array_map(fn($r) => (object) $r, $filled);
    }
}
