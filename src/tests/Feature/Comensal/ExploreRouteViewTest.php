<?php

namespace Tests\Feature\Comensal;

use App\Models\Categoria;
use App\Models\Comensal;
use App\Models\Restaurante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreRouteViewTest extends TestCase
{
    use RefreshDatabase;

    private function comensal(): Comensal
    {
        return Comensal::create([
            'nombre' => 'Ana',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'A',
            'email' => 'ana@test.com',
            'password' => bcrypt('password'),
            'telefono' => '77710001',
            'estado' => 'activo',
        ]);
    }

    public function test_explore_page_shows_route_elements(): void
    {
        $c = $this->comensal();
        Restaurante::create([
            'nombre' => 'Ruta Centro',
            'telefono' => '77711111',
            'direccion' => 'Calle Mercado 50',
            'zona' => 'Centro',
            'latitud' => -16.4965,
            'longitud' => -68.1376,
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar'));

        $response->assertOk();
        $response->assertSee('id="desktopRoutePanel"', false);
        $response->assertSee('id="exploreRouteModal"', false);
        $response->assertSee('id="mobileRouteMap"', false);
        $response->assertSeeText('Ver indicaciones');
        $response->assertSeeText('Cómo llegar');
        $response->assertSeeText('Ruta Centro');
    }

    public function test_explore_page_shows_route_chip_only_for_restaurants_with_coordinates(): void
    {
        $c = $this->comensal();
        Restaurante::create([
            'nombre' => 'Con Coordenadas',
            'telefono' => '77722222',
            'direccion' => 'Av. 6 de Agosto',
            'zona' => 'Sopocachi',
            'latitud' => -16.5033,
            'longitud' => -68.1200,
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        Restaurante::create([
            'nombre' => 'Sin Coordenadas',
            'telefono' => '77733333',
            'direccion' => 'Zona Desconocida',
            'zona' => 'Desconocida',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar'));

        $response->assertOk();
        $response->assertSeeText('Con Coordenadas');
        $response->assertSeeText('Sin Coordenadas');
        $response->assertSeeText('Cómo llegar');
        $response->assertSee('open-route', false);
    }

    public function test_explore_page_shows_categories(): void
    {
        $c = $this->comensal();
        $cat = Categoria::create([
            'nombre_categoria' => 'Pizza',
            'descripcion' => 'Pizzerías',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar'));

        $response->assertOk();
        $response->assertSeeText('Pizza');
        $response->assertSeeText('Todas');
        $response->assertSee('cat-chip', false);
    }

    public function test_explore_page_filters_by_query(): void
    {
        $c = $this->comensal();
        Restaurante::create([
            'nombre' => 'Pizza Centro',
            'telefono' => '77744444',
            'direccion' => 'Calle 1',
            'zona' => 'Centro',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);
        Restaurante::create([
            'nombre' => 'Sushi Bar',
            'telefono' => '77755555',
            'direccion' => 'Calle 2',
            'zona' => 'Sopocachi',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar', ['q' => 'Pizza']));

        $response->assertOk();
        $response->assertSeeText('Pizza Centro');
        $response->assertDontSeeText('Sushi Bar');
    }

    public function test_explore_page_filters_by_categoria(): void
    {
        $c = $this->comensal();
        $cat = Categoria::create(['nombre_categoria' => 'Pizza', 'descripcion' => '', 'estado' => 'activo']);

        $r1 = Restaurante::create([
            'nombre' => 'Pizza Place',
            'telefono' => '77766666',
            'direccion' => 'Av. 1',
            'zona' => 'Centro',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);
        $r2 = Restaurante::create([
            'nombre' => 'Sushi Place',
            'telefono' => '77777777',
            'direccion' => 'Av. 2',
            'zona' => 'Sopocachi',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);
        $r1->categorias()->attach($cat->id);

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar', ['categoria' => $cat->id]));

        $response->assertOk();
        $response->assertSeeText('Pizza Place');
        $response->assertDontSeeText('Sushi Place');
    }

    public function test_explore_page_always_renders_base_restaurants(): void
    {
        $c = $this->comensal();
        Restaurante::create([
            'nombre' => 'Base Restaurant',
            'telefono' => '77788888',
            'direccion' => 'Av. Siempre Viva',
            'zona' => 'Centro',
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);

        // List should appear without geolocation dependency
        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar'));

        $response->assertOk();
        $response->assertSeeText('Base Restaurant');
    }

    public function test_explore_page_shows_quick_filters_and_advanced_modal(): void
    {
        $c = $this->comensal();

        $response = $this->actingAs($c, 'comensal')->get(route('comensal.explorar'));

        $response->assertOk();
        $response->assertSee('quick-filter', false);
        $response->assertSee('Cerca de ti', false);
        $response->assertSeeText('Abiertos ahora');
        $response->assertSeeText('Económicos');
        $response->assertSee('id="advMaxDistance"', false);
        $response->assertSee('id="advZone"', false);
        $response->assertSee('id="advOpenNow"', false);
        $response->assertSeeText('Limpiar');
        $response->assertSee('id="clearFilters"', false);
    }
}
