<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRestaurante extends Model
{
    use HasFactory;

    protected $table = 'detalles_restaurantes';

    protected $fillable = [
        'usuario_id',
        'nit',
        'zona',
        'direccion',
        'latitud',
        'longitud',
        'hora_apertura_lunes_viernes',
        'hora_cierre_lunes_viernes',
        'hora_apertura_sabado',
        'hora_cierre_sabado',
        'hora_apertura_domingo',
        'hora_cierre_domingo',
        'email_reservas',
        'instagram',
        'facebook_url',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
