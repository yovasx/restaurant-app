<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'publicidad',
        'video_url',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    protected $appends = ['imagen_url', 'video_url_resolved'];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    protected function imagenUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => media_url($this->imagen),
        );
    }

    protected function videoUrlResolved(): Attribute
    {
        return Attribute::make(
            get: fn () => media_url($this->video_url),
        );
    }
}
