<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilRestaurante extends Model
{
    protected $table = 'perfiles_restaurante';

    protected $fillable = [
        'usuario_id',
        'nit',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
