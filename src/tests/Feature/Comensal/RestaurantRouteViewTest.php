<?php

namespace Tests\Feature\Comensal;

use App\Models\Restaurante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantRouteViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_page_shows_compact_how_to_arrive_cta_and_modal_when_restaurant_has_coordinates(): void
    {
        $restaurante = Restaurante::create([
            'nombre' => 'Ruta Bistro',
            'telefono' => '77712345',
            'direccion' => 'Av. Montenegro 123',
            'zona' => 'Zona Sur',
            'latitud' => -16.5321,
            'longitud' => -68.0842,
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->get(route('restaurante.show', $restaurante->id));

        $response->assertOk();
        $response->assertSeeText('Cómo llegar');
        $response->assertSee('data-modal-open="restaurant-route-modal"', false);
        $response->assertSee('id="restaurant-route-modal"', false);
        $response->assertSeeText('Ver indicaciones');
        $response->assertSee('restaurantRouteMap', false);
    }

    public function test_detail_page_shows_fallback_message_when_restaurant_has_no_coordinates(): void
    {
        $restaurante = Restaurante::create([
            'nombre' => 'Ruta Pendiente',
            'telefono' => '77754321',
            'direccion' => 'Calle 21 de Calacoto 45',
            'zona' => 'Calacoto',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->get(route('restaurante.show', $restaurante->id));

        $response->assertOk();
        $response->assertSeeText('Ubicación pendiente');
        $response->assertDontSeeText('Trazar ruta desde mi ubicación');
        $response->assertDontSee('data-modal-open="restaurant-route-modal"', false);
    }
}
