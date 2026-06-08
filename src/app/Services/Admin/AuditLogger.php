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

        return Auditoria::create([
            'usuario_id' => Auth::guard('admin')->id(),
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
