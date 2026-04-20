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
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comensal_id')->constrained('comensales')->onDelete('cascade');
            $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
    
            // Evita que un comensal tenga el mismo restaurante dos veces en favoritos
            $table->unique(['comensal_id', 'restaurante_id']);        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
