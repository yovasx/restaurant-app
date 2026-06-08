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
    public function generate(int $restauranteId): array
    {
        $restaurante = Restaurante::find($restauranteId);

        if (!$restaurante) {
            return $this->empty();
        }

        $from = now()->subDays(30)->startOfDay();
        $to = now()->endOfDay();

        return [
            'kpis' => $this->kpis($restauranteId, $from, $to),
            'alerts' => $this->alerts($restaurante, $from),
            'latestReviews' => $this->latestReviews($restauranteId),
            'scoreDistribution' => $this->scoreDistribution($restauranteId, $from, $to),
            'promoSummary' => $this->promoSummary($restauranteId),
            'branchSummary' => $restaurante,
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
        ];
    }

    private function kpis(int $id, Carbon $from, Carbon $to): array
    {
        $productosActivos = Producto::where('restaurante_id', $id)
            ->where('activo', true)->count();

        $productosSinStock = Producto::where('restaurante_id', $id)
            ->where('activo', true)->where('stock', 0)->count();

        $hoy = now()->toDateString();

        $promocionesActivas = Promocion::where('restaurante_id', $id)
            ->where('estado', 'activo')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy);
            })
            ->count();

        $visitas = Visita::where('restaurante_id', $id)
            ->where('fecha_visita', '>=', $from->toDateString())
            ->where('fecha_visita', '<=', $to->toDateString())
            ->count();

        $menuIds = Menu::where('restaurante_id', $id)->pluck('id');

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

    private function alerts(Restaurante $r, Carbon $since): array
    {
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

    private function latestReviews(int $restauranteId): array
    {
        $menuIds = Menu::where('restaurante_id', $restauranteId)->pluck('id');

        return Resena::whereIn('menu_id', $menuIds)
            ->with(['comensal:id,nombre,apellido_paterno', 'menu:id,nombre'])
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function scoreDistribution(int $restauranteId, Carbon $from, Carbon $to): array
    {
        $menuIds = Menu::where('restaurante_id', $restauranteId)->pluck('id');

        $scores = Resena::whereIn('menu_id', $menuIds)
            ->whereBetween('created_at', [$from, $to])
            ->select('score', DB::raw('count(*) as total'))
            ->groupBy('score')
            ->orderBy('score')
            ->pluck('total', 'score');

        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $result[$i] = $scores[$i] ?? 0;
        }
        return $result;
    }

    private function promoSummary(int $restauranteId): array
    {
        $hoy = now()->toDateString();

        $activas = Promocion::where('restaurante_id', $restauranteId)
            ->where('estado', 'activo')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
            })
            ->count();

        $vencidas = Promocion::where('restaurante_id', $restauranteId)
            ->where('estado', 'activo')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', $hoy)
            ->count();

        $inactivas = Promocion::where('restaurante_id', $restauranteId)
            ->where('estado', 'inactivo')
            ->count();

        return compact('activas', 'vencidas', 'inactivas');
    }
}
