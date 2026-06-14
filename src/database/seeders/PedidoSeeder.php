<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\Comensal;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantes = Restaurante::where('estado', 'activo')->pluck('id')->toArray();
        $comensales = Comensal::all()->pluck('id')->toArray();

        if (empty($restaurantes) || empty($comensales)) {
            $this->command->warn('No hay restaurantes o comensales para sembrar pedidos.');
            return;
        }

        $totalPedidos = 0;
        $totalDetalles = 0;
        $daysBack = 45;

        // === Generic orders for all restaurants ===
        foreach ($restaurantes as $restauranteId) {
            $menus = Menu::where('restaurante_id', $restauranteId)->pluck('id')->toArray();
            if (empty($menus)) continue;

            $numPedidos = random_int(20, 40);
            $menuCount = count($menus);

            for ($i = 0; $i < $numPedidos; $i++) {
                $dia = random_int(0, $daysBack);
                $fecha = now()->subDays($dia);

                $horaRand = random_int(0, 100);
                if ($horaRand < 40) {
                    $fecha = $fecha->setTime(random_int(11, 14), random_int(0, 59));
                } elseif ($horaRand < 80) {
                    $fecha = $fecha->setTime(random_int(19, 21), random_int(0, 59));
                } else {
                    $fecha = $fecha->setTime(random_int(8, 22), random_int(0, 59));
                }

                $comensalId = $comensales[array_rand($comensales)];
                $numItems = random_int(1, 3);

                $detalleMenus = [];
                for ($j = 0; $j < $numItems; $j++) {
                    $idx = random_int(0, $menuCount - 1);
                    $menuId = $menus[$idx];
                    if (isset($detalleMenus[$menuId])) {
                        $detalleMenus[$menuId]['cantidad']++;
                    } else {
                        $menu = Menu::find($menuId);
                        $detalleMenus[$menuId] = [
                            'menu_id' => $menuId,
                            'cantidad' => 1,
                            'precio_unitario' => $menu ? $menu->precio : 0,
                        ];
                    }
                }

                $total = array_reduce($detalleMenus, fn($carry, $item) => $carry + ($item['cantidad'] * $item['precio_unitario']), 0);

                $pedidoId = DB::table('pedidos')->insertGetId([
                    'restaurante_id' => $restauranteId,
                    'comensal_id' => $comensalId,
                    'fecha_pedido' => $fecha,
                    'estado' => 'completado',
                    'total' => $total,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
                $totalPedidos++;

                foreach ($detalleMenus as $detalle) {
                    DB::table('detalle_pedido')->insert([
                        'pedido_id' => $pedidoId,
                        'menu_id' => $detalle['menu_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'created_at' => $fecha,
                        'updated_at' => $fecha,
                    ]);
                    $totalDetalles++;
                }
            }

            // Extra popular orders per restaurant
            $extraPopular = array_slice($menus, 0, max(1, intdiv($menuCount, 3)));
            $extraPedidos = random_int(5, 15);
            for ($i = 0; $i < $extraPedidos; $i++) {
                $dia = random_int(0, $daysBack);
                $fecha = now()->subDays($dia);

                $horaRand = random_int(0, 100);
                if ($horaRand < 40) {
                    $fecha = $fecha->setTime(random_int(11, 14), random_int(0, 59));
                } elseif ($horaRand < 80) {
                    $fecha = $fecha->setTime(random_int(19, 21), random_int(0, 59));
                } else {
                    $fecha = $fecha->setTime(random_int(8, 22), random_int(0, 59));
                }

                $comensalId = $comensales[array_rand($comensales)];
                $numItems = random_int(1, 2);
                $detalleMenus = [];

                for ($j = 0; $j < $numItems; $j++) {
                    $menuId = $extraPopular[array_rand($extraPopular)];
                    if (isset($detalleMenus[$menuId])) {
                        $detalleMenus[$menuId]['cantidad']++;
                    } else {
                        $menu = Menu::find($menuId);
                        $detalleMenus[$menuId] = [
                            'menu_id' => $menuId,
                            'cantidad' => 1,
                            'precio_unitario' => $menu ? $menu->precio : 0,
                        ];
                    }
                }

                $total = array_reduce($detalleMenus, fn($carry, $item) => $carry + ($item['cantidad'] * $item['precio_unitario']), 0);

                $pedidoId = DB::table('pedidos')->insertGetId([
                    'restaurante_id' => $restauranteId,
                    'comensal_id' => $comensalId,
                    'fecha_pedido' => $fecha,
                    'estado' => 'completado',
                    'total' => $total,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
                $totalPedidos++;

                foreach ($detalleMenus as $detalle) {
                    DB::table('detalle_pedido')->insert([
                        'pedido_id' => $pedidoId,
                        'menu_id' => $detalle['menu_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'created_at' => $fecha,
                        'updated_at' => $fecha,
                    ]);
                    $totalDetalles++;
                }
            }
        }

        // === Wok Roll principal: structured Markov profiles + ambient noise ===
        $wokRoll = Restaurante::whereHas('usuario', fn($q) => $q->where('email', 'contacto@wokroll.com'))->where('es_principal', true)->first();
        if ($wokRoll) {
            $wokMenus = Menu::where('restaurante_id', $wokRoll->id)->pluck('id')->toArray();
            if (!empty($wokMenus)) {
                $buckets = $this->buildMenuBuckets($wokMenus);
                $totalPedidos += $this->seedSlotProfiles($wokRoll, $comensales, $buckets, 12, $this->principalProfiles());
                $totalPedidos += $this->seedAmbientNoise($wokRoll, $comensales, $buckets, 150, 200);
            }
        }

        // === Wok Roll Sopocachi: structured Markov profiles + ambient noise ===
        $wokBranch = Restaurante::whereHas('usuario', fn($q) => $q->where('email', 'contacto@wokroll.com'))->where('es_principal', false)->first();
        if ($wokBranch) {
            $branchMenus = Menu::where('restaurante_id', $wokBranch->id)->pluck('id')->toArray();
            if (!empty($branchMenus)) {
                $buckets = $this->buildMenuBuckets($branchMenus);
                $totalPedidos += $this->seedSlotProfiles($wokBranch, $comensales, $buckets, 12, $this->branchProfiles());
                $totalPedidos += $this->seedAmbientNoise($wokBranch, $comensales, $buckets, 80, 120);
            }
        }

        $this->command->info("$totalPedidos pedidos con $totalDetalles detalles creados.");
    }

    // ==================== WOK ROLL PROFILES ====================

    private function principalProfiles(): array
    {
        // 12 semanas de intensidad por slot (más reciente = última posición)
        // Cada valor = pedidos aprox a generar en esa semana para ese slot
        // lunes=0 .. domingo=6
        return [
            // Almuerzo
            ['dow' => 0, 'hour' => 13, 'weeks' => [3, 4, 6, 2, 5, 7, 3, 4, 8, 5, 6, 3]],
            ['dow' => 0, 'hour' => 12, 'weeks' => [2, 3, 4, 2, 4, 5, 2, 3, 6, 4, 4, 2]],
            ['dow' => 0, 'hour' => 14, 'weeks' => [2, 3, 4, 1, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 1, 'hour' => 13, 'weeks' => [3, 4, 5, 3, 5, 6, 3, 4, 7, 5, 5, 3]],
            ['dow' => 1, 'hour' => 12, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 4, 4, 2]],
            ['dow' => 1, 'hour' => 14, 'weeks' => [2, 3, 4, 2, 4, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 2, 'hour' => 13, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],
            ['dow' => 2, 'hour' => 12, 'weeks' => [2, 3, 4, 2, 4, 5, 3, 4, 5, 4, 4, 3]],
            ['dow' => 2, 'hour' => 14, 'weeks' => [3, 4, 5, 2, 4, 5, 3, 3, 5, 4, 5, 3]],
            ['dow' => 3, 'hour' => 13, 'weeks' => [4, 5, 7, 3, 5, 7, 4, 5, 8, 5, 6, 4]],
            ['dow' => 3, 'hour' => 12, 'weeks' => [3, 4, 5, 2, 4, 5, 3, 4, 6, 4, 5, 3]],
            ['dow' => 3, 'hour' => 14, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 4, 'hour' => 13, 'weeks' => [4, 6, 7, 4, 6, 8, 5, 6, 8, 6, 7, 5]],
            ['dow' => 4, 'hour' => 12, 'weeks' => [3, 5, 5, 3, 5, 6, 4, 5, 6, 5, 5, 4]],
            ['dow' => 4, 'hour' => 14, 'weeks' => [3, 4, 5, 3, 5, 6, 4, 4, 6, 5, 5, 3]],
            ['dow' => 5, 'hour' => 13, 'weeks' => [3, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],
            ['dow' => 5, 'hour' => 12, 'weeks' => [2, 4, 4, 2, 4, 5, 3, 4, 5, 4, 4, 3]],
            ['dow' => 5, 'hour' => 14, 'weeks' => [2, 4, 5, 2, 4, 5, 3, 4, 5, 4, 5, 3]],
            ['dow' => 6, 'hour' => 13, 'weeks' => [6, 7, 9, 5, 7, 10, 6, 7, 10, 7, 8, 6]],
            ['dow' => 6, 'hour' => 12, 'weeks' => [5, 6, 7, 4, 6, 8, 5, 6, 8, 6, 6, 5]],
            ['dow' => 6, 'hour' => 14, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],

            // Cena
            ['dow' => 0, 'hour' => 20, 'weeks' => [4, 5, 7, 3, 6, 8, 4, 5, 9, 6, 7, 4]],
            ['dow' => 0, 'hour' => 19, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 7, 5, 5, 3]],
            ['dow' => 0, 'hour' => 21, 'weeks' => [2, 3, 4, 2, 4, 5, 2, 3, 5, 4, 4, 2]],
            ['dow' => 1, 'hour' => 20, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 8, 6, 6, 4]],
            ['dow' => 1, 'hour' => 19, 'weeks' => [3, 4, 5, 2, 4, 5, 3, 4, 6, 4, 5, 3]],
            ['dow' => 1, 'hour' => 21, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 2, 'hour' => 20, 'weeks' => [4, 5, 7, 3, 6, 8, 4, 5, 8, 6, 7, 4]],
            ['dow' => 2, 'hour' => 19, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 5, 5, 3]],
            ['dow' => 2, 'hour' => 21, 'weeks' => [2, 3, 5, 2, 4, 5, 2, 3, 5, 4, 4, 2]],
            ['dow' => 3, 'hour' => 20, 'weeks' => [5, 6, 7, 4, 6, 8, 5, 6, 9, 6, 7, 5]],
            ['dow' => 3, 'hour' => 19, 'weeks' => [3, 5, 5, 3, 5, 6, 4, 5, 7, 5, 5, 3]],
            ['dow' => 3, 'hour' => 21, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 4, 'hour' => 20, 'weeks' => [7, 8, 10, 6, 8, 11, 7, 8, 12, 9, 10, 7]],
            ['dow' => 4, 'hour' => 19, 'weeks' => [5, 6, 7, 4, 6, 8, 5, 6, 9, 7, 7, 5]],
            ['dow' => 4, 'hour' => 21, 'weeks' => [4, 5, 7, 3, 5, 7, 4, 5, 8, 6, 6, 4]],
            ['dow' => 5, 'hour' => 20, 'weeks' => [8, 9, 11, 7, 9, 12, 8, 9, 13, 10, 11, 8]],
            ['dow' => 5, 'hour' => 19, 'weeks' => [5, 7, 8, 5, 7, 9, 6, 7, 10, 7, 8, 5]],
            ['dow' => 5, 'hour' => 21, 'weeks' => [5, 6, 8, 4, 6, 8, 5, 6, 9, 7, 7, 5]],
            ['dow' => 6, 'hour' => 20, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 6, 'hour' => 19, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 6, 'hour' => 21, 'weeks' => [1, 2, 3, 1, 2, 4, 1, 2, 4, 2, 3, 1]],
        ];
    }

    private function branchProfiles(): array
    {
        // Sucursal Sopocachi: perfil más nocturno
        return [
            // Almuerzo (menos fuerte)
            ['dow' => 0, 'hour' => 13, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 0, 'hour' => 14, 'weeks' => [1, 2, 3, 1, 2, 4, 1, 2, 4, 2, 3, 1]],
            ['dow' => 1, 'hour' => 13, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 2, 'hour' => 13, 'weeks' => [2, 4, 4, 2, 4, 5, 3, 4, 5, 4, 4, 3]],
            ['dow' => 3, 'hour' => 13, 'weeks' => [3, 4, 5, 2, 4, 5, 3, 4, 6, 4, 5, 3]],
            ['dow' => 4, 'hour' => 13, 'weeks' => [3, 4, 5, 3, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 5, 'hour' => 13, 'weeks' => [2, 3, 4, 2, 3, 5, 2, 3, 5, 3, 4, 2]],
            ['dow' => 6, 'hour' => 13, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],

            // Cena (fuerte, sobre todo finde)
            ['dow' => 0, 'hour' => 20, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],
            ['dow' => 0, 'hour' => 21, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 0, 'hour' => 22, 'weeks' => [2, 3, 4, 1, 3, 4, 2, 3, 5, 3, 4, 2]],
            ['dow' => 1, 'hour' => 20, 'weeks' => [3, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 3]],
            ['dow' => 1, 'hour' => 21, 'weeks' => [2, 4, 5, 2, 4, 5, 3, 4, 5, 4, 5, 3]],
            ['dow' => 2, 'hour' => 20, 'weeks' => [4, 5, 6, 3, 5, 7, 4, 5, 7, 5, 6, 4]],
            ['dow' => 2, 'hour' => 21, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 3, 'hour' => 20, 'weeks' => [4, 5, 7, 3, 5, 7, 4, 5, 8, 6, 7, 4]],
            ['dow' => 3, 'hour' => 21, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 4, 'hour' => 20, 'weeks' => [6, 7, 9, 5, 7, 10, 6, 7, 10, 8, 9, 6]],
            ['dow' => 4, 'hour' => 21, 'weeks' => [5, 6, 8, 4, 6, 9, 5, 6, 9, 7, 8, 5]],
            ['dow' => 4, 'hour' => 22, 'weeks' => [3, 4, 6, 2, 4, 6, 3, 4, 7, 5, 5, 3]],
            ['dow' => 5, 'hour' => 20, 'weeks' => [7, 8, 10, 6, 8, 11, 7, 8, 12, 9, 10, 7]],
            ['dow' => 5, 'hour' => 21, 'weeks' => [6, 7, 9, 5, 7, 10, 6, 7, 10, 8, 9, 6]],
            ['dow' => 5, 'hour' => 22, 'weeks' => [4, 5, 7, 3, 5, 7, 4, 5, 8, 6, 7, 4]],
            ['dow' => 6, 'hour' => 20, 'weeks' => [3, 4, 5, 2, 4, 6, 3, 4, 6, 4, 5, 3]],
            ['dow' => 6, 'hour' => 21, 'weeks' => [2, 3, 4, 1, 3, 5, 2, 3, 5, 3, 4, 2]],
        ];
    }

    // ==================== SEEDING HELPERS ====================

    private function buildMenuBuckets(array $menuIds): array
    {
        $count = count($menuIds);
        if ($count >= 8) {
            return [
                'popular' => [$menuIds[0], $menuIds[1], $menuIds[2]],
                'medium'  => array_slice($menuIds, 3, 5),
                'other'   => array_slice($menuIds, 8),
            ];
        }
        return [
            'popular' => array_slice($menuIds, 0, min(3, $count)),
            'medium'  => array_slice($menuIds, min(3, $count)),
            'other'   => [],
        ];
    }

    private function seedSlotProfiles($restaurante, array $comensales, array $buckets, int $totalWeeks, array $profiles): int
    {
        $startDate = now()->subDays($totalWeeks * 7)->startOfDay();
        $startDow = (int) $startDate->format('N') - 1;
        $startDate->subDays($startDow);

        $total = 0;
        foreach ($profiles as $profile) {
            $dow = $profile['dow'];
            $hour = $profile['hour'];
            $weeklyCounts = $profile['weeks'];

            foreach ($weeklyCounts as $weekIdx => $count) {
                if ($count <= 0) continue;

                $date = (clone $startDate)->addDays($weekIdx * 7 + $dow)->setTime($hour, random_int(0, 59));
                if ($date->gt(now())) continue;

                for ($i = 0; $i < $count; $i++) {
                    $this->createOrderWithDetails(
                        $restaurante->id,
                        $comensales[array_rand($comensales)],
                        $date,
                        $buckets
                    );
                    $total++;
                }
            }
        }
        return $total;
    }

    private function seedAmbientNoise($restaurante, array $comensales, array $buckets, int $min, int $max): int
    {
        $total = random_int($min, $max);
        $created = 0;
        for ($i = 0; $i < $total; $i++) {
            $diaWeight = random_int(0, 100);
            $dia = $diaWeight < 60 ? random_int(0, 20) : ($diaWeight < 85 ? random_int(21, 45) : random_int(46, 84));
            $fecha = now()->subDays($dia);
            $dayOfWeek = (int) $fecha->format('w');

            $horaRand = random_int(0, 100);
            if ($horaRand < 42) {
                $fecha = $fecha->setTime(random_int(11, 14), random_int(0, 59));
            } elseif ($horaRand < 78) {
                $fecha = $fecha->setTime(random_int(19, 21), random_int(0, 59));
            } else {
                $fecha = $fecha->setTime(random_int(8, 22), random_int(0, 59));
            }

            $isWeekend = in_array($dayOfWeek, [0, 6]);
            $numItems = $isWeekend ? random_int(2, 4) : random_int(1, 3);

            $uniqueItems = [];
            for ($j = 0; $j < $numItems; $j++) {
                $roll = random_int(0, 100);
                if ($roll < 50) {
                    $pickId = $buckets['popular'][array_rand($buckets['popular'])];
                } elseif ($roll < 78) {
                    $pickId = $buckets['medium'][array_rand($buckets['medium'])];
                } else {
                    $pickId = !empty($buckets['other']) ? $buckets['other'][array_rand($buckets['other'])] : $buckets['medium'][array_rand($buckets['medium'])];
                }
                if (isset($uniqueItems[$pickId])) {
                    $uniqueItems[$pickId]['cantidad']++;
                } else {
                    $menu = Menu::find($pickId);
                    $uniqueItems[$pickId] = ['menu_id' => $pickId, 'cantidad' => 1, 'precio_unitario' => $menu ? $menu->precio : 0];
                }
            }

            $totalMonto = array_reduce($uniqueItems, fn($c, $d) => $c + ($d['cantidad'] * $d['precio_unitario']), 0);

            $pedidoId = DB::table('pedidos')->insertGetId([
                'restaurante_id' => $restaurante->id,
                'comensal_id'    => $comensales[array_rand($comensales)],
                'fecha_pedido'   => $fecha,
                'estado'         => 'completado',
                'total'          => $totalMonto,
                'created_at'     => $fecha,
                'updated_at'     => $fecha,
            ]);

            foreach ($uniqueItems as $d) {
                DB::table('detalle_pedido')->insert([
                    'pedido_id'      => $pedidoId,
                    'menu_id'        => $d['menu_id'],
                    'cantidad'       => $d['cantidad'],
                    'precio_unitario'=> $d['precio_unitario'],
                    'created_at'     => $fecha,
                    'updated_at'     => $fecha,
                ]);
            }
            $created++;
        }
        return $created;
    }

    private function createOrderWithDetails(int $restauranteId, int $comensalId, \Illuminate\Support\Carbon $fecha, array $buckets): void
    {
        $numItems = random_int(1, 3);
        $items = [];

        for ($j = 0; $j < $numItems; $j++) {
            $roll = random_int(0, 100);
            if ($roll < 50) {
                $pickId = $buckets['popular'][array_rand($buckets['popular'])];
            } elseif ($roll < 78) {
                $pickId = $buckets['medium'][array_rand($buckets['medium'])];
            } else {
                $pickId = !empty($buckets['other']) ? $buckets['other'][array_rand($buckets['other'])] : $buckets['medium'][array_rand($buckets['medium'])];
            }
            if (isset($items[$pickId])) {
                $items[$pickId]['cantidad']++;
            } else {
                $menu = Menu::find($pickId);
                $items[$pickId] = ['menu_id' => $pickId, 'cantidad' => 1, 'precio_unitario' => $menu ? $menu->precio : 0];
            }
        }

        $totalMonto = array_reduce($items, fn($c, $d) => $c + ($d['cantidad'] * $d['precio_unitario']), 0);

        $pedidoId = DB::table('pedidos')->insertGetId([
            'restaurante_id' => $restauranteId,
            'comensal_id'    => $comensalId,
            'fecha_pedido'   => $fecha,
            'estado'         => 'completado',
            'total'          => $totalMonto,
            'created_at'     => $fecha,
            'updated_at'     => $fecha,
        ]);

        foreach ($items as $d) {
            DB::table('detalle_pedido')->insert([
                'pedido_id'      => $pedidoId,
                'menu_id'        => $d['menu_id'],
                'cantidad'       => $d['cantidad'],
                'precio_unitario'=> $d['precio_unitario'],
                'created_at'     => $fecha,
                'updated_at'     => $fecha,
            ]);
        }
    }
}
