<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre_categoria' => 'Pizza',              'descripcion' => 'Pizzerías artesanales, italianas y napolitanas'],
            ['nombre_categoria' => 'Sushi',              'descripcion' => 'Sushi bar, cocina japonesa y nikkei'],
            ['nombre_categoria' => 'Burgers',            'descripcion' => 'Hamburguesas gourmet, american food y fast food premium'],
            ['nombre_categoria' => 'Parrilla',           'descripcion' => 'Parrilladas, carnes a la brasa y cortes argentinos'],
            ['nombre_categoria' => 'Comida Boliviana',   'descripcion' => 'Cocina tradicional boliviana, salteñas, silpancho, pique macho'],
            ['nombre_categoria' => 'Café y Brunch',      'descripcion' => 'Café de especialidad, brunch, repostería y desayunos'],
            ['nombre_categoria' => 'Pastas',             'descripcion' => 'Pastas artesanales, lasañas y cocina italiana'],
            ['nombre_categoria' => 'Pollos',             'descripcion' => 'Pollo al spiedo, broaster y pollos a la brasa'],
            ['nombre_categoria' => 'Chifa',              'descripcion' => 'Cocina fusión chino-peruana, arroces y wantanes'],
            ['nombre_categoria' => 'Mariscos',           'descripcion' => 'Pescados y mariscos, ceviches y parihuelas'],
            ['nombre_categoria' => 'Postres y Panadería', 'descripcion' => 'Pastelería fina, tortas, helados y pan artesanal'],
            ['nombre_categoria' => 'Cocina Fusión',      'descripcion' => 'Cocina de autor, fusión latinoamericana y experimental'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['nombre_categoria' => $cat['nombre_categoria']],
                $cat,
            );
        }

        $this->command->info('12 categorías sembradas correctamente.');
    }
}
