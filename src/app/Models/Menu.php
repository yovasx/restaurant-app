<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'restaurante_id', 'categoria_id', 'nombre', 'descripcion',
        'precio', 'foto_plato', 'tipo', 'estado', 'orden',
    ];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }
}
