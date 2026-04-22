<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Comensal;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRestaurantes = Usuario::where('rol_id', 2)->count();
        $totalComensales = Comensal::count();
        // Mock data for reviews/revenues for now since we don't have the full models hooked up yet
        return view('admin.dashboard', compact('totalRestaurantes', 'totalComensales'));
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
        $usuario = Usuario::findOrFail($id);
        return view('admin.restaurantes.edit', compact('usuario'));
    }

    public function destroyRestaurante($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->estado = 'inactivo';
        $usuario->save();
        return back()->with('success', 'Restaurante movido a inactivos correctamente.');
    }

    public function updateRestaurante(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:usuarios,email,'.$usuario->id,
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo,baneado',
            'password' => 'nullable|string|min:6'
        ]);

        $usuario->nombre = $validated['nombre'];
        $usuario->email = $validated['email'];
        $usuario->telefono = $validated['telefono'];
        $usuario->estado = $validated['estado'];
        
        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }
        
        $usuario->save();
        return redirect()->route('admin.restaurantes.index')->with('success', 'Registro del local actualizado exitosamente.');
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

    public function destroyComensal($id)
    {
        $comensal = Comensal::findOrFail($id);
        $comensal->estado = 'inactivo';
        $comensal->save();
        return back()->with('success', 'Usuario movido a inactivos correctamente.');
    }

    public function updateComensal(Request $request, $id)
    {
        $comensal = Comensal::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:comensales,email,'.$comensal->id,
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo,baneado',
            'password' => 'nullable|string|min:6'
        ]);

        $comensal->nombre = $validated['nombre'];
        $comensal->email = $validated['email'];
        $comensal->telefono = $validated['telefono'];
        $comensal->estado = $validated['estado'];
        
        if (!empty($validated['password'])) {
            $comensal->password = Hash::make($validated['password']);
        }
        
        $comensal->save();
        return redirect()->route('admin.comensales.index')->with('success', 'Comensal actualizado exitosamente.');
    }

    public function changeRole(Request $request)
    {
        // This method allows an admin to transfer a "Comensal" to a "Restaurante" by moving tables,
        // or change "Restaurante" to "Administrador" by changing rol_id.
        $request->validate([
            'user_type' => 'required|in:comensal,usuario',
            'user_id' => 'required|integer',
            'new_role' => 'required|in:1,2,comensal' // 1: Admin, 2: Restaurante
        ]);

        DB::beginTransaction();
        try {
            if ($request->user_type === 'comensal') {
                $user = Comensal::findOrFail($request->user_id);
                
                if ($request->new_role !== 'comensal') {
                    // Moving Comensal -> Usuario (Restaurante or Admin)
                    $newUser = Usuario::create([
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'password' => $user->password,
                        'telefono' => $user->telefono,
                        'estado' => $user->estado,
                        'rol_id' => $request->new_role
                    ]);
                    $user->delete();
                }
            } else {
                $user = Usuario::findOrFail($request->user_id);
                
                if ($request->new_role === 'comensal') {
                    // Moving Usuario -> Comensal
                    $newComensal = Comensal::create([
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'password' => $user->password,
                        'telefono' => $user->telefono,
                        'estado' => $user->estado
                    ]);
                    $user->delete();
                } else {
                    // Changing between Admin and Restaurante
                    $user->update(['rol_id' => $request->new_role]);
                }
            }
            DB::commit();
            return back()->with('success', 'Rol modificado y transferido de ser necesario.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al cambiar de rol: ' . $e->getMessage()]);
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
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'estado' => 'required|in:activo,inactivo',
            'user_type' => 'required|in:1,2,comensal' // 1:admin, 2:restaurante
        ]);

        if ($validated['user_type'] === 'comensal') {
            // Check uniqueness in comensales
            if (Comensal::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'El correo ya está en uso por otro comensal.'])->withInput();
            }

            Comensal::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'estado' => $validated['estado']
            ]);
        } else {
            // Check uniqueness in usuarios
            if (Usuario::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'El correo ya está en uso por otro usuario/restaurante.'])->withInput();
            }

            Usuario::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'telefono' => $validated['telefono'],
                'estado' => $validated['estado'],
                'rol_id' => $validated['user_type']
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Usuario creado exitosamente.');
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

        Categoria::create([
            'nombre_categoria' => $validated['nombre_categoria'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'activo'
        ]);
        return back()->with('success', 'Categoría creada exitosamente.');
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
            'estado' => 'required|in:activo,inactivo'
        ]);

        $categoria->update($validated);
        return back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroyCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->estado = 'inactivo';
        $categoria->save();
        return back()->with('success', 'Categoría movida a inactivos.');
    }
}
