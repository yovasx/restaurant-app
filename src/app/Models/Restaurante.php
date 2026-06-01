<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'logo_url',
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

    protected $appends = ['foto_portada_url', 'logo_url_resolved'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'restaurante_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'restaurante_id');
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class, 'restaurante_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'restaurante_categorias');
    }

    protected function fotoPortadaUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => media_url($this->foto_portada),
        );
    }

    protected function logoUrlResolved(): Attribute
    {
        return Attribute::make(
            get: fn () => media_url($this->logo_url),
        );
    }
}
