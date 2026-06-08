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
}
