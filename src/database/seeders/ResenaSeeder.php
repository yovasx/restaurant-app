<?php

namespace Database\Seeders;

use App\Models\Resena;
use App\Models\Menu;
use App\Models\Comensal;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

class ResenaSeeder extends Seeder
{
    public function run(): void
    {
        $comensales = Comensal::all()->pluck('id')->toArray();
        $menus = Menu::all()->groupBy('restaurante_id');

        $comentariosPositivos = [
            'Excelente atención, la comida deliciosa. Volveré sin duda.',
            'Muy buena relación calidad-precio. Recomendado.',
            'El plato estrella es increíble, superó mis expectativas.',
            'Ambiente agradable y personal muy amable.',
            'La mejor experiencia gastronómica en La Paz.',
            'Todo fresco y bien preparado. Volvemos seguido.',
            'Los precios son justos y la porción es generosa.',
            'Muy recomendable, el sabor es auténtico.',
            'Atención rápida y comida deliciosa. 5 estrellas.',
            'Me encantó, definitivamente mi nuevo lugar favorito.',
        ];

        $comentariosNeutros = [
            'Bueno pero puede mejorar. La atención fue un poco lenta.',
            'La comida estaba bien, pero el ambiente es ruidoso.',
            'Precios un poco elevados para la porción que sirven.',
            'Buen servicio aunque tardaron un poco en atendernos.',
            'La comida es buena pero esperaba más variedad en el menú.',
        ];

        $comentariosNegativos = [
            'La comida no era lo que esperaba. Muy salada.',
            'Mala atención, nos hicieron esperar demasiado.',
            'Los precios son caros para la calidad que ofrecen.',
            'No volvería, la comida estaba fría.',
            'Deficiente, el local no estaba limpio.',
        ];

        $total = 0;

        foreach ($menus as $restauranteId => $restMenus) {
            $numReviews = random_int(4, 8);
            $usedComensales = [];

            // Ensure at least one review of each score bucket per restaurant
            $forcedScores = [1, 3, 5];
            foreach ($forcedScores as $fs) {
                $available = array_diff($comensales, $usedComensales);
                if (empty($available)) break;

                $comensalId = $available[array_rand($available)];
                $usedComensales[] = $comensalId;
                $menu = $restMenus->random();

                $comentario = match (true) {
                    $fs >= 4 => $comentariosPositivos[array_rand($comentariosPositivos)],
                    $fs >= 3 => $comentariosNeutros[array_rand($comentariosNeutros)],
                    default  => $comentariosNegativos[array_rand($comentariosNegativos)],
                };

                $offset = random_int(0, 44);
                $createdAt = now()->subDays($offset)->addHours(random_int(8, 22))->addMinutes(random_int(0, 59));

                $resena = new Resena([
                    'comensal_id' => $comensalId,
                    'menu_id'     => $menu->id,
                    'score'       => $fs,
                    'comentario'  => $comentario,
                ]);
                $resena->created_at = $createdAt;
                $resena->updated_at = $createdAt;
                $resena->save();
                $total++;
            }

            // Fill remaining reviews randomly
            $remaining = $numReviews - count($forcedScores);
            for ($i = 0; $i < $remaining; $i++) {
                $available = array_diff($comensales, $usedComensales);
                if (empty($available)) break;

                $comensalId = $available[array_rand($available)];
                $usedComensales[] = $comensalId;

                $menu = $restMenus->random();

                $score = $this->weightedScore();
                $comentario = match (true) {
                    $score >= 4 => $comentariosPositivos[array_rand($comentariosPositivos)],
                    $score >= 3 => $comentariosNeutros[array_rand($comentariosNeutros)],
                    default     => $comentariosNegativos[array_rand($comentariosNegativos)],
                };

                $offset = random_int(0, 44);
                $createdAt = now()->subDays($offset)->addHours(random_int(8, 22))->addMinutes(random_int(0, 59));

                $resena = new Resena([
                    'comensal_id' => $comensalId,
                    'menu_id'     => $menu->id,
                    'score'       => $score,
                    'comentario'  => $comentario,
                ]);
                $resena->created_at = $createdAt;
                $resena->updated_at = $createdAt;
                $resena->save();
                $total++;
            }
        }

        // === Reseñas extra para Wok Roll ===
        $wokIds = Restaurante::whereHas('usuario', fn($q) => $q->where('email', 'contacto@wokroll.com'))->pluck('id');
        $wokMenus = Menu::whereIn('restaurante_id', $wokIds)->get();
        foreach ($wokMenus->groupBy('restaurante_id') as $rid => $menus) {
            $extra = random_int(6, 12);
            $usedLocal = [];
            for ($i = 0; $i < $extra; $i++) {
                $available = array_diff($comensales, $usedLocal);
                if (empty($available)) break;
                $comensalId = $available[array_rand($available)];
                $usedLocal[] = $comensalId;
                $menu = $menus->random();
                $score = $this->weightedScore();
                $comentario = match (true) {
                    $score >= 4 => $comentariosPositivos[array_rand($comentariosPositivos)],
                    $score >= 3 => $comentariosNeutros[array_rand($comentariosNeutros)],
                    default => $comentariosNegativos[array_rand($comentariosNegativos)],
                };
                $offset = random_int(0, 44);
                $createdAt = now()->subDays($offset)->addHours(random_int(8, 22))->addMinutes(random_int(0, 59));
                $resena = new Resena([
                    'comensal_id' => $comensalId, 'menu_id' => $menu->id,
                    'score' => $score, 'comentario' => $comentario,
                ]);
                $resena->created_at = $createdAt;
                $resena->updated_at = $createdAt;
                $resena->save();
                $total++;
            }
        }

        $this->command->info("$total reseñas creadas correctamente.");
    }

    private function weightedScore(): int
    {
        $r = random_int(1, 100);
        return match (true) {
            $r <= 10  => 1,
            $r <= 20  => 2,
            $r <= 40  => 3,
            $r <= 70  => 4,
            default   => 5,
        };
    }
}
