<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'estado',
        'rol_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function perfilRestaurante()
    {
        return $this->hasOne(PerfilRestaurante::class, 'usuario_id');
    }

    public function restaurantes()
    {
        return $this->hasMany(Restaurante::class, 'usuario_id');
    }
}
