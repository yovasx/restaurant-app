<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comensal extends Authenticatable
{
    use Notifiable;

    protected $table = 'comensales';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'telefono',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }
}
