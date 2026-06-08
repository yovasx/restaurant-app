<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\Comensal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        $comensales = Comensal::all()->pluck('id')->toArray();
        $restaurantes = Restaurante::where('estado', 'activo')->pluck('id')->toArray();

        if (empty($comensales) || empty($restaurantes)) {
            $this->command->warn('No hay comensales o restaurantes para sembrar visitas.');
            return;
        }

        $metodos = ['geolocalizacion', 'manual', 'qr'];
        $total = 0;

        foreach ($restaurantes as $restauranteId) {
            $numVisits = random_int(8, 15);

            $usedPairs = [];
            $visitDates = [];

            for ($i = 0; $i < $numVisits; $i++) {
                $comensalId = $comensales[array_rand($comensales)];

                $offset = random_int(0, 44);
                $fecha = now()->subDays($offset)->toDateString();

                $pairKey = "{$comensalId}_{$restauranteId}_{$fecha}";
                if (isset($usedPairs[$pairKey])) {
                    continue;
                }
                $usedPairs[$pairKey] = true;

                $fechaVisita = now()->subDays($offset);
                $createdAt = (clone $fechaVisita)->addHours(random_int(8, 22))->addMinutes(random_int(0, 59));

                DB::table('visitas')->insert([
                    'restaurante_id' => $restauranteId,
                    'comensal_id'    => $comensalId,
                    'fecha_visita'   => $fecha,
                    'metodo'         => $metodos[array_rand($metodos)],
                    'created_at'     => $createdAt,
                    'updated_at'     => $createdAt,
                ]);
                $total++;
            }
        }

        $top5 = array_slice($restaurantes, 0, min(5, count($restaurantes)));
        foreach ($top5 as $restauranteId) {
            $extra = random_int(5, 15);
            for ($i = 0; $i < $extra; $i++) {
                $comensalId = $comensales[array_rand($comensales)];
                $offset = random_int(0, 44);
                $fecha = now()->subDays($offset)->toDateString();

                $fechaVisita = now()->subDays($offset);
                $createdAt = (clone $fechaVisita)->addHours(random_int(8, 22))->addMinutes(random_int(0, 59));

                DB::table('visitas')->insert([
                    'restaurante_id' => $restauranteId,
                    'comensal_id'    => $comensalId,
                    'fecha_visita'   => $fecha,
                    'metodo'         => $metodos[array_rand($metodos)],
                    'created_at'     => $createdAt,
                    'updated_at'     => $createdAt,
                ]);
                $total++;
            }
        }

        $this->command->info("$total visitas creadas correctamente.");
    }
}
