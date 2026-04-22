<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;

class ComensalController extends Controller
{
    public function index()
    {
        // Show a default set of restaurantes; JS will request nearby when user allows location
        $restaurants = Restaurante::where('estado', 'activo')->take(12)->get();
        return view('comensal.inicio', compact('restaurants'));
    }

    public function explorar()
    {
        // initial set for the explorar view
        $restaurants = Restaurante::where('estado', 'activo')->take(50)->get();
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

        // Haversine formula (distance in kilometers) with avg rating and avg price
        $restaurants = Restaurante::selectRaw(
            "restaurantes.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitud ) ) * cos( radians( longitud ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitud ) ) ) ) AS distance,
            (SELECT AVG(resenas.score) FROM resenas JOIN menus ON menus.id = resenas.menu_id WHERE menus.restaurante_id = restaurantes.id) AS avg_rating,
            (SELECT AVG(productos.precio) FROM productos WHERE productos.usuario_id = restaurantes.usuario_id AND productos.precio IS NOT NULL) AS avg_price",
            [$lat, $lng, $lat]
        )
        ->where('estado', 'activo')
        ->whereNotNull('latitud')
        ->whereNotNull('longitud')
        ->orderBy('distance')
        ->limit(50)
        ->get();

        return response()->json(['data' => $restaurants]);
    }

    public function show($id)
    {
        $restaurante = Restaurante::findOrFail($id);
        $productos = \App\Models\Producto::where('usuario_id', $restaurante->usuario_id)->where('activo', 1)->get();
        $promociones = \App\Models\Promocion::where('restaurante_id', $restaurante->id)->where('estado', 'activo')->get();

        return view('restaurante.detalle', compact('restaurante', 'productos', 'promociones'));
    }
}
