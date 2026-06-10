<?php

namespace Tests\Feature\Restaurante;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Restaurante;
use App\Models\Producto;
use App\Models\Menu;
use App\Models\Promocion;
use App\Models\Visita;
use App\Models\Resena;
use App\Models\Comensal;
use App\Models\Categoria;
use App\Services\Restaurante\RestaurantDashboardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $user;
    protected Restaurante $restaurante;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert(['id' => 1, 'nombre' => 'administrador', 'estado' => 'activo']);
        DB::table('roles')->insert(['id' => 2, 'nombre' => 'restaurante', 'estado' => 'activo']);

        $this->user = Usuario::factory()->create([
            'rol_id' => 2,
            'nombre' => 'Chef Test',
        ]);

        $this->restaurante = Restaurante::create([
            'usuario_id' => $this->user->id,
            'nombre' => 'Restaurante Test',
            'telefono' => '555-1234',
            'email_reservas' => 'reservas@test.com',
            'direccion' => 'Av. Test 123',
            'zona' => 'Sopocachi',
            'latitud' => -16.5,
            'longitud' => -68.1,
            'horario_apertura' => '09:00',
            'horario_cierre' => '22:00',
            'estado' => 'activo',
            'es_principal' => true,
            'fecha_registro' => now()->toDateString(),
        ]);

        session(['restaurante_sucursal_id' => $this->restaurante->id]);

        $this->actingAs($this->user, 'restaurante');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        auth('restaurante')->logout();
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_kpis(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Plato Test',
            'precio' => 25.00,
            'stock' => 10,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Platos Activos');
        $response->assertSee('1');
        $response->assertSee('Promociones');
        $response->assertSee('Visitas');
        $response->assertSee('Score Prom');
    }

    public function test_dashboard_kpis_are_correct(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Activo',
            'precio' => 10,
            'stock' => 5,
            'activo' => true,
        ]);
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Sin Stock',
            'precio' => 15,
            'stock' => 0,
            'activo' => true,
        ]);

        Promocion::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Promo Test',
            'tipo' => 'descuento',
            'valor' => 10,
            'estado' => 'activo',
            'fecha_inicio' => now()->subDay()->toDateString(),
            'fecha_fin' => now()->addMonth()->toDateString(),
        ]);

        Visita::create([
            'restaurante_id' => $this->restaurante->id,
            'comensal_id' => Comensal::create([
                'nombre' => 'Test',
                'apellido_paterno' => 'User',
                'apellido_materno' => 'Test',
                'email' => 'test' . uniqid() . '@comensal.com',
                'telefono' => '555-0001',
                'password' => bcrypt('password'),
            ])->id,
            'fecha_visita' => now()->subDay()->toDateString(),
            'metodo' => 'geolocalizacion',
        ]);

        Menu::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Menu Test',
            'precio' => 30,
            'estado' => 'activo',
        ]);

        $service = app(RestaurantDashboardService::class);
        $data = $service->generate($this->restaurante->id);

        $this->assertEquals(2, $data['kpis']['platosActivos']['current']);
        $this->assertEquals(1, $data['kpis']['sinStock']['current']);
        $this->assertEquals(1, $data['kpis']['promocionesActivas']['current']);
        $this->assertEquals(1, $data['kpis']['visitas']['current']);
    }

    public function test_dashboard_shows_alerts_when_applicable(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Sin Stock',
            'precio' => 10,
            'stock' => 0,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Alertas');
        $response->assertSee('Productos sin stock');
    }

    public function test_dashboard_shows_menu_table(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Milanesa',
            'precio' => 45,
            'stock' => 5,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Gestión de Menú');
        $response->assertSee('Milanesa');
    }

    public function test_dashboard_shows_empty_state_when_no_data(): void
    {
        $this->restaurante->delete();

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Gestión de Menú');
        $response->assertSee('Aún no tienes platos agregados');
    }

    public function test_dashboard_shows_latest_reviews_section(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Test',
            'email' => 'carlos@test.com',
            'telefono' => '555-0002',
            'password' => bcrypt('password'),
        ]);

        $menu = Menu::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Plato Top',
            'precio' => 35,
            'estado' => 'activo',
        ]);

        Resena::create([
            'comensal_id' => $comensal->id,
            'menu_id' => $menu->id,
            'score' => 5,
            'comentario' => 'Excelente plato',
        ]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Últimas Reseñas');
        $response->assertSee('Excelente plato');
    }

    public function test_dashboard_shows_score_distribution(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Test',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'dist@test.com',
            'telefono' => '555-0003',
            'password' => bcrypt('password'),
        ]);
        $menu = Menu::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Plato',
            'precio' => 20,
            'estado' => 'activo',
        ]);

        Resena::create(['comensal_id' => $comensal->id, 'menu_id' => $menu->id, 'score' => 5]);
        Resena::create(['comensal_id' => $comensal->id, 'menu_id' => $menu->id, 'score' => 3]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Distribución de Score');
        $response->assertSee('5');
        $response->assertSee('3');
    }

    public function test_dashboard_shows_promo_summary(): void
    {
        Promocion::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Oferta',
            'tipo' => 'descuento',
            'estado' => 'activo',
            'fecha_inicio' => now()->subDay()->toDateString(),
            'fecha_fin' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Promociones');
        $response->assertSee('Activas');
    }

    public function test_service_returns_empty_for_nonexistent_restaurant(): void
    {
        $service = app(RestaurantDashboardService::class);
        $data = $service->generate(9999);

        $this->assertEquals(0, $data['kpis']['platosActivos']['current']);
        $this->assertEmpty($data['alerts']);
        $this->assertNull($data['branchSummary']);
    }

    public function test_dashboard_respects_active_branch(): void
    {
        $sucursal2 = Restaurante::create([
            'usuario_id' => $this->user->id,
            'nombre' => 'Sucursal 2',
            'telefono' => '555-5678',
            'email_reservas' => 's2@test.com',
            'direccion' => 'Av. 2',
            'zona' => 'Zona Sur',
            'latitud' => -16.5,
            'longitud' => -68.1,
            'estado' => 'activo',
            'es_principal' => false,
            'fecha_registro' => now()->toDateString(),
        ]);

        Producto::create([
            'restaurante_id' => $sucursal2->id,
            'nombre' => 'Plato S2',
            'precio' => 30,
            'stock' => 5,
            'activo' => true,
        ]);

        session(['restaurante_sucursal_id' => $sucursal2->id]);

        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Plato S2');
        $response->assertSee('Sucursal 2');
    }
}
