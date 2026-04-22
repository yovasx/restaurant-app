<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_restaurantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nit')->nullable();
            $table->string('zona')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->time('hora_apertura_lunes_viernes')->nullable();
            $table->time('hora_cierre_lunes_viernes')->nullable();
            $table->time('hora_apertura_sabado')->nullable();
            $table->time('hora_cierre_sabado')->nullable();
            $table->time('hora_apertura_domingo')->nullable();
            $table->time('hora_cierre_domingo')->nullable();
            $table->string('email_reservas')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_restaurantes');
    }
};
