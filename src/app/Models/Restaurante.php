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
        'email_reservas',
        'instagram',
        'facebook_url',
        'zona',
        'fecha_registro',
        'estado',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'restaurante_id');
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class, 'restaurante_id');
    }
}
