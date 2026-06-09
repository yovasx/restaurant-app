<?php

namespace App\Services\Admin;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public function log(
        string $modulo,
        string $accion,
        ?string $entidad = null,
        ?string $entidadId = null,
        ?string $descripcion = null,
        ?array $antes = null,
        ?array $despues = null,
        ?array $meta = null,
    ): Auditoria {
        $baseMeta = [
            'ip' => request()->ip(),
            'ruta' => request()->path(),
            'route_name' => request()->route()?->getName(),
            'user_agent' => request()->userAgent(),
        ];

        $meta = array_merge($baseMeta, $meta ?? []);

        $usuarioId = Auth::guard('admin')->id();
        if ($usuarioId !== null && !\App\Models\Usuario::where('id', $usuarioId)->exists()) {
            $usuarioId = null;
        }

        return Auditoria::create([
            'usuario_id' => $usuarioId,
            'modulo' => $modulo,
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $entidadId ? (string) $entidadId : null,
            'descripcion' => $descripcion,
            'antes' => $antes,
            'despues' => $despues,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
