<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create a materialized view with aggregated avg_rating and avg_price per restaurante
        DB::statement(<<<'SQL'
CREATE MATERIALIZED VIEW IF NOT EXISTS restaurantes_stats AS
SELECT r.id AS restaurante_id,
       r.usuario_id,
       AVG(resenas.score) AS avg_rating,
       AVG(productos.precio) AS avg_price
FROM restaurantes r
LEFT JOIN menus ON menus.restaurante_id = r.id
LEFT JOIN resenas ON resenas.menu_id = menus.id
LEFT JOIN productos ON productos.usuario_id = r.usuario_id AND productos.precio IS NOT NULL
GROUP BY r.id, r.usuario_id;
SQL
        );

        // Indexes to speed up joins
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_restaurante_id_idx ON restaurantes_stats(restaurante_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_usuario_id_idx ON restaurantes_stats(usuario_id)');
    }

    public function down()
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS restaurantes_stats');
    }
};
