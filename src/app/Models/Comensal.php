<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Comensal extends Authenticatable
{
    use Notifiable;

    protected $table = 'comensales';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];
}
