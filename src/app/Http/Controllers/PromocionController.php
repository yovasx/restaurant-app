<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Promocion;
use App\Models\Restaurante;

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

    private function findOwnedSucursalOrFail($restauranteId)
    {
        $usuario = Auth::guard('restaurante')->user();
        return $usuario->restaurantes()->findOrFail($restauranteId);
    }

    private function getRestauranteId()
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursalId = session('restaurante_sucursal_id');
        if ($sucursalId) {
            $sucursal = $usuario->restaurantes()->where('id', $sucursalId)->first();
            if ($sucursal) return $sucursal->id;
        }
        $principal = $usuario->restaurantes()->where('es_principal', true)->first();
        return $principal?->id ?? $usuario->restaurantes()->first()?->id;
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
            return back()->with('error', 'Debes completar tu perfil de restaurante primero.')->withInput();
        }

        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:descuento,2x1,postre,otro',
            'valor'        => 'nullable|numeric|min:0',
            'condicion'    => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'imagen'       => 'nullable',
            'publicidad'   => 'nullable|in:imagen,video',
            'video_url'    => 'nullable|string|max:255',
        ]);

        Promocion::create(array_merge($validated, [
            'restaurante_id' => $restauranteId,
            'estado'   => 'activo',
            'imagen'   => resolve_media_input($request, 'imagen', 'promociones'),
        ]));

        return $this->redirectTo($request, 'restaurante.promociones.index')
            ->with('success', 'Promoción creada exitosamente.');
    }

    public function edit(Promocion $promocion)
    {
        $this->findOwnedSucursalOrFail($promocion->restaurante_id);
        return view('restaurante.promociones.edit', compact('promocion'));
    }

    public function update(Request $request, Promocion $promocion)
    {
        $this->findOwnedSucursalOrFail($promocion->restaurante_id);
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:descuento,2x1,postre,otro',
            'valor'        => 'nullable|numeric|min:0',
            'condicion'    => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'       => 'required|in:activo,inactivo',
            'imagen'       => 'nullable',
            'publicidad'   => 'nullable|in:imagen,video',
            'video_url'    => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('imagen') || $request->filled('imagen')) {
            $validated['imagen'] = resolve_media_input($request, 'imagen', 'promociones');
        } else {
            unset($validated['imagen']);
        }

        $promocion->update($validated);

        return $this->redirectTo($request, 'restaurante.promociones.index')
            ->with('success', 'Promoción actualizada.');
    }

    public function destroy(Request $request, Promocion $promocion)
    {
        $this->findOwnedSucursalOrFail($promocion->restaurante_id);
        $promocion->update(['estado' => 'inactivo']);
        return $this->redirectTo($request, 'restaurante.promociones.index')->with('success', 'Promoción archivada.');
    }

    public function resenas()
    {
        $restauranteId = $this->getRestauranteId();
        $resenas = \App\Models\Resena::whereHas('menu', function ($q) use ($restauranteId) {
                $q->where('restaurante_id', $restauranteId);
            })
            ->with(['comensal', 'menu'])
            ->latest()
            ->paginate(10);

        return view('restaurante.resenas', compact('resenas'));
    }
}
