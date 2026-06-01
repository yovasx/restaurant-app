<?php

namespace Database\Seeders;

use App\Models\Resena;
use App\Models\Menu;
use App\Models\Comensal;
use Illuminate\Database\Seeder;

class ResenaSeeder extends Seeder
{
    public function run(): void
    {
        $comensales = Comensal::all()->pluck('id')->toArray();
        $menus = Menu::all()->groupBy('restaurante_id');

        $comentarios = [
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
            'Bueno pero puede mejorar. La atención fue un poco lenta.',
            'La comida estaba bien, pero el ambiente es ruidoso.',
            'Precios un poco elevados para la porción que sirven.',
            'Buen servicio aunque tardaron un poco en atendernos.',
            'La comida es buena pero esperaba más variedad en el menú.',
            'La comida no era lo que esperaba. Muy salada.',
            'Mala atención, nos hicieron esperar demasiado.',
            'Los precios son caros para la calidad que ofrecen.',
            'No volvería, la comida estaba fría.',
            'Deficiente, el local no estaba limpio.',
        ];

        $total = 0;

        foreach ($menus as $restauranteId => $restMenus) {
            $numReviews = random_int(3, 5);
            $usedComensales = [];

            for ($i = 0; $i < $numReviews; $i++) {
                $available = array_diff($comensales, $usedComensales);
                if (empty($available)) {
                    break;
                }
                $comensalId = $available[array_rand($available)];
                $usedComensales[] = $comensalId;

                $menu = $restMenus->random();

                $score = $this->weightedScore();
                $comentario = match (true) {
                    $score >= 4 => $comentarios[array_rand(array_slice($comentarios, 0, 10))],
                    $score >= 3 => $comentarios[10 + array_rand(array_slice($comentarios, 10, 5))],
                    default     => $comentarios[15 + array_rand(array_slice($comentarios, 15))],
                };

                Resena::create([
                    'comensal_id' => $comensalId,
                    'menu_id'     => $menu->id,
                    'score'       => $score,
                    'comentario'  => $comentario,
                ]);
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
