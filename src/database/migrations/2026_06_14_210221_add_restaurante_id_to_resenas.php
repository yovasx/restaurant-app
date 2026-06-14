<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            $table->foreignId('restaurante_id')->nullable()->constrained('restaurantes')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE resenas ALTER COLUMN menu_id DROP NOT NULL');

        DB::statement('UPDATE resenas SET restaurante_id = menus.restaurante_id FROM menus WHERE resenas.menu_id = menus.id');
    }

    public function down(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['restaurante_id']);
            $table->dropColumn('restaurante_id');
        });

        DB::statement('ALTER TABLE resenas ALTER COLUMN menu_id SET NOT NULL');
    }
};
