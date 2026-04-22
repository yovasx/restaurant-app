<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    protected $table = 'restaurantes';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'horario_apertura',
        'horario_cierre',
        'hora_apertura_sabado',
        'hora_cierre_sabado',
        'hora_apertura_domingo',
        'hora_cierre_domingo',
        'foto_portada',
        'telefono',
        'fecha_registro',
        'estado',
        'email_reservas',
        'instagram',
        'facebook_url',
        'zona',
        'nit'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
