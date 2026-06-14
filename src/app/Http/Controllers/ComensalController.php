<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Menu;
use App\Models\Promocion;
use App\Models\Restaurante;
use App\Models\Resena;
use App\Models\Favorito;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ComensalController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::where('estado', 'activo')->get();

        $query = Restaurante::select('restaurantes.*', 'stats.avg_rating', 'stats.avg_price')
            ->leftJoin('restaurantes_stats as stats', 'stats.restaurante_id', '=', 'restaurantes.id')
            ->where('restaurantes.estado', 'activo')
            ->with('categorias');

        if ($request->filled('categoria') && $request->categoria !== 'todos') {
            $query->whereHas('categorias', function ($q) use ($request) {
                $q->where('categorias.id', $request->categoria);
            });
        }

        $restaurants = $query->paginate(12);

        $dashboard = [];
        if (Auth::guard('comensal')->check()) {
            $user = Auth::guard('comensal')->user();
            $dashboard = [
                'totalFavoritos' => Favorito::where('comensal_id', $user->id)->count(),
                'totalResenas' => Resena::where('comensal_id', $user->id)->count(),
                'totalVisitas' => Visita::where('comensal_id', $user->id)->count(),
                'recentFavoritos' => Favorito::where('comensal_id', $user->id)
                    ->with('restaurante')
                    ->latest()->limit(5)->get(),
                'recentResenas' => Resena::where('comensal_id', $user->id)
                    ->with('menu:id,nombre')
                    ->latest()->limit(5)->get(),
            ];
        }

        return view('comensal.inicio', array_merge(compact('restaurants', 'categorias'), $dashboard));
    }

    public function explorar(Request $request)
    {
        $categorias = Categoria::where('estado', 'activo')->get();

        $query = Restaurante::select('restaurantes.*', 'stats.avg_rating', 'stats.avg_price')
            ->leftJoin('restaurantes_stats as stats', 'stats.restaurante_id', '=', 'restaurantes.id')
            ->where('restaurantes.estado', 'activo')
            ->with('categorias');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('restaurantes.nombre', 'ilike', "%{$q}%")
                    ->orWhere('restaurantes.descripcion', 'ilike', "%{$q}%")
                    ->orWhere('restaurantes.zona', 'ilike', "%{$q}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->whereHas('categorias', function ($sub) use ($request) {
                $sub->where('categorias.id', $request->categoria);
            });
        }

        $restaurants = $query->get();

        return view('comensal.explorar', compact('restaurants', 'categorias'));
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

        $cacheKey = 'nearby:'.round($lat,4).':'.round($lng,4);

        $data = Cache::remember($cacheKey, 30, function() use ($lat, $lng) {
            $radiusKm = 50;
            $latDelta = $radiusKm / 111;
            $lngDelta = abs($radiusKm / (111 * cos(deg2rad($lat))));

            $minLat = $lat - $latDelta;
            $maxLat = $lat + $latDelta;
            $minLng = $lng - $lngDelta;
            $maxLng = $lng + $lngDelta;

            return Restaurante::selectRaw(
                "restaurantes.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitud ) ) * cos( radians( longitud ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitud ) ) ) ) AS distance, stats.avg_rating AS avg_rating, stats.avg_price AS avg_price",
                [$lat, $lng, $lat]
            )
            ->leftJoin('restaurantes_stats as stats', 'stats.restaurante_id', '=', 'restaurantes.id')
            ->with('categorias')
            ->where('estado', 'activo')
            ->whereBetween('latitud', [$minLat, $maxLat])
            ->whereBetween('longitud', [$minLng, $maxLng])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('distance')
            ->limit(50)
            ->get();
        });

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $restaurante = Restaurante::with(['resenas.comensal', 'resenas.menu'])->findOrFail($id);
        $menus = Menu::where('restaurante_id', $restaurante->id)->where('estado', 'activo')->orderBy('orden')->get();
        $promociones = Promocion::where('restaurante_id', $restaurante->id)->where('estado', 'activo')->get();

        $promedio = $restaurante->resenas->avg('score');
        $totalResenas = $restaurante->resenas->count();
        $resenasRecientes = $restaurante->resenas->sortByDesc('created_at')->take(5);

        $miResena = null;
        $miResenaPlatos = collect();
        if (Auth::guard('comensal')->check()) {
            $comensalId = Auth::guard('comensal')->user()->id;
            $miResena = $restaurante->resenas
                ->where('comensal_id', $comensalId)
                ->whereNotNull('restaurante_id')
                ->first();

            $miResenaPlatos = Resena::where('comensal_id', $comensalId)
                ->whereIn('menu_id', $menus->pluck('id'))
                ->get()
                ->keyBy('menu_id');
        }

        return view('restaurante.detalle', compact(
            'restaurante', 'menus', 'promociones',
            'promedio', 'totalResenas', 'resenasRecientes', 'miResena', 'miResenaPlatos'
        ));
    }

    public function saveResena(Request $request, $id)
    {
        $restaurante = Restaurante::findOrFail($id);

        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
            'menu_id' => 'nullable|integer|exists:menus,id',
        ]);

        $comensalId = Auth::guard('comensal')->id();

        if ($request->filled('menu_id')) {
            $menu = Menu::where('id', $request->menu_id)
                ->where('restaurante_id', $restaurante->id)
                ->firstOrFail();

            $resena = Resena::where('comensal_id', $comensalId)
                ->where('menu_id', $menu->id)
                ->first();

            if ($resena) {
                $resena->update([
                    'score' => $validated['score'],
                    'comentario' => $validated['comentario'] ?? null,
                ]);
            } else {
                Resena::create([
                    'comensal_id' => $comensalId,
                    'restaurante_id' => $restaurante->id,
                    'menu_id' => $menu->id,
                    'score' => $validated['score'],
                    'comentario' => $validated['comentario'] ?? null,
                ]);
            }
        } else {
            $resena = Resena::where('comensal_id', $comensalId)
                ->where('restaurante_id', $restaurante->id)
                ->first();

            if ($resena) {
                $resena->update([
                    'score' => $validated['score'],
                    'comentario' => $validated['comentario'] ?? null,
                ]);
            } else {
                Resena::create([
                    'comensal_id' => $comensalId,
                    'restaurante_id' => $restaurante->id,
                    'score' => $validated['score'],
                    'comentario' => $validated['comentario'] ?? null,
                ]);
            }
        }

        return redirect()->route('restaurante.show', $restaurante->id)
            ->with('success', 'Tu reseña ha sido guardada.');
    }
}
