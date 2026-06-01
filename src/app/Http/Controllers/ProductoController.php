<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Restaurante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductoController extends Controller
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
        $sucursal = $usuario->restaurantes()->findOrFail($restauranteId);
        return $sucursal;
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
        $productos = $restauranteId
            ? Producto::where('restaurante_id', $restauranteId)->with('categoria')->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $restauranteId = $this->getRestauranteId();
        if (!$restauranteId) {
            return back()->with('error', 'Debes tener al menos una sucursal configurada.')->withInput();
        }

        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'foto'         => 'nullable',
        ]);

        Producto::create([
            'restaurante_id' => $restauranteId,
            'nombre'       => $validated['nombre'],
            'precio'       => $validated['precio'],
            'stock'        => $validated['stock'],
            'categoria_id' => $validated['categoria_id'],
            'descripcion'  => $validated['descripcion'],
            'foto'         => resolve_media_input($request, 'foto', 'productos'),
        ]);

        return $this->redirectTo($request, 'restaurante.dashboard')
            ->with('success', 'Plato creado con éxito.');
    }

    public function show(Producto $producto)
    {
        return redirect()->route('restaurante.dashboard');
    }

    public function edit(Producto $producto)
    {
        $this->findOwnedSucursalOrFail($producto->restaurante_id);
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $this->findOwnedSucursalOrFail($producto->restaurante_id);
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'foto'         => 'nullable',
        ]);

        if ($request->hasFile('foto') || $request->filled('foto')) {
            $validated['foto'] = resolve_media_input($request, 'foto', 'productos');
        } else {
            unset($validated['foto']);
        }

        $producto->update($validated);

        return $this->redirectTo($request, 'restaurante.dashboard')
            ->with('success', 'Plato actualizado con éxito.');
    }

    public function destroy(Request $request, Producto $producto)
    {
        $this->findOwnedSucursalOrFail($producto->restaurante_id);
        $producto->delete();
        return $this->redirectTo($request, 'restaurante.dashboard')
            ->with('success', 'Plato eliminado correctamente.');
    }

    public function toggle(Producto $producto)
    {
        $this->findOwnedSucursalOrFail($producto->restaurante_id);
        $producto->activo = !$producto->activo;
        $producto->save();
        $status = $producto->activo ? 'habilitado' : 'deshabilitado';
        return back()->with('success', "Plato {$status} correctamente.");
    }
}
