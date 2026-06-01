<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;

class ComensalController extends Controller
{
    public function index()
    {
        $restaurants = Restaurante::where('estado', 'activo')->paginate(12);
        return view('comensal.inicio', compact('restaurants'));
    }

    public function explorar()
    {
        $restaurants = Restaurante::where('estado', 'activo')->paginate(50);
        return view('comensal.explorar', compact('restaurants'));
    }

    public function nearby(Request $request)
    {
        if (! $request->boolean('ajax')) {
            return redirect()->route('home');
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');

        $radiusKm = 50;
        $latDelta = $radiusKm / 111;
        $lngDelta = abs($radiusKm / (111 * cos(deg2rad($lat))));

        $minLat = $lat - $latDelta;
        $maxLat = $lat + $latDelta;
        $minLng = $lng - $lngDelta;
        $maxLng = $lng + $lngDelta;

        $restaurants = Restaurante::selectRaw(
            "restaurantes.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitud ) ) * cos( radians( longitud ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitud ) ) ) ) AS distance, stats.avg_rating AS avg_rating, stats.avg_price AS avg_price",
            [$lat, $lng, $lat]
        )
        ->leftJoin('restaurantes_stats as stats', 'stats.restaurante_id', '=', 'restaurantes.id')
        ->where('estado', 'activo')
        ->whereBetween('latitud', [$minLat, $maxLat])
        ->whereBetween('longitud', [$minLng, $maxLng])
        ->whereNotNull('latitud')
        ->whereNotNull('longitud')
        ->orderBy('distance')
        ->limit(50)
        ->get();

        $cacheKey = 'nearby:'.round($lat,4).':'.round($lng,4);
        $cached = cache()->remember($cacheKey, 30, function() use ($restaurants) {
            return $restaurants;
        });

        return response()->json(['data' => $cached]);
    }

    public function show($id)
    {
        $restaurante = Restaurante::findOrFail($id);
        $productos = \App\Models\Producto::where('restaurante_id', $restaurante->id)->where('activo', 1)->get();
        $promociones = \App\Models\Promocion::where('restaurante_id', $restaurante->id)->where('estado', 'activo')->get();

        return view('restaurante.detalle', compact('restaurante', 'productos', 'promociones'));
    }
}
