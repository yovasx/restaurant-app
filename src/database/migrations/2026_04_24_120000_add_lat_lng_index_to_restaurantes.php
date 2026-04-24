<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            // Add a composite index for lat/lng lookups
            $table->index(['latitud', 'longitud'], 'restaurantes_lat_lng_index');
        });
    }

    public function down()
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropIndex('restaurantes_lat_lng_index');
        });
    }
};
