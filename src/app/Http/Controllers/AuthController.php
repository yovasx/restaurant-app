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
}
