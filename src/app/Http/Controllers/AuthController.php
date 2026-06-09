<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Comensal;
use App\Models\PerfilRestaurante;
use App\Models\Restaurante;
use App\Services\Admin\AuditLogger;

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

        $type = match ($request->input('login_type', 'comensal')) {
            'admin', '1' => 'admin',
            'restaurante', 'usuario', '2' => 'restaurante',
            default => 'comensal',
        };

        if ($type === 'comensal') {
            if (Auth::guard('comensal')->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('inicio');
            }
        } elseif ($type === 'admin') {
            if (Auth::guard('admin')->attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'rol_id' => 1,
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                app(AuditLogger::class)->log('auth', 'login_exitoso', null, null, "Admin {$credentials['email']} inició sesión");
                return redirect()->route('admin.dashboard');
            }
        } else {
            if (Auth::guard('restaurante')->attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'rol_id' => 2,
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('restaurante.dashboard');
            }
        }

        if ($type !== 'comensal') {
            app(AuditLogger::class)->log('auth', 'login_fallido', null, null, "Intento fallido de inicio de sesión: {$credentials['email']}");
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email', 'login_type'));
    }

    public function logoutComensal()
    {
        Auth::guard('comensal')->logout();
        return redirect()->route('home');
    }

    public function logoutAdmin()
    {
        $user = Auth::guard('admin')->user();
        if ($user) {
            app(AuditLogger::class)->log('auth', 'logout', null, null, "Admin {$user->email} cerró sesión");
        }
        Auth::guard('admin')->logout();
        return redirect()->route('home');
    }

    public function logoutRestaurante()
    {
        Auth::guard('restaurante')->logout();
        return redirect()->route('home');
    }

    public function showRegisterComensal()
    {
        return view('auth.register-comensal');
    }

    public function registerComensal(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:comensales,email',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $comensal = Comensal::create([
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
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
            'password' => 'required|string|min:6|confirmed',
            'nit' => 'required|string',
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
            'email_reservas' => 'required|email',
            'instagram' => 'nullable|string',
            'facebook_url' => 'nullable|string',
            'foto_portada' => 'nullable',
        ]);

        $usuario = \App\Models\Usuario::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
            'rol_id' => 2,
        ]);

        PerfilRestaurante::create([
            'usuario_id' => $usuario->id,
            'nit' => $validated['nit'],
        ]);

        $portadaPath = resolve_media_input($request, 'foto_portada', 'portadas');

        Restaurante::create([
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
            'email_reservas' => $validated['email_reservas'],
            'instagram' => $request->input('instagram'),
            'facebook_url' => $request->input('facebook_url'),
            'zona' => $request->input('zona'),
            'foto_portada' => $portadaPath,
            'fecha_registro' => now()->toDateString(),
            'es_principal' => true,
        ]);

        Auth::guard('restaurante')->login($usuario);

        return redirect()->intended(route('restaurante.dashboard'));
    }

    public function updatePerfilComensal(Request $request)
    {
        $comensal = Auth::guard('comensal')->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:comensales,email,'.$comensal->id,
            'telefono' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $comensal->nombre = $validated['nombre'];
        $comensal->apellido_paterno = $validated['apellido_paterno'];
        $comensal->apellido_materno = $validated['apellido_materno'];
        $comensal->email = $validated['email'];
        $comensal->telefono = $validated['telefono'];
        if (!empty($validated['password'])) {
            $comensal->password = Hash::make($validated['password']);
        }
        $comensal->save();

        return back()->with('success', 'Perfil actualizado exitosamente.');
    }
}
