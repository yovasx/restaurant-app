<?php

namespace Database\Seeders;

use App\Models\Comensal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComensalSeeder extends Seeder
{
    public function run(): void
    {
        $personas = [
            ['Carlos', 'Mamani', 'Quispe', '71234501'],
            ['María', 'Quispe', 'Flores', '71234502'],
            ['Juan', 'Condori', 'Morales', '71234503'],
            ['Ana', 'López', 'García', '71234504'],
            ['Luis', 'Choque', 'Rodríguez', '71234505'],
            ['Rosa', 'Martínez', 'Vargas', '71234506'],
            ['José', 'Gutiérrez', 'Alvarez', '71234507'],
            ['Carmen', 'Ramos', 'Pérez', '71234508'],
            ['Miguel', 'Sánchez', 'Torrez', '71234509'],
            ['Elena', 'Romero', 'Salinas', '71234510'],
            ['Pedro', 'Rojas', 'Quino', '71234511'],
            ['Silvia', 'Fernández', 'González', '71234512'],
            ['Roberto', 'Castro', 'Cruz', '71234513'],
            ['Patricia', 'Aruquipa', 'Yucra', '71234514'],
            ['Fernando', 'Quisbert', 'Chambi', '71234515'],
            ['Marta', 'Ticona', 'Paco', '71234516'],
            ['Jorge', 'Huanca', 'Quenta', '71234517'],
            ['Laura', 'Mayta', 'Calle', '71234518'],
            ['David', 'Apaza', 'Aguilar', '71234519'],
            ['Andrea', 'Camacho', 'Paredes', '71234520'],
            ['Hugo', 'Villca', 'Mendoza', '71234521'],
            ['Cecilia', 'Pinto', 'Cordero', '71234522'],
            ['Marco', 'Orellana', 'Navarro', '71234523'],
            ['Isabel', 'Rivero', 'Roca', '71234524'],
            ['Eduardo', 'Llanos', 'Antezana', '71234525'],
            ['Verónica', 'Balboa', 'Cabrera', '71234526'],
            ['Sergio', 'Delgado', 'Espinoza', '71234527'],
            ['Mónica', 'Valverde', 'Padilla', '71234528'],
            ['Pablo', 'Medrano', 'Bustamante', '71234529'],
            ['Sandra', 'Escobar', 'Villarroel', '71234530'],
            ['Diego', 'Justiniano', 'Alvarado', '71234531'],
            ['Adriana', 'Hurtado', 'Velasco', '71234532'],
            ['Ricardo', 'Peñaranda', 'Saavedra', '71234533'],
            ['Teresa', 'Aguirre', 'Bautista', '71234534'],
            ['Alberto', 'Heredia', 'Loza', '71234535'],
            ['Lucía', 'Montaño', 'Olivera', '71234536'],
            ['Martín', 'Pereira', 'Quiroz', '71234537'],
            ['Gabriela', 'Salazar', 'Uriarte', '71234538'],
            ['Daniel', 'Quispe', 'Mamani', '71234539'],
            ['Susana', 'Flores', 'García', '71234540'],
            ['Andrés', 'Condori', 'Sánchez', '71234541'],
            ['Pamela', 'Morales', 'Martínez', '71234542'],
            ['Gustavo', 'López', 'Romero', '71234543'],
            ['Marcela', 'Choque', 'Rojas', '71234544'],
            ['Oscar', 'Vargas', 'Castro', '71234545'],
            ['Juana', 'Alvarez', 'Fernández', '71234546'],
            ['Raúl', 'Pérez', 'Camacho', '71234547'],
            ['Ruth', 'Torrez', 'Paredes', '71234548'],
            ['Javier', 'Salinas', 'Aguilar', '71234549'],
            ['Lourdes', 'Cruz', 'Quisbert', '71234550'],
        ];

        foreach ($personas as $p) {
            [$nombre, $ap, $am, $tel] = $p;
            $email = strtolower($nombre) . '.' . strtolower($ap) . $tel . '@example.com';

            Comensal::create([
                'nombre'            => $nombre,
                'apellido_paterno'  => $ap,
                'apellido_materno'  => $am,
                'email'             => $email,
                'telefono'          => $tel,
                'password'          => Hash::make('comensal123'),
                'estado'            => 'activo',
            ]);
        }

        $this->command->info('50 comensales creados correctamente.');
    }
}
