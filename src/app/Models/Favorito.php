<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorito extends Model
{
    protected $fillable = ['comensal_id', 'restaurante_id'];

    public function comensal(): BelongsTo
    {
        return $this->belongsTo(Comensal::class);
    }

    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }
}
