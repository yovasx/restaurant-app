<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Restaurante\RestaurantForecastService;

class RestaurantForecastController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = $this->getRestaurante();
        $restauranteId = $restaurante?->id;

        $scope = $request->input('scope', 'sucursal_activa');
        $ids = $restauranteId ? [$restauranteId] : [];

        if ($scope === 'todas_mis_sucursales') {
            $ids = $usuario->restaurantes()->pluck('id')->toArray();
        }

        $forecast = count($ids) > 0
            ? app(RestaurantForecastService::class)->generate($ids)
            : app(RestaurantForecastService::class)->empty();

        return view('restaurante.pronosticos.index', array_merge(
            compact('usuario', 'restaurante', 'scope'),
            $forecast
        ));
    }

    private function getRestaurante()
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursalId = session('restaurante_sucursal_id');
        if ($sucursalId) {
            $sucursal = $usuario->restaurantes()->where('id', $sucursalId)->first();
            if ($sucursal) return $sucursal;
        }
        return $usuario->restaurantes()->where('es_principal', true)->first()
            ?? $usuario->restaurantes()->first();
    }
}
