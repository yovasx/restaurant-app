<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';

    protected $fillable = [
        'comensal_id', 'menu_id', 'visita_id', 'score', 'comentario', 'respuesta_restaurante',
    ];

    public function comensal()
    {
        return $this->belongsTo(Comensal::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
