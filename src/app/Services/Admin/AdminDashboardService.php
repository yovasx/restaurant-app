<?php

namespace App\Services\Admin;

use App\Models\Restaurante;
use App\Models\Comensal;
use App\Models\Resena;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Visita;
use App\Models\Auditoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class AdminDashboardService
{
    public function generate(): array
    {
        $from = now()->subDays(30)->startOfDay();
        $to = now()->endOfDay();

        return [
            'kpis' => $this->kpis($from, $to),
            'alerts' => $this->alerts($from),
            'latestAudits' => $this->latestAudits(),
            'rankings' => $this->rankings($from, $to),
        ];
    }

    private function kpis(Carbon $from, Carbon $to): array
    {
        $restaurantesActivos = Restaurante::where('estado', 'activo')->count();
        $comensalesActivos = Comensal::where('estado', 'activo')->count();

        $visitas30d = Visita::where('fecha_visita', '>=', $from->toDateString())
            ->where('fecha_visita', '<=', $to->toDateString())
            ->count();

        $resenasQuery = Resena::whereBetween('created_at', [$from, $to]);
        $resenas30d = (clone $resenasQuery)->count();
        $promedioScore = (clone $resenasQuery)->avg('score');

        $backupDir = storage_path('app/backups/database');
        $backups = is_dir($backupDir) ? count(File::files($backupDir)) : 0;

        return compact(
            'restaurantesActivos', 'comensalesActivos',
            'visitas30d', 'resenas30d', 'promedioScore', 'backups'
        );
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

    private function rankings(Carbon $from, Carbon $to): array
    {
        $reportService = app(GlobalReportService::class);
        $filters = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
        $data = $reportService->generate($filters);

        return [
            'top_visitas' => $data['tables']['top_restaurantes_visitas'] ?? [],
            'peor_rating' => $data['tables']['bottom_restaurantes_rating'] ?? [],
            'distribucion_score' => $data['tables']['resenas_por_score'] ?? [],
            'promociones' => $data['tables']['promociones_por_estado'] ?? [],
            'productos' => $data['tables']['productos_por_estado'] ?? [],
        ];
    }
}
