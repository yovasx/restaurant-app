<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $usuario = Auth::guard('usuario')->user();
        $productos = Producto::where('usuario_id', $usuario->id)
            ->with('categoria')
            ->paginate(10);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'foto'         => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('productos', 'public');
        }

        Producto::create([
            'nombre'       => $validated['nombre'],
            'precio'       => $validated['precio'],
            'stock'        => $validated['stock'],
            'categoria_id' => $validated['categoria_id'],
            'descripcion'  => $validated['descripcion'],
            'foto'         => $fotoPath,
            'usuario_id'   => Auth::guard('usuario')->id(),
        ]);

        return redirect()->route('restaurante.dashboard')
            ->with('success', 'Plato creado con éxito.');
    }

    public function show(Producto $producto)
    {
        return redirect()->route('restaurante.dashboard');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'foto'         => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('productos', 'public');
        } else {
            unset($validated['foto']); // keep existing if no new upload
        }

        $producto->update($validated);

        return redirect()->route('restaurante.dashboard')
            ->with('success', 'Plato actualizado con éxito.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('restaurante.dashboard')
            ->with('success', 'Plato eliminado correctamente.');
    }

    public function toggle(Producto $producto)
    {
        $producto->activo = !$producto->activo;
        $producto->save();
        $status = $producto->activo ? 'habilitado' : 'deshabilitado';
        return back()->with('success', "Plato {$status} correctamente.");
    }
}
