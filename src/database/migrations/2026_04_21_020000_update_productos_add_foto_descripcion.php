<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('foto');
            $table->foreignId('usuario_id')->nullable()->after('categoria_id')->constrained('usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn(['foto', 'descripcion', 'usuario_id']);
        });
    }
};
