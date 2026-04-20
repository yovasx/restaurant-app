<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Comensal;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $type = $request->input('login_type', 'comensal');

        if ($type === 'comensal') {
            if (Auth::guard('comensal')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('inicio');
            }
        } else {
            if (Auth::guard('usuario')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                
                if (Auth::guard('usuario')->user()->rol_id == 1) {
                    return redirect()->route('admin.dashboard');
                }
                
                return redirect()->intended('productos');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('comensal')->check()) {
            Auth::guard('comensal')->logout();
        }
        if (Auth::guard('usuario')->check()) {
            Auth::guard('usuario')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showRegisterComensal()
    {
        return view('auth.register-comensal');
    }

    public function registerComensal(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:comensales,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $comensal = Comensal::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('comensal')->login($comensal);

        return redirect()->intended('inicio');
    }

    public function showRegisterRestaurante()
    {
        return view('auth.register-restaurante');
    }

    public function registerRestaurante(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'email' => 'required|string|email|max:150|unique:usuarios,email',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'nit' => 'nullable|string',
            'zona' => 'nullable|string',
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'hora_apertura_lunes_viernes' => 'nullable|string',
            'hora_cierre_lunes_viernes' => 'nullable|string',
            'hora_apertura_sabado' => 'nullable|string',
            'hora_cierre_sabado' => 'nullable|string',
            'hora_apertura_domingo' => 'nullable|string',
            'hora_cierre_domingo' => 'nullable|string',
            'email_reservas' => 'nullable|email',
            'instagram' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'foto_portada' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120' // Max 5MB
        ]);

        $usuario = \App\Models\Usuario::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
            'rol_id' => 2 // Default Restaurante Role
        ]);

        $portadaPath = null;
        if ($request->hasFile('foto_portada')) {
            $portadaPath = $request->file('foto_portada')->store('portadas', 'public');
        }

        \App\Models\Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => $validated['nombre'],
            'descripcion' => $request->input('descripcion'),
            'direccion' => $request->input('direccion'),
            'telefono' => $validated['telefono'],
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'horario_apertura' => $request->input('hora_apertura_lunes_viernes'),
            'horario_cierre' => $request->input('hora_cierre_lunes_viernes'),
            'hora_apertura_sabado' => $request->input('hora_apertura_sabado'),
            'hora_cierre_sabado' => $request->input('hora_cierre_sabado'),
            'hora_apertura_domingo' => $request->input('hora_apertura_domingo'),
            'hora_cierre_domingo' => $request->input('hora_cierre_domingo'),
            'email_reservas' => $request->input('email_reservas'),
            'instagram' => $request->input('instagram'),
            'facebook_url' => $request->input('facebook_url'),
            'zona' => $request->input('zona'),
            'nit' => $request->input('nit'),
            'foto_portada' => $portadaPath,
            'fecha_registro' => now()->toDateString()
        ]);

        Auth::guard('usuario')->login($usuario);

        return redirect()->intended('productos');
    }

    public function updatePerfilComensal(Request $request)
    {
        $comensal = Auth::guard('comensal')->user();
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:comensales,email,'.$comensal->id,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);
        
        $comensal->nombre = $validated['nombre'];
        $comensal->email = $validated['email'];
        $comensal->telefono = $validated['telefono'];
        if (!empty($validated['password'])) {
            $comensal->password = Hash::make($validated['password']);
        }
        $comensal->save();
        
        return back()->with('success', 'Perfil actualizado exitosamente.');
    }
}
