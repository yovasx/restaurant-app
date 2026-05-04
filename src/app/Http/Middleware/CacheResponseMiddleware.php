<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a peticiones GET (no a POST, PUT, DELETE para no interferir con acciones)
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        // Generar una clave de caché única por usuario (o 'guest') y por URL
        $userId = auth()->id() ?? auth('usuario')->id() ?? 'guest';
        $key = 'page_cache_' . $userId . '_' . md5($request->fullUrl());

        // Si existe en caché, devolver la respuesta cacheada inmediatamente (super rápido)
        if (Cache::has($key)) {
            $cached = Cache::get($key);
            return response($cached['content'], $cached['status'], $cached['headers']);
        }

        // Si no existe, procesar la petición normalmente
        $response = $next($request);

        // Si fue exitosa (código 200), guardamos el resultado en caché por 30 segundos
        // Suficiente para que no se sienta lento al navegar, pero sin mantener datos viejos por horas
        if ($response->isSuccessful()) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ], 30);
        }

        return $response;
    }
}
