<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'administrador', 'estado' => 'activo'],
            ['id' => 2, 'nombre' => 'restaurante', 'estado' => 'activo'],
        ]);

        if (!Usuario::where('email', 'admin@admin.com')->exists()) {
            Usuario::create([
                'nombre'    => 'Administrador',
                'email'     => 'admin@admin.com',
                'password'  => Hash::make('admin123'),
                'telefono'  => '70000000',
                'estado'    => 'activo',
                'rol_id'    => 1,
            ]);
        }

        $this->call(CategoriaSeeder::class);
        $this->call(RestauranteSeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(PromocionSeeder::class);
        $this->call(ComensalSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(VisitaSeeder::class);
        $this->call(ResenaSeeder::class);
        $this->call(PedidoSeeder::class);
    }
}
