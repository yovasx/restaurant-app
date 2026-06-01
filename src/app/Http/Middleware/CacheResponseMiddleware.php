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
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        if (
            auth()->guard('comensal')->check() ||
            auth()->guard('admin')->check() ||
            auth()->guard('restaurante')->check()
        ) {
            return $next($request);
        }

        $key = 'page_cache_guest_' . md5($request->fullUrl());

        if (Cache::has($key)) {
            $cached = Cache::get($key);
            return response($cached['content'], $cached['status'], $cached['headers']);
        }

        $response = $next($request);

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
