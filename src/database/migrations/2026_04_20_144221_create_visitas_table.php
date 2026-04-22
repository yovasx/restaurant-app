<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
            $table->foreignId('comensal_id')->constrained('comensales')->onDelete('cascade');
            $table->date('fecha_visita');
            $table->string('metodo', 50)->default('geolocalizacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
