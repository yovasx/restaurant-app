<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'restaurante_id',
        'nombre',
        'precio',
        'stock',
        'categoria_id',
        'foto',
        'descripcion',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    protected $appends = ['foto_url'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => media_url($this->foto),
        );
    }
}
