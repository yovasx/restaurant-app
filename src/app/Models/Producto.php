<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'categoria_id',
        'foto',
        'descripcion',
        'usuario_id',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
