<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Comensal;
use App\Models\Categoria;
use App\Models\PerfilRestaurante;
use App\Services\Admin\AdminDashboardService;
use App\Services\Admin\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private function redirectTo(Request $request, string $fallbackRoute)
    {
        $redirectTo = $request->input('redirect_to');

        if (is_string($redirectTo) && (str_starts_with($redirectTo, url('/')) || str_starts_with($redirectTo, '/'))) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route($fallbackRoute);
    }

    public function dashboard(Request $request, AdminDashboardService $service)
    {
        $range = (int) $request->query('range', 30);
        $range = in_array($range, [7, 14, 30, 60, 90]) ? $range : 30;
        $estadoFilter = $request->query('estado');

        $from = now()->subDays($range)->startOfDay();
        $to = now()->endOfDay();

        $data = $service->generate($from, $to, $estadoFilter);
        $data['selectedRange'] = $range;
        $data['selectedEstado'] = $estadoFilter;
        $data['pendingRestaurantes'] = Usuario::where('rol_id', 2)
            ->where('estado', 'activo')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        $data['pendingComensales'] = Comensal::where('estado', 'activo')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', $data);
    }

    public function restaurantes(Request $request)
    {
        $tab = $request->query('tab', 'activos');
        if ($tab === 'inactivos') {
            $restaurantes = Usuario::where('rol_id', 2)->where('estado', 'inactivo')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $restaurantes = Usuario::where('rol_id', 2)->where('estado', '!=', 'inactivo')->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('admin.restaurantes.index', compact('restaurantes', 'tab'));
    }

    public function editRestaurante($id)
    {
        $usuario = Usuario::with('perfilRestaurante')->findOrFail($id);
        return view('admin.restaurantes.edit', compact('usuario'));
    }

    public function destroyRestaurante(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $antes = ['estado' => $usuario->estado];
        $usuario->estado = 'inactivo';
        $usuario->save();
        app(\App\Services\Admin\AuditLogger::class)->log('restaurantes', 'archivar', 'Usuario', $id, "Restaurante \"{$usuario->nombre}\u201d movido a inactivos", $antes, ['estado' => 'inactivo']);
        return $this->redirectTo($request, 'admin.restaurantes.index')->with('success', 'Restaurante movido a inactivos correctamente.');
    }

    public function updateRestaurante(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:usuarios,email,'.$usuario->id,
            'telefono' => 'required|string|max:20',
            'estado' => 'required|in:activo,inactivo,baneado',
            'password' => 'nullable|string|min:6',
            'nit' => 'required|string',
        ]);

        $antes = [
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'estado' => $usuario->estado,
            'nit' => $usuario->perfilRestaurante?->nit,
        ];

        $usuario->nombre = $validated['nombre'];
        $usuario->email = $validated['email'];
        $usuario->telefono = $validated['telefono'];
        $usuario->estado = $validated['estado'];

        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

        $perfil = $usuario->perfilRestaurante;
        if ($perfil) {
            $perfil->update(['nit' => $validated['nit']]);
        } else {
            PerfilRestaurante::create([
                'usuario_id' => $usuario->id,
                'nit' => $validated['nit'],
            ]);
        }

        $despues = [
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'estado' => $usuario->estado,
            'nit' => $usuario->perfilRestaurante?->nit,
        ];

        $cambios = array_filter($despues, fn($v, $k) => ($antes[$k] ?? null) !== $v, ARRAY_FILTER_USE_BOTH);
        $desc = "Restaurante #{$id} actualizado";
        if (isset($cambios['estado'])) {
            $desc .= " | estado: {$antes['estado']} -> {$cambios['estado']}";
        }

        app(\App\Services\Admin\AuditLogger::class)->log('restaurantes', 'actualizar', 'Usuario', $id, $desc, $antes, $despues);

        return $this->redirectTo($request, 'admin.restaurantes.index')->with('success', 'Registro del local actualizado exitosamente.');
    }

    public function comensales(Request $request)
    {
        $tab = $request->query('tab', 'activos');
        if ($tab === 'inactivos') {
            $comensales = Comensal::where('estado', 'inactivo')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $comensales = Comensal::where('estado', '!=', 'inactivo')->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('admin.comensales.index', compact('comensales', 'tab'));
    }

    public function editComensal($id)
    {
        $comensal = Comensal::findOrFail($id);
        return view('admin.comensales.edit', compact('comensal'));
    }

    public function destroyComensal(Request $request, $id)
    {
        $comensal = Comensal::findOrFail($id);
        $antes = ['estado' => $comensal->estado];
        $comensal->estado = 'inactivo';
        $comensal->save();
        app(\App\Services\Admin\AuditLogger::class)->log('comensales', 'archivar', 'Comensal', $id, "Comensal \"{$comensal->nombre}\u201d movido a inactivos", $antes, ['estado' => 'inactivo']);
        return $this->redirectTo($request, 'admin.comensales.index')->with('success', 'Usuario movido a inactivos correctamente.');
    }

    public function updateComensal(Request $request, $id)
    {
        $comensal = Comensal::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:comensales,email,'.$comensal->id,
            'telefono' => 'required|string|max:20',
            'estado' => 'required|in:activo,inactivo,baneado',
            'password' => 'nullable|string|min:6',
        ]);

        $antes = [
            'nombre' => $comensal->nombre,
            'apellido_paterno' => $comensal->apellido_paterno,
            'email' => $comensal->email,
            'telefono' => $comensal->telefono,
            'estado' => $comensal->estado,
        ];

        $comensal->nombre = $validated['nombre'];
        $comensal->apellido_paterno = $validated['apellido_paterno'];
        $comensal->apellido_materno = $validated['apellido_materno'];
        $comensal->email = $validated['email'];
        $comensal->telefono = $validated['telefono'];
        $comensal->estado = $validated['estado'];

        if (!empty($validated['password'])) {
            $comensal->password = Hash::make($validated['password']);
        }

        $comensal->save();

        $despues = [
            'nombre' => $comensal->nombre,
            'apellido_paterno' => $comensal->apellido_paterno,
            'email' => $comensal->email,
            'telefono' => $comensal->telefono,
            'estado' => $comensal->estado,
        ];

        $cambios = array_filter($despues, fn($v, $k) => ($antes[$k] ?? null) !== $v, ARRAY_FILTER_USE_BOTH);
        $desc = "Comensal #{$id} actualizado";
        if (isset($cambios['estado'])) {
            $desc .= " | estado: {$antes['estado']} -> {$cambios['estado']}";
        }

        app(\App\Services\Admin\AuditLogger::class)->log('comensales', 'actualizar', 'Comensal', $id, $desc, $antes, $despues);

        return $this->redirectTo($request, 'admin.comensales.index')->with('success', 'Comensal actualizado exitosamente.');
    }

    public function changeRole(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:comensal,usuario',
            'user_id' => 'required|integer',
            'new_role' => 'required|in:1,2,comensal',
        ]);

        $roleMap = ['1' => 'admin', '2' => 'restaurante', 'comensal' => 'comensal'];
        $antes = ['rol' => $request->user_type === 'comensal' ? 'comensal' : ($roleMap[Usuario::find($request->user_id)?->rol_id ?? 'comensal'] ?? 'desconocido')];

        DB::beginTransaction();
        try {
            if ($request->user_type === 'comensal') {
                $user = Comensal::findOrFail($request->user_id);

                if ($request->new_role !== 'comensal') {
                    $newUser = Usuario::create([
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'password' => $user->password,
                        'telefono' => $user->telefono,
                        'estado' => $user->estado,
                        'rol_id' => $request->new_role,
                    ]);
                    if ($request->new_role == 2) {
                        PerfilRestaurante::create([
                            'usuario_id' => $newUser->id,
                            'nit' => 'PENDIENTE',
                        ]);
                    }
                    $user->delete();
                }
            } else {
                $user = Usuario::findOrFail($request->user_id);

                if ($request->new_role === 'comensal') {
                    $newComensal = Comensal::create([
                        'nombre' => $user->nombre,
                        'apellido_paterno' => '',
                        'apellido_materno' => '',
                        'email' => $user->email,
                        'password' => $user->password,
                        'telefono' => $user->telefono,
                        'estado' => $user->estado,
                    ]);
                    $user->delete();
                } else {
                    $user->update(['rol_id' => $request->new_role]);
                }
            }
            DB::commit();

            $despues = ['rol' => $roleMap[$request->new_role] ?? $request->new_role];
            app(\App\Services\Admin\AuditLogger::class)->log('roles', 'cambiar_rol', $request->user_type, $request->user_id, "Rol cambiado: {$antes['rol']} -> {$despues['rol']}", $antes, $despues);

            return $this->redirectTo($request, 'admin.dashboard')->with('success', 'Rol modificado y transferido de ser necesario.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar de rol: ' . $e->getMessage())->withInput();
        }
    }

    public function createUsuario()
    {
        return view('admin.usuarios.create');
    }

    public function storeUsuario(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'estado' => 'required|in:activo,inactivo',
            'user_type' => 'required|in:1,2,comensal',
        ]);

        $tipoMap = ['1' => 'admin', '2' => 'restaurante', 'comensal' => 'comensal'];
        $tipo = $tipoMap[$validated['user_type']] ?? $validated['user_type'];

        if ($validated['user_type'] === 'comensal') {
            $rules = [
                'apellido_paterno' => 'required|string|max:100',
                'apellido_materno' => 'required|string|max:100',
            ];
            $extra = $request->validate($rules);

            if (Comensal::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'El correo ya está en uso por otro comensal.'])->withInput();
            }

            $comensal = Comensal::create([
                'nombre' => $validated['nombre'],
                'apellido_paterno' => $extra['apellido_paterno'],
                'apellido_materno' => $extra['apellido_materno'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'estado' => $validated['estado'],
            ]);
            app(\App\Services\Admin\AuditLogger::class)->log('usuarios', 'crear', 'Comensal', $comensal->id, "Comensal \"{$comensal->nombre}\u201d creado", null, ['tipo' => $tipo, 'email' => $validated['email']]);
        } else {
            if (Usuario::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'El correo ya está en uso por otro usuario/restaurante.'])->withInput();
            }

            $usuario = Usuario::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'estado' => $validated['estado'],
                'rol_id' => $validated['user_type'],
            ]);

            if ($validated['user_type'] == 2) {
                $nitData = $request->validate(['nit' => 'required|string']);
                PerfilRestaurante::create([
                    'usuario_id' => $usuario->id,
                    'nit' => $nitData['nit'],
                ]);
            }
            app(\App\Services\Admin\AuditLogger::class)->log('usuarios', 'crear', 'Usuario', $usuario->id, "Usuario \"{$usuario->nombre}\u201d creado como {$tipo}", null, ['tipo' => $tipo, 'email' => $validated['email']]);
        }

        return $this->redirectTo($request, 'admin.dashboard')->with('success', 'Usuario creado exitosamente.');
    }

    public function categorias(Request $request)
    {
        $tab = $request->query('tab', 'activos');
        if ($tab === 'inactivos') {
            $categorias = Categoria::where('estado', 'inactivo')->orderBy('nombre_categoria')->paginate(10);
        } else {
            $categorias = Categoria::where('estado', '!=', 'inactivo')->orderBy('nombre_categoria')->paginate(10);
        }
        return view('admin.categorias.index', compact('categorias', 'tab'));
    }

    public function storeCategoria(Request $request)
    {
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = Categoria::create([
            'nombre_categoria' => $validated['nombre_categoria'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'activo',
        ]);
        app(\App\Services\Admin\AuditLogger::class)->log('categorias', 'crear', 'Categoria', $categoria->id, "Categor\u00eda \"{$categoria->nombre_categoria}\u201d creada", null, $validated);
        return $this->redirectTo($request, 'admin.categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function editCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function updateCategoria(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $antes = ['nombre_categoria' => $categoria->nombre_categoria, 'descripcion' => $categoria->descripcion, 'estado' => $categoria->estado];
        $categoria->update($validated);
        app(\App\Services\Admin\AuditLogger::class)->log('categorias', 'actualizar', 'Categoria', $id, "Categor\u00eda #{$id} actualizada", $antes, $validated);
        return $this->redirectTo($request, 'admin.categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroyCategoria(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $antes = ['estado' => $categoria->estado];
        $categoria->estado = 'inactivo';
        $categoria->save();
        app(\App\Services\Admin\AuditLogger::class)->log('categorias', 'archivar', 'Categoria', $id, "Categor\u00eda \"{$categoria->nombre_categoria}\u201d movida a inactivos", $antes, ['estado' => 'inactivo']);
        return $this->redirectTo($request, 'admin.categorias.index')->with('success', 'Categoría movida a inactivos.');
    }
}
