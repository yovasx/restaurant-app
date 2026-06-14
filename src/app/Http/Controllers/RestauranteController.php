<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Restaurante;
use App\Services\Restaurante\RestaurantDashboardService;

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

    public function dashboard(Request $request)
    {
        $usuario = Auth::guard('restaurante')->user();
        $restaurante = $this->getRestaurante();
        $restauranteId = $restaurante?->id;

        $selectedRange = (int) $request->input('range', 30);
        if (!in_array($selectedRange, [7, 14, 30, 60, 90], true)) {
            $selectedRange = 30;
        }

        $from = now()->subDays($selectedRange)->startOfDay();
        $to = now()->endOfDay();

        $scope = $request->input('scope', 'sucursal_activa');
        $ids = $restauranteId ? [$restauranteId] : [];

        if ($scope === 'todas_mis_sucursales') {
            $ids = $usuario->restaurantes()->pluck('id')->toArray();
        }

        $productos = $restauranteId
            ? Producto::where('restaurante_id', $restauranteId)->with('categoria')->latest()->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $categorias = Categoria::where('estado', 'activo')->orderBy('nombre_categoria')->get();
        $totalProductos = $productos->total();

        $dashboard = count($ids) > 0
            ? app(RestaurantDashboardService::class)->generate($ids, $from, $to)
            : app(RestaurantDashboardService::class)->empty($from, $to);

        return view('restaurante.dashboard', array_merge(compact(
            'usuario', 'restaurante', 'productos', 'categorias', 'totalProductos', 'scope', 'selectedRange'
        ), $dashboard));
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
            'foto_portada'                => 'nullable',
            'logo_url'                    => 'nullable|string|max:255',
            'password'                    => 'nullable|string|min:6',
            'nit'                         => 'required|string',
            'brand_primary_color'         => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_secondary_color'       => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_accent_color'          => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_enabled'               => 'nullable|boolean',
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
        if ($request->hasFile('foto_portada') || $request->filled('foto_portada')) {
            $portadaPath = resolve_media_input($request, 'foto_portada', 'portadas');
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
            'logo_url'             => $validated['logo_url'] ?? $restaurante?->logo_url,
            'brand_primary_color'  => $validated['brand_primary_color'] ?? $restaurante?->brand_primary_color,
            'brand_secondary_color'=> $validated['brand_secondary_color'] ?? $restaurante?->brand_secondary_color,
            'brand_accent_color'   => $validated['brand_accent_color'] ?? $restaurante?->brand_accent_color,
            'brand_enabled'        => $request->has('brand_enabled') ? (bool) $request->brand_enabled : ($restaurante?->brand_enabled ?? true),
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

    private function findOwnedSucursalOrFail($id)
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursal = $usuario->restaurantes()->findOrFail($id);
        return $sucursal;
    }

    private function resolveSucursalActivaOPrincipal()
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursalId = session('restaurante_sucursal_id');
        if ($sucursalId) {
            $sucursal = $usuario->restaurantes()->where('id', $sucursalId)->first();
            if ($sucursal) return $sucursal;
        }
        $principal = $usuario->restaurantes()->where('es_principal', true)->where('estado', 'activo')->first();
        if ($principal) {
            session(['restaurante_sucursal_id' => $principal->id]);
            return $principal;
        }
        $primera = $usuario->restaurantes()->where('estado', 'activo')->first();
        if ($primera) {
            session(['restaurante_sucursal_id' => $primera->id]);
            return $primera;
        }
        return null;
    }

    // ─── Sucursales ─────────────────────────────────────────────────

    public function sucursalesIndex()
    {
        $usuario = Auth::guard('restaurante')->user();
        $sucursales = $usuario->restaurantes()->orderByDesc('es_principal')->orderBy('nombre')->get();
        $sucursalActiva = $this->resolveSucursalActivaOPrincipal();

        return view('restaurante.sucursales.index', compact('sucursales', 'sucursalActiva'));
    }

    public function storeSucursal(Request $request)
    {
        $usuario = Auth::guard('restaurante')->user();

        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string',
            'telefono'              => 'required|string|max:20',
            'email_reservas'        => 'required|email',
            'instagram'             => 'nullable|string',
            'facebook_url'          => 'nullable|string',
            'direccion'             => 'required|string|max:255',
            'zona'                  => 'required|string|max:100',
            'latitud'               => 'nullable|numeric',
            'longitud'              => 'nullable|numeric',
            'horario_apertura'      => 'nullable|string',
            'horario_cierre'        => 'nullable|string',
            'hora_apertura_sabado'  => 'nullable|string',
            'hora_cierre_sabado'    => 'nullable|string',
            'hora_apertura_domingo' => 'nullable|string',
            'hora_cierre_domingo'   => 'nullable|string',
            'foto_portada'          => 'nullable',
        ]);

        $portadaPath = resolve_media_input($request, 'foto_portada', 'portadas');

        $totalActivas = $usuario->restaurantes()->where('estado', 'activo')->count();

        $sucursal = Restaurante::create([
            'usuario_id'            => $usuario->id,
            'nombre'                => $validated['nombre'],
            'descripcion'           => $validated['descripcion'],
            'telefono'              => $validated['telefono'],
            'email_reservas'        => $validated['email_reservas'],
            'instagram'             => $validated['instagram'],
            'facebook_url'          => $validated['facebook_url'],
            'direccion'             => $validated['direccion'],
            'zona'                  => $validated['zona'],
            'latitud'               => $validated['latitud'],
            'longitud'              => $validated['longitud'],
            'horario_apertura'      => $validated['horario_apertura'],
            'horario_cierre'        => $validated['horario_cierre'],
            'hora_apertura_sabado'  => $validated['hora_apertura_sabado'],
            'hora_cierre_sabado'    => $validated['hora_cierre_sabado'],
            'hora_apertura_domingo' => $validated['hora_apertura_domingo'],
            'hora_cierre_domingo'   => $validated['hora_cierre_domingo'],
            'foto_portada'          => $portadaPath,
            'estado'                => 'activo',
            'fecha_registro'        => now()->toDateString(),
            'es_principal'          => $totalActivas === 0,
        ]);

        if ($totalActivas === 0) {
            session(['restaurante_sucursal_id' => $sucursal->id]);
        }

        return redirect()->route('restaurante.sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function updateSucursal(Request $request, Restaurante $restaurante)
    {
        $this->findOwnedSucursalOrFail($restaurante->id);

        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string',
            'telefono'              => 'required|string|max:20',
            'email_reservas'        => 'required|email',
            'instagram'             => 'nullable|string',
            'facebook_url'          => 'nullable|string',
            'direccion'             => 'required|string|max:255',
            'zona'                  => 'required|string|max:100',
            'latitud'               => 'nullable|numeric',
            'longitud'              => 'nullable|numeric',
            'horario_apertura'      => 'nullable|string',
            'horario_cierre'        => 'nullable|string',
            'hora_apertura_sabado'  => 'nullable|string',
            'hora_cierre_sabado'    => 'nullable|string',
            'hora_apertura_domingo' => 'nullable|string',
            'hora_cierre_domingo'   => 'nullable|string',
            'foto_portada'          => 'nullable',
        ]);

        $portadaPath = $restaurante->foto_portada;
        if ($request->hasFile('foto_portada') || $request->filled('foto_portada')) {
            $portadaPath = resolve_media_input($request, 'foto_portada', 'portadas');
        }

        $restaurante->update([
            'nombre'                => $validated['nombre'],
            'descripcion'           => $validated['descripcion'],
            'telefono'              => $validated['telefono'],
            'email_reservas'        => $validated['email_reservas'],
            'instagram'             => $validated['instagram'],
            'facebook_url'          => $validated['facebook_url'],
            'direccion'             => $validated['direccion'],
            'zona'                  => $validated['zona'],
            'latitud'               => $validated['latitud'],
            'longitud'              => $validated['longitud'],
            'horario_apertura'      => $validated['horario_apertura'],
            'horario_cierre'        => $validated['horario_cierre'],
            'hora_apertura_sabado'  => $validated['hora_apertura_sabado'],
            'hora_cierre_sabado'    => $validated['hora_cierre_sabado'],
            'hora_apertura_domingo' => $validated['hora_apertura_domingo'],
            'hora_cierre_domingo'   => $validated['hora_cierre_domingo'],
            'foto_portada'          => $portadaPath,
        ]);

        return redirect()->route('restaurante.sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function archiveSucursal(Restaurante $restaurante)
    {
        $this->findOwnedSucursalOrFail($restaurante->id);
        $usuario = Auth::guard('restaurante')->user();

        $activas = $usuario->restaurantes()->where('estado', 'activo')->count();
        if ($activas <= 1) {
            return back()->with('error', 'No puedes archivar la única sucursal activa.');
        }

        $restaurante->update(['estado' => 'inactivo', 'es_principal' => false]);

        if (session('restaurante_sucursal_id') == $restaurante->id) {
            $newActiva = $usuario->restaurantes()->where('estado', 'activo')->where('es_principal', true)->first()
                ?? $usuario->restaurantes()->where('estado', 'activo')->first();
            if ($newActiva) {
                session(['restaurante_sucursal_id' => $newActiva->id]);
                if ($restaurante->es_principal) {
                    $newActiva->update(['es_principal' => true]);
                }
            }
        }

        return redirect()->route('restaurante.sucursales.index')
            ->with('success', 'Sucursal archivada correctamente.');
    }

    public function setSucursalPrincipal(Restaurante $restaurante)
    {
        $this->findOwnedSucursalOrFail($restaurante->id);
        $usuario = Auth::guard('restaurante')->user();

        $usuario->restaurantes()->where('es_principal', true)->update(['es_principal' => false]);
        $restaurante->update(['es_principal' => true]);

        return redirect()->route('restaurante.sucursales.index')
            ->with('success', 'Sucursal marcada como principal.');
    }

    public function selectSucursal(Restaurante $restaurante)
    {
        $this->findOwnedSucursalOrFail($restaurante->id);

        session(['restaurante_sucursal_id' => $restaurante->id]);

        return back()->with('success', 'Sucursal activa cambiada a: '.$restaurante->nombre);
    }
}
