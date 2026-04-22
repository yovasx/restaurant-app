<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('cascade');
            $table->string('email_reservas')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('zona', 150)->nullable();
            $table->string('nit', 50)->nullable();
            $table->time('hora_apertura_sabado')->nullable();
            $table->time('hora_cierre_sabado')->nullable();
            $table->time('hora_apertura_domingo')->nullable();
            $table->time('hora_cierre_domingo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn([
                'usuario_id', 'email_reservas', 'instagram', 'facebook_url', 
                'zona', 'nit', 'hora_apertura_sabado', 'hora_cierre_sabado', 
                'hora_apertura_domingo', 'hora_cierre_domingo'
            ]);
        });
    }
};
