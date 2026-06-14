<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'restaurante_id',
        'comensal_id',
        'fecha_pedido',
        'estado',
        'total',
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    public function comensal()
    {
        return $this->belongsTo(Comensal::class);
    }

    public function detalle()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
