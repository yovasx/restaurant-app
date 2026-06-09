<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Restaurante;
use App\Models\Comensal;
use App\Models\Resena;
use App\Models\Visita;
use App\Models\Menu;
use App\Models\Producto;
use App\Models\Promocion;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert(['id' => 1, 'nombre' => 'administrador', 'estado' => 'activo']);
        DB::table('roles')->insert(['id' => 2, 'nombre' => 'restaurante', 'estado' => 'activo']);

        $admin = Usuario::factory()->create(['rol_id' => 1]);
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_dashboard_shows_kpis(): void
    {
        $usuario = Usuario::factory()->create(['rol_id' => 2]);
        Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Test Rest',
            'telefono' => '555-0001',
            'estado' => 'activo',
        ]);
        Comensal::create([
            'nombre' => 'Test',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'test@comensal.com',
            'telefono' => '555-0002',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Restaurantes Activos');
        $response->assertSee('Comensales Activos');
    }

    public function test_dashboard_shows_audit_section(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Últimos eventos de auditoría');
    }

    public function test_dashboard_shows_alerts_section(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Atención Requerida');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        auth('admin')->logout();
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_rankings(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Top Restaurantes por Visitas');
        $response->assertSee('Distribución de Score');
    }

    public function test_dashboard_shows_activity_chart(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Actividad 30 días');
    }

    public function test_dashboard_chart_shows_with_seeded_data(): void
    {
        $usuario = Usuario::factory()->create(['rol_id' => 2]);
        $rest = Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Chart Test Rest',
            'telefono' => '555-0100',
            'estado' => 'activo',
        ]);
        $comensal = Comensal::create([
            'nombre' => 'Chart',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'chart@test.com',
            'telefono' => '555-0101',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);
        Visita::create([
            'restaurante_id' => $rest->id,
            'comensal_id' => $comensal->id,
            'fecha_visita' => now()->subDays(2)->toDateString(),
        ]);
        Visita::create([
            'restaurante_id' => $rest->id,
            'comensal_id' => $comensal->id,
            'fecha_visita' => now()->subDays(2)->toDateString(),
        ]);
        Visita::create([
            'restaurante_id' => $rest->id,
            'comensal_id' => $comensal->id,
            'fecha_visita' => now()->yesterday()->toDateString(),
        ]);

        $menu = Menu::create([
            'restaurante_id' => $rest->id,
            'nombre' => 'Chart Menu',
            'precio' => 100,
            'estado' => 'activo',
        ]);
        Resena::create([
            'menu_id' => $menu->id,
            'comensal_id' => $comensal->id,
            'nombre' => 'Tester',
            'score' => 4,
            'comentario' => 'Good',
        ]);
        Resena::create([
            'menu_id' => $menu->id,
            'comensal_id' => $comensal->id,
            'nombre' => 'Tester2',
            'score' => 5,
            'comentario' => 'Excelent',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Actividad 30 días');
        $response->assertSee('Visitas');
        $response->assertSee('Reseñas');
    }

    public function test_dashboard_renders_horizontal_bars(): void
    {
        $usuario = Usuario::factory()->create(['rol_id' => 2]);
        $rest = Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Bar Test',
            'telefono' => '555-0200',
            'estado' => 'activo',
        ]);
        $comensal = Comensal::create([
            'nombre' => 'Bar',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'bar@test.com',
            'telefono' => '555-0201',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);
        Visita::create([
            'restaurante_id' => $rest->id,
            'comensal_id' => $comensal->id,
            'fecha_visita' => now()->toDateString(),
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('bg-gradient-to-r from-indigo-400 to-indigo-600');
    }

    public function test_dashboard_shows_donuts(): void
    {
        $usuario = Usuario::factory()->create(['rol_id' => 2]);
        $rest = Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Donut Test',
            'telefono' => '555-0300',
            'estado' => 'activo',
        ]);

        $comensal = Comensal::create([
            'nombre' => 'Donut',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'donut@test.com',
            'telefono' => '555-0301',
            'password' => bcrypt('password'),
            'estado' => 'activo',
        ]);

        Visita::create([
            'restaurante_id' => $rest->id,
            'comensal_id' => $comensal->id,
            'fecha_visita' => now()->toDateString(),
        ]);

        $menu = Menu::create([
            'restaurante_id' => $rest->id,
            'nombre' => 'Donut Menu',
            'precio' => 100,
            'estado' => 'activo',
        ]);

        Resena::create([
            'menu_id' => $menu->id,
            'comensal_id' => $comensal->id,
            'nombre' => 'Donut Tester',
            'score' => 5,
            'comentario' => 'Great',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Distribución de Score');
        $response->assertSee('Promociones');
        $response->assertSee('Productos');
    }
}
