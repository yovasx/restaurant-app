<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Drop dependent objects before altering columns
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS restaurantes_stats');

        // 1. Create perfiles_restaurante (1:1 business profile for restaurant accounts)
        Schema::create('perfiles_restaurante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->string('nit', 50);
            $table->timestamps();
        });

        // 2. Drop detalles_restaurantes (unused, replaced by sucursales model)
        Schema::dropIfExists('detalles_restaurantes');

        // 3. Add apellido_paterno / apellido_materno to comensales
        Schema::table('comensales', function (Blueprint $table) {
            $table->string('apellido_paterno', 100)->after('nombre');
            $table->string('apellido_materno', 100)->after('apellido_paterno');
        });
        DB::statement('ALTER TABLE comensales ALTER COLUMN telefono SET NOT NULL');

        // 4. usuarios.telefono NOT NULL
        DB::statement('ALTER TABLE usuarios ALTER COLUMN telefono SET NOT NULL');

        // 5. Alter restaurantes: add es_principal, telefono NOT NULL, drop nit (moved to perfiles_restaurante)
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->boolean('es_principal')->default(false)->after('estado');
        });
        DB::statement('ALTER TABLE restaurantes ALTER COLUMN telefono SET NOT NULL');
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropColumn('nit');
        });

        // 6. Productos: drop usuario_id FK, add restaurante_id FK
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('restaurante_id')->after('id')->constrained('restaurantes')->onDelete('cascade');
        });

        // 7. Recreate materialized view with the new column name
        DB::statement(<<<'SQL'
CREATE MATERIALIZED VIEW restaurantes_stats AS
SELECT r.id AS restaurante_id,
       r.usuario_id,
       AVG(resenas.score) AS avg_rating,
       AVG(productos.precio) AS avg_price
FROM restaurantes r
LEFT JOIN menus ON menus.restaurante_id = r.id
LEFT JOIN resenas ON resenas.menu_id = menus.id
LEFT JOIN productos ON productos.restaurante_id = r.id AND productos.precio IS NOT NULL
GROUP BY r.id, r.usuario_id;
SQL
        );
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_restaurante_id_idx ON restaurantes_stats(restaurante_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_usuario_id_idx ON restaurantes_stats(usuario_id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS restaurantes_stats');

        Schema::dropIfExists('perfiles_restaurante');

        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['restaurante_id']);
            $table->dropColumn('restaurante_id');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
        });

        Schema::table('comensales', function (Blueprint $table) {
            $table->dropColumn(['apellido_paterno', 'apellido_materno']);
        });
        DB::statement('ALTER TABLE comensales ALTER COLUMN telefono DROP NOT NULL');
        DB::statement('ALTER TABLE usuarios ALTER COLUMN telefono DROP NOT NULL');
        DB::statement('ALTER TABLE restaurantes ALTER COLUMN telefono DROP NOT NULL');

        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropColumn('es_principal');
            $table->string('nit', 50)->nullable();
        });

        // Restore materialized view with old column name
        DB::statement(<<<'SQL'
CREATE MATERIALIZED VIEW restaurantes_stats AS
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
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_restaurante_id_idx ON restaurantes_stats(restaurante_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS restaurantes_stats_usuario_id_idx ON restaurantes_stats(usuario_id)');
    }
};
