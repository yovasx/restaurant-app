<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table = 'visitas';

    protected $fillable = [
        'restaurante_id',
        'comensal_id',
        'fecha_visita',
        'metodo',
    ];
}
