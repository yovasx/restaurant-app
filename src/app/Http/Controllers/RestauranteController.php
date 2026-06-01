<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Restaurante;

class RestauranteController extends Controller
{
    private function getRestaurante()
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursalId = session('restaurante_sucursal_id');
        if ($sucursalId) {
            $sucursal = $usuario->restaurantes()->where('id', $sucursalId)->first();
            if ($sucursal) {
                return $sucursal;
            }
        }
        return $usuario->restaurantes()->where('es_principal', true)->first()
            ?? $usuario->restaurantes()->first();
    }

    public function dashboard()
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = $this->getRestaurante();
        $restauranteId = $restaurante?->id;

        $productos = $restauranteId
            ? Producto::where('restaurante_id', $restauranteId)->with('categoria')->latest()->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        $totalProductos = $productos->total();

        return view('restaurante.dashboard', compact('usuario', 'restaurante', 'productos', 'categorias', 'totalProductos'));
    }

    public function configuracion()
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = $this->getRestaurante();
        $perfil = $usuario->perfilRestaurante;
        return view('restaurante.configuracion', compact('usuario', 'restaurante', 'perfil'));
    }

    public function updateConfiguracion(Request $request)
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = $this->getRestaurante();

        $validated = $request->validate([
            'nombre'                      => 'required|string|max:150',
            'descripcion'                 => 'nullable|string',
            'telefono'                    => 'required|string|max:20',
            'direccion'                   => 'nullable|string|max:255',
            'zona'                        => 'nullable|string|max:100',
            'latitud'                     => 'nullable|numeric',
            'longitud'                    => 'nullable|numeric',
            'horario_apertura'            => 'nullable|string',
            'horario_cierre'              => 'nullable|string',
            'hora_apertura_sabado'        => 'nullable|string',
            'hora_cierre_sabado'          => 'nullable|string',
            'hora_apertura_domingo'       => 'nullable|string',
            'hora_cierre_domingo'         => 'nullable|string',
            'email_reservas'              => 'required|email',
            'instagram'                   => 'nullable|string',
            'facebook_url'                => 'nullable|string',
            'foto_portada'                => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'password'                    => 'nullable|string|min:6',
            'nit'                         => 'required|string',
        ]);

        if ($request->email && $request->email !== $usuario->email) {
            $request->validate(['email' => 'email|unique:usuarios,email,'.$usuario->id]);
            $usuario->email = $request->email;
        }
        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }
        $usuario->save();

        $perfil = $usuario->perfilRestaurante;
        if ($perfil) {
            $perfil->update(['nit' => $validated['nit']]);
        } else {
            \App\Models\PerfilRestaurante::create([
                'usuario_id' => $usuario->id,
                'nit' => $validated['nit'],
            ]);
        }

        $portadaPath = $restaurante ? $restaurante->foto_portada : null;
        if ($request->hasFile('foto_portada')) {
            $portadaPath = $request->file('foto_portada')->store('portadas', 'public');
        }

        $data = [
            'nombre'               => $validated['nombre'],
            'descripcion'          => $validated['descripcion'],
            'telefono'             => $validated['telefono'],
            'direccion'            => $validated['direccion'],
            'zona'                 => $validated['zona'],
            'latitud'              => $validated['latitud'],
            'longitud'             => $validated['longitud'],
            'horario_apertura'     => $validated['horario_apertura'],
            'horario_cierre'       => $validated['horario_cierre'],
            'hora_apertura_sabado' => $validated['hora_apertura_sabado'],
            'hora_cierre_sabado'   => $validated['hora_cierre_sabado'],
            'hora_apertura_domingo'=> $validated['hora_apertura_domingo'],
            'hora_cierre_domingo'  => $validated['hora_cierre_domingo'],
            'email_reservas'       => $validated['email_reservas'],
            'instagram'            => $validated['instagram'],
            'facebook_url'         => $validated['facebook_url'],
            'foto_portada'         => $portadaPath,
        ];

        if ($restaurante) {
            $restaurante->update($data);
        } else {
            Restaurante::create(array_merge($data, [
                'usuario_id'     => $usuario->id,
                'estado'         => 'activo',
                'fecha_registro' => now()->toDateString(),
                'es_principal'   => true,
            ]));
        }

        return back()->with('success', 'Configuración guardada exitosamente.');
    }
}
