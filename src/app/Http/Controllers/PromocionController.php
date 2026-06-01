<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Promocion;
use App\Models\Restaurante;
use App\Models\Resena;
use App\Models\Menu;

class PromocionController extends Controller
{
    private function redirectTo(Request $request, string $fallbackRoute)
    {
        $redirectTo = $request->input('redirect_to');

        if (is_string($redirectTo) && (str_starts_with($redirectTo, url('/')) || str_starts_with($redirectTo, '/'))) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route($fallbackRoute);
    }

    private function getRestauranteId()
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = Restaurante::where('usuario_id', $usuario->id)->first();
        return $restaurante ? $restaurante->id : null;
    }

    public function index()
    {
        $restauranteId = $this->getRestauranteId();
        $promociones = $restauranteId
            ? Promocion::where('restaurante_id', $restauranteId)->latest()->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        return view('restaurante.promociones.index', compact('promociones'));
    }

    public function create()
    {
        return view('restaurante.promociones.create');
    }

    public function store(Request $request)
    {
        $restauranteId = $this->getRestauranteId();
        if (!$restauranteId) {
            return back()->withErrors(['error' => 'Debes completar tu perfil de restaurante primero.'])->withInput();
        }

        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:descuento,2x1,postre,otro',
            'valor'        => 'nullable|numeric|min:0',
            'condicion'    => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'imagen'       => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('promociones', 'public');
        }

        Promocion::create(array_merge($validated, [
            'restaurante_id' => $restauranteId,
            'estado'  => 'activo',
            'imagen'  => $imagenPath,
        ]));

        return $this->redirectTo($request, 'restaurante.promociones.index')
            ->with('success', 'Promoción creada exitosamente.');
    }

    public function edit(Promocion $promocion)
    {
        return view('restaurante.promociones.edit', compact('promocion'));
    }

    public function update(Request $request, Promocion $promocion)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:descuento,2x1,postre,otro',
            'valor'        => 'nullable|numeric|min:0',
            'condicion'    => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'       => 'required|in:activo,inactivo',
            'imagen'       => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('promociones', 'public');
        } else {
            unset($validated['imagen']);
        }

        $promocion->update($validated);

        return $this->redirectTo($request, 'restaurante.promociones.index')
            ->with('success', 'Promoción actualizada.');
    }

    public function destroy(Request $request, Promocion $promocion)
    {
        $promocion->update(['estado' => 'inactivo']);
        return $this->redirectTo($request, 'restaurante.promociones.index')->with('success', 'Promoción archivada.');
    }

    public function resenas()
    {
        $restauranteId = $this->getRestauranteId();
        // Get reviews for menus linked to this restaurant
        $resenas = \App\Models\Resena::whereHas('menu', function ($q) use ($restauranteId) {
                $q->where('restaurante_id', $restauranteId);
            })
            ->with(['comensal', 'menu'])
            ->latest()
            ->paginate(10);

        return view('restaurante.resenas', compact('resenas'));
    }
}
