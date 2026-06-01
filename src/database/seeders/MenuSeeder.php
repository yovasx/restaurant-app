<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Menu;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        $count = 0;

        foreach ($productos as $p) {
            Menu::create([
                'restaurante_id' => $p->restaurante_id,
                'categoria_id'   => $p->categoria_id,
                'nombre'         => $p->nombre,
                'descripcion'    => $p->descripcion,
                'precio'         => $p->precio,
                'foto_plato'     => $p->foto,
                'tipo'           => 'fijo',
                'estado'         => 'activo',
                'orden'          => 0,
            ]);
            $count++;
        }

        $restaurantesSinMenu = Restaurante::whereDoesntHave('menus')->get();
        foreach ($restaurantesSinMenu as $r) {
            Menu::create([
                'restaurante_id' => $r->id,
                'nombre'         => 'Menú del día',
                'descripcion'    => 'Plato principal de la casa',
                'precio'         => 45.00,
                'tipo'           => 'del_dia',
                'estado'         => 'activo',
                'orden'          => 0,
            ]);
            $count++;
        }

        $this->command->info("$count menús creados.");
    }
}
