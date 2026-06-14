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
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->string('brand_primary_color', 7)->nullable()->after('logo_url');
            $table->string('brand_secondary_color', 7)->nullable()->after('brand_primary_color');
            $table->string('brand_accent_color', 7)->nullable()->after('brand_secondary_color');
            $table->boolean('brand_enabled')->default(true)->after('brand_accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropColumn(['brand_primary_color', 'brand_secondary_color', 'brand_accent_color', 'brand_enabled']);
        });
    }
};
