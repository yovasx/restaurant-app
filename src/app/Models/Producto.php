<?php

namespace App\Models;

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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }
}
