<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookie = $this->resolveCookieName($request);

        if ($cookie !== null) {
            config(['session.cookie' => $cookie]);
        }

        return $next($request);
    }

    private function resolveCookieName(Request $request): ?string
    {
        $role = $this->resolveRole($request);

        return match ($role) {
            'admin'       => 'gastroguia_admin_session',
            'restaurante' => 'gastroguia_restaurante_session',
            'comensal'    => 'gastroguia_comensal_session',
            default       => null,
        };
    }

    private function resolveRole(Request $request): string
    {
        $route = $request->route();

        if ($route && ($name = $route->getName())) {
            if (str_starts_with($name, 'logout.')) {
                return substr($name, 7) ?: 'comensal';
            }

            if ($name === 'login.admin') {
                return 'admin';
            }

            if ($name === 'login') {
                if ($request->isMethod('post')) {
                    return $this->normalizeRole($request->input('login_type', 'comensal'));
                }
                return $this->normalizeRole($request->query('role', 'comensal'));
            }

            if (str_starts_with($name, 'admin.')) {
                return 'admin';
            }

            if ($name !== 'restaurante.show' && str_starts_with($name, 'restaurante.')) {
                return 'restaurante';
            }

            if (str_starts_with($name, 'productos.')) {
                return 'restaurante';
            }

            if (str_starts_with($name, 'register.restaurante')) {
                return 'restaurante';
            }

            if (str_starts_with($name, 'register.')) {
                return 'comensal';
            }

            if (str_starts_with($name, 'comensal.')) {
                return 'comensal';
            }
        }

        $path = $request->path();

        if (str_starts_with($path, 'admin')) {
            return 'admin';
        }

        if (str_starts_with($path, 'register/restaurante')) {
            return 'restaurante';
        }

        return 'comensal';
    }

    private function normalizeRole(string $role): string
    {
        return match ($role) {
            'admin', '1' => 'admin',
            'restaurante', 'usuario', '2' => 'restaurante',
            default => 'comensal',
        };
    }
}
