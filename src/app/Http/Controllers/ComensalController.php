<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;

class ComensalController extends Controller
{
    public function index()
    {
        // Paginate default set to reduce memory and avoid returning large collections
        $restaurants = Restaurante::where('estado', 'activo')->paginate(12);
        return view('comensal.inicio', compact('restaurants'));
    }

    public function explorar()
    {
        // Paginate la lista de explorar
        $restaurants = Restaurante::where('estado', 'activo')->paginate(50);
        return view('comensal.explorar', compact('restaurants'));
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');

        // Use a bounding box to reduce rows before calculating Haversine.
        // Approx degrees for ~50km radius: 1 deg lat ~ 111 km
        $radiusKm = 50; // limit search radius
        $latDelta = $radiusKm / 111; // approx
        $lngDelta = abs($radiusKm / (111 * cos(deg2rad($lat))));

        $minLat = $lat - $latDelta;
        $maxLat = $lat + $latDelta;
        $minLng = $lng - $lngDelta;
        $maxLng = $lng + $lngDelta;

        // Use materialized view `restaurantes_stats` to avoid repeated heavy aggregation
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

        // Cache short-term per coordinate (rounded) to avoid repeat heavy queries
        $cacheKey = 'nearby:'.round($lat,4).':'.round($lng,4);
        $cached = cache()->remember($cacheKey, 30, function() use ($restaurants) {
            return $restaurants;
        });

        return response()->json(['data' => $cached]);
    }

    public function show($id)
    {
        $restaurante = Restaurante::findOrFail($id);
        $productos = \App\Models\Producto::where('usuario_id', $restaurante->usuario_id)->where('activo', 1)->get();
        $promociones = \App\Models\Promocion::where('restaurante_id', $restaurante->id)->where('estado', 'activo')->get();

        return view('restaurante.detalle', compact('restaurante', 'productos', 'promociones'));
    }
}
