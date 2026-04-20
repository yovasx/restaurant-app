<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'restaurante_id',
        'nombre',
        'tipo',
        'valor',
        'condicion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'imagen',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }
}
