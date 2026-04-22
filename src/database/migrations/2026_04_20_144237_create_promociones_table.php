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
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
            $table->string('nombre', 150);
            $table->string('tipo', 30)->default('descuento'); // descuento | 2x1 | postre
            $table->decimal('valor', 10, 2)->nullable();
            $table->text('condicion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('publicidad', 20)->default('imagen'); // imagen | video
            $table->string('estado', 20)->default('activo');        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
