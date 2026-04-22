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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('set null');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('foto_plato', 255)->nullable();
            $table->string('tipo', 20)->default('fijo'); // fijo | del_dia
            $table->string('estado', 20)->default('activo');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
