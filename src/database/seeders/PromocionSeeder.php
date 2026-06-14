<?php

namespace Database\Seeders;

use App\Models\Promocion;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

class PromocionSeeder extends Seeder
{
    public function run(): void
    {
        $count = 0;

        foreach ($this->data() as $emailSlug => $promos) {
            $restaurante = Restaurante::whereHas('usuario', fn($q) => $q->where('email', 'like', "contacto@{$emailSlug}%"))->first();
            if (!$restaurante) continue;

            foreach ($promos as $p) {
                Promocion::create([
                    'restaurante_id' => $restaurante->id,
                    'nombre' => $p[0],
                    'tipo' => $p[1],
                    'valor' => $p[2] ?? 0,
                    'condicion' => $p[3] ?? '',
                    'fecha_inicio' => $p[4] ?? now()->toDateString(),
                    'fecha_fin' => $p[5] ?? now()->addMonth()->toDateString(),
                    'publicidad' => $p[6] ?? 'imagen',
                    'imagen' => $p[7] ?? null,
                    'video_url' => $p[8] ?? null,
                    'estado' => 'activo',
                ]);
                $count++;
            }
        }

        // === Promociones sucursal Wok Roll Sopocachi ===
        $sopocachi = Restaurante::where('email_reservas', 'sopocachi@wokroll.com')->first();
        if ($sopocachi) {
            $sopocachiPromos = [
                ['After Office Wok: Tallarín + Cerveza 38Bs', 'descuento', 20, 'Válido de 18:00 a 20:00, de lunes a viernes', '2026-06-01', '2026-09-30'],
                ['Combo Lomo Saltado + Wantán 55Bs', 'descuento', 15, 'Lomo saltado al wok con wantán frito (8uds)', '2026-06-01', '2026-08-31'],
                ['2x1 en Aeropuerto Wok Roll', '2x1', 0, 'Compra un aeropuerto y llévate otro gratis', '2026-07-01', '2026-08-15'],
            ];
            foreach ($sopocachiPromos as $p) {
                Promocion::create([
                    'restaurante_id' => $sopocachi->id,
                    'nombre' => $p[0], 'tipo' => $p[1], 'valor' => $p[2] ?? 0,
                    'condicion' => $p[3] ?? '', 'estado' => 'activo',
                    'fecha_inicio' => $p[4] ?? now()->toDateString(),
                    'fecha_fin' => $p[5] ?? now()->addMonth()->toDateString(),
                ]);
                $count++;
            }
        }

        $this->command->info("{$count} promociones sembradas correctamente.");
    }

    private function data(): array
    {
        return [
            'mcdonalds' => [
                ['Combo Big Mac + Papas + Gaseosa', 'descuento', 15, 'Válido todos los días de 12:00 a 15:00', '2026-06-01', '2026-08-31'],
                ['McNuggets 10 piezas + 1 bebida', '2x1', 0, 'Compra un combo McNuggets y llévate otro igual gratis. Válido solo en sucursal Zona Sur', '2026-06-01', '2026-07-31'],
            ],
            'starbucks' => [
                ['2x1 en Frappuccinos', '2x1', 0, 'Válido de lunes a miércoles después de las 15:00', '2026-06-01', '2026-07-15'],
                ['Latte + Croissant a 35Bs', 'descuento', 20, 'Desayuno especial con café latte y croissant de mantequilla', '2026-06-01', '2026-08-31'],
            ],
            'burgerkinglapaz' => [
                ['Combo Whopper por 39Bs', 'descuento', 25, 'Hamburguesa Whopper, papas medianas y gaseosa', '2026-06-01', '2026-08-31'],
            ],
            'kfclapaz' => [
                ['Bucket 8 piezas + Pepsi 2L a 78Bs', 'descuento', 18, '8 piezas de pollo receta original con gaseosa familiar', '2026-06-01', '2026-07-31'],
                ['Lunes de Alitas: 6uds + Papas a 38Bs', 'descuento', 20, 'Solo los lunes, alitas BBQ con papas fritas', '2026-06-01', '2026-09-30'],
            ],
            'pizzahut' => [
                ['Pizza Mediana + Coca 1.5L a solo 69Bs', 'descuento', 15, 'Elige tu pizza favorita tamaño mediano', '2026-06-01', '2026-08-31'],
                ['2x1 en pizzas personales', '2x1', 0, 'Compra una pizza personal y llévate otra gratis del mismo valor o menor', '2026-06-15', '2026-07-31'],
            ],
            'dominoslapaz' => [
                ['Pizza Grande + 2 Coca 500ml a 79Bs', 'descuento', 20, 'Válido solo para pedidos online', '2026-06-01', '2026-08-31'],
            ],
            'subwaylapaz' => [
                ['Sub 30cm al precio de 15cm', '2x1', 0, 'Compra un sub de 30cm y el segundo de 15cm es gratis', '2026-06-01', '2026-07-31'],
                ['Cookie + Café por 12Bs', 'descuento', 30, 'Acompaña tu sub con este combo dulce', '2026-06-01', '2026-08-31'],
            ],
            'papajohnslapaz' => [
                ['Pizza Familiar + 2 Coca 500ml a 89Bs', 'descuento', 15, 'Pizza familiar pepperoni o hawaiana', '2026-06-01', '2026-08-31'],
            ],
            'hardrockcafe' => [
                ['Happy Hour 2x1 en coctelería', '2x1', 0, 'De 17:00 a 19:00, todos los días. Aplica en coctelería seleccionada', '2026-06-01', '2026-09-30'],
                ['Combo Legendary + Cerveza 68Bs', 'descuento', 20, 'Hamburguesa Legendary con papas y cerveza artesanal', '2026-06-01', '2026-08-31'],
            ],
            'tgifridayslapaz' => [
                ['Costillas BBQ Half Rack + Cóctel 2x1', '2x1', 0, 'Martes de costillas. Válido de 12:00 a 20:00', '2026-06-01', '2026-08-31'],
                ['Nachos Supreme + 2 Margaritas 88Bs', 'descuento', 20, 'Comparte nachos gigantes con 2 margaritas frozen', '2026-06-01', '2026-07-31'],
            ],
            'chilis' => [
                ['Baby Back Ribs + Margarita 98Bs', 'descuento', 15, 'Costillas baby back full rack con margarita clásica', '2026-06-01', '2026-08-31'],
                ['Combo Fajitas para 2: 138Bs', 'descuento', 20, 'Fajitas de pollo o res para dos personas con guarniciones', '2026-06-01', '2026-09-30'],
            ],
            'dunkinlapaz' => [
                ['Café + Donut por 15Bs', 'descuento', 25, 'Café americano regular + donut glaseado', '2026-06-01', '2026-08-31'],
                ['2x1 en Munchkins (10uds)', '2x1', 0, 'Lleva 10 munchkins y recibe otros 10 gratis', '2026-06-01', '2026-07-31'],
            ],
            'gustu' => [
                ['Menú Degustación 8 tiempos + Maridaje 450Bs', 'descuento', 10, 'Maridaje de vinos y singanis incluido', '2026-06-01', '2026-08-31'],
            ],
            'popularcocina' => [
                ['Combo Familiar Fricasé 85Bs', 'descuento', 15, 'Fricasé para 4 personas con refresco de frutas incluido', '2026-06-01', '2026-08-31'],
                ['2x1 en Silpancho + Refresco', '2x1', 0, 'Válido de lunes a miércoles al mediodía', '2026-06-01', '2026-07-31'],
            ],
            'brutalburger' => [
                ['Smash Burger + Papas + Cerveza 48Bs', 'descuento', 15, 'Hamburguesa smash, papas fritas y cerveza artesanal', '2026-06-01', '2026-09-30'],
                ['2x1 en Milkshakes', '2x1', 0, 'Válido de 15:00 a 18:00 todos los días', '2026-06-01', '2026-08-31'],
            ],
            'santoramen' => [
                ['Tonkotsu Ramen + Gyozas 6uds a 72Bs', 'descuento', 20, 'Ramen + gyozas de cerdo, la combinación perfecta', '2026-06-01', '2026-08-31'],
            ],
            'elhornito' => [
                ['Pizza Margherita + Tiramisú 58Bs', 'descuento', 15, 'Pizza Margherita entera + tiramisú de postre', '2026-06-01', '2026-07-31'],
                ['2x1 en Calzone los lunes', '2x1', 0, 'Compra un calzone y recibe otro gratis los lunes', '2026-06-01', '2026-08-31'],
            ],
            'mochimochi' => [
                ['Rainbow Roll + Edamame 72Bs', 'descuento', 20, 'Sushi rainbow roll + edamame con sriracha', '2026-06-01', '2026-08-31'],
            ],
            'barbarianburger' => [
                ['Barbarian XXL + Papas Cargadas 69Bs', 'descuento', 20, 'La hamburguesa más grande del menú con papas cargadas', '2026-06-01', '2026-08-31'],
            ],
            'fogonboliviano' => [
                ['Parrillada para 2 a 129Bs', 'descuento', 15, 'Parrillada completa para dos personas', '2026-06-01', '2026-07-31'],
                ['Lomo de Res + Vino Tinto 99Bs', 'descuento', 20, 'Lomo fino 300g con copa de vino tinto boliviano', '2026-06-01', '2026-09-30'],
            ],
            'nonnamia' => [
                ['Lasaña + Tiramisú a 68Bs', 'descuento', 15, 'Lasaña clásica con tiramisú de la casa', '2026-06-01', '2026-08-31'],
                ['2x1 en Ñoquis los miércoles', '2x1', 0, 'Ñoquis de papa con boloñesa: compra 1 y llévate 2', '2026-06-01', '2026-09-30'],
            ],
            'elmarino' => [
                ['Ceviche Clásico + Maracuyá 58Bs', 'descuento', 15, 'Ceviche de pescado fresco con jugo natural de maracuyá', '2026-06-01', '2026-08-31'],
            ],
            'elrancho' => [
                ['Parrillada El Rancho 2 pers. 125Bs', 'descuento', 12, 'Parrillada para dos con cortes seleccionados', '2026-06-01', '2026-08-31'],
            ],
            'samurairamen' => [
                ['Tonkotsu Ramen + Yakitori 3uds a 72Bs', 'descuento', 20, 'Ramen de cerdo con brochetas de pollo glaseadas', '2026-06-01', '2026-08-31'],
            ],
            'cafeorigen' => [
                ['Brunch Completo 45Bs', 'descuento', 18, 'Huevos benedictinos, panceta, pan artesanal, fruta y café', '2026-06-01', '2026-09-30'],
            ],
            'dulcetentacion' => [
                ['Cheesecake + Café 35Bs', 'descuento', 20, 'Cheesecake de maracuyá con café expreso o americano', '2026-06-01', '2026-08-31'],
                ['2x1 en Macarons (12uds)', '2x1', 0, '6 macarons al precio de 12: compra 6 y llévate 6 más', '2026-06-01', '2026-07-31'],
            ],
            'pollosreal' => [
                ['Pollo Entero + Papas Grandes + Coca 2L 62Bs', 'descuento', 15, 'Pollo al spiedo entero con guarniciones familiares', '2026-06-01', '2026-08-31'],
            ],
            'dragonrojo' => [
                ['Arroz Chaufa + Wantán Frito 48Bs', 'descuento', 20, 'Arroz chaufa especial con wantán frito (10uds)', '2026-06-01', '2026-07-31'],
            ],
            'panartesano' => [
                ['Croissant + Café Latte 28Bs', 'descuento', 22, 'Croissant de mantequilla con café latte artesanal', '2026-06-01', '2026-09-30'],
            ],
            'wokroll' => [
                ['Combo Tallarín Saltado + Wantán 42Bs', 'descuento', 20, 'Tallarín saltado mixto con wantán frito (8uds)', '2026-06-01', '2026-08-31'],
                ['Arroz Chaufa Familiar + 4 Gaseosas 68Bs', 'descuento', 18, 'Arroz chaufa de la casa para 4 personas con 4 gaseosas', '2026-06-01', '2026-09-30'],
                ['2x1 en Dim Sum (12uds)', '2x1', 0, 'Compra 6 dim sum y llévate 6 más', '2026-06-15', '2026-07-31'],
            ],
        ];
    }
}
