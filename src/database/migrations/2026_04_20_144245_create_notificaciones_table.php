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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comensal_id')->constrained('comensales')->onDelete('cascade');
            $table->foreignId('promocion_id')->nullable()->constrained('promociones')->onDelete('set null');
            $table->string('tipo', 50)->nullable();
            $table->text('mensaje');
            $table->timestamp('fecha_envio')->nullable();
            $table->boolean('leido')->default(false);         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
