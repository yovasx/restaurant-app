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
        // 1. Tabla Pivot: rol_operacion
        Schema::create('rol_operacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('operacion_id')->constrained('operaciones')->onDelete('cascade');
            // Esto evita que un mismo rol tenga la misma operación repetida
            $table->unique(['rol_id', 'operacion_id']);
        });

        // 2. Tabla Pivot: restaurante_categorias
        Schema::create('restaurante_categorias', function (Blueprint $table) {
            $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            // Usamos una llave primaria compuesta (muy profesional)
            $table->primary(['restaurante_id', 'categoria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Importante: Borrar las tablas en orden inverso
        Schema::dropIfExists('restaurante_categorias');
        Schema::dropIfExists('rol_operacion');
    }
};