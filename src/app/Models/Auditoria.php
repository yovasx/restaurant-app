<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'modulo',
        'accion',
        'entidad',
        'entidad_id',
        'descripcion',
        'antes',
        'despues',
        'meta',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'antes' => 'array',
            'despues' => 'array',
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function accionLabel(): string
    {
        $labels = [
            'crear' => 'Creación',
            'actualizar' => 'Actualización',
            'archivar' => 'Archivado',
            'cambiar_rol' => 'Cambio de rol',
            'exportar_excel' => 'Exportación Excel',
            'exportar_pdf' => 'Exportación PDF',
            'generar_backup' => 'Generar backup',
            'descargar_backup' => 'Descargar backup',
            'error_backup' => 'Error en backup',
            'error_excel' => 'Error en exportación',
            'error_pdf' => 'Error en exportación',
            'login_exitoso' => 'Inicio de sesión',
            'login_fallido' => 'Intento fallido',
            'logout' => 'Cierre de sesión',
        ];

        return $labels[$this->accion] ?? str_replace('_', ' ', ucfirst($this->accion));
    }

    public function cambios(): array
    {
        $antes = $this->antes ?? [];
        $despues = $this->despues ?? [];

        if (empty($antes) && empty($despues)) {
            return [];
        }

        $keys = array_unique(array_merge(array_keys($antes), array_keys($despues)));
        $changes = [];

        foreach ($keys as $key) {
            $old = $antes[$key] ?? null;
            $new = $despues[$key] ?? null;

            if ($old !== $new) {
                $changes[] = [
                    'campo' => $key,
                    'antes' => $old,
                    'despues' => $new,
                ];
            }
        }

        return $changes;
    }

    public function metaVisible(): array
    {
        return $this->meta ?? [];
    }
}
