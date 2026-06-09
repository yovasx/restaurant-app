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
use App\Services\Restaurante\RestaurantReportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantReportTest extends TestCase
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
            'nombre' => 'Chef Report',
        ]);

        $this->restaurante = Restaurante::create([
            'usuario_id' => $this->user->id,
            'nombre' => 'Restaurante Report Test',
            'telefono' => '555-9999',
            'email_reservas' => 'report@test.com',
            'direccion' => 'Av. Report 456',
            'zona' => 'Miraflores',
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

    public function test_authenticated_user_can_access_report_page(): void
    {
        $response = $this->get(route('restaurante.reportes.index'));
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_report_page(): void
    {
        auth('restaurante')->logout();
        $response = $this->get(route('restaurante.reportes.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_report_page_shows_filters(): void
    {
        $response = $this->get(route('restaurante.reportes.index'));
        $response->assertStatus(200);
        $response->assertSee('Desde');
        $response->assertSee('Hasta');
        $response->assertSee('Alcance');
        $response->assertSee('Sucursal');
        $response->assertSee('Exportar Excel');
        $response->assertSee('Exportar PDF');
    }

    public function test_report_page_shows_kpis(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Plato Report',
            'precio' => 25,
            'stock' => 10,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.reportes.index'));
        $response->assertStatus(200);
        $response->assertSee('Platos Activos');
        $response->assertSee('Promociones Activas');
        $response->assertSee('Visitas');
        $response->assertSee('Reseñas');
        $response->assertSee('Score Promedio');
    }

    public function test_report_page_shows_data_with_scope_sucursal_activa(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Solo Activo',
            'precio' => 20,
            'stock' => 3,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.reportes.index', ['scope' => 'sucursal_activa']));
        $response->assertStatus(200);
        $response->assertSee('Platos Activos');
    }

    public function test_report_page_shows_data_with_scope_todas_mis_sucursales(): void
    {
        $sucursal2 = Restaurante::create([
            'usuario_id' => $this->user->id,
            'nombre' => 'Sucursal 2 Report',
            'telefono' => '555-8888',
            'email_reservas' => 's2report@test.com',
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
            'nombre' => 'Plato S2 Report',
            'precio' => 30,
            'stock' => 5,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.reportes.index', ['scope' => 'todas_mis_sucursales']));
        $response->assertStatus(200);
        $response->assertSee('Platos Activos');
    }

    public function test_report_excludes_other_owner_branches(): void
    {
        $otherUser = Usuario::factory()->create(['rol_id' => 2, 'nombre' => 'Other']);
        $otherRestaurante = Restaurante::create([
            'usuario_id' => $otherUser->id,
            'nombre' => 'Other Branch',
            'telefono' => '555-7777',
            'email_reservas' => 'other@test.com',
            'direccion' => 'Other St',
            'zona' => 'Other',
            'latitud' => -16.5,
            'longitud' => -68.1,
            'estado' => 'activo',
            'es_principal' => true,
            'fecha_registro' => now()->toDateString(),
        ]);

        $response = $this->get(route('restaurante.reportes.index', ['restaurante_id' => $otherRestaurante->id]));
        $response->assertStatus(200);
        $response->assertDontSee('Other Branch');
    }

    public function test_report_page_filters_by_date(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Date Test',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Test',
            'email' => 'date@test.com',
            'telefono' => '555-0001',
            'password' => bcrypt('password'),
        ]);

        $menu = Menu::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Date Menu',
            'precio' => 15,
            'estado' => 'activo',
        ]);

        Resena::create([
            'comensal_id' => $comensal->id,
            'menu_id' => $menu->id,
            'score' => 4,
            'comentario' => 'Old review',
            'created_at' => now()->subDays(60),
        ]);

        $response = $this->get(route('restaurante.reportes.index', [
            'from' => now()->subDays(10)->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]));
        $response->assertStatus(200);
        $response->assertDontSee('Old review');
    }

    public function test_report_page_empty_state(): void
    {
        $this->restaurante->delete();

        $response = $this->get(route('restaurante.reportes.index'));
        $response->assertStatus(200);
        $response->assertSee('Sin datos en este rango');
    }

    public function test_service_generate_returns_correct_structure(): void
    {
        $service = app(RestaurantReportService::class);
        $data = $service->generate($this->user->id, ['scope' => 'sucursal_activa', 'restaurante_id' => $this->restaurante->id]);

        $this->assertArrayHasKey('filters', $data);
        $this->assertArrayHasKey('kpis', $data);
        $this->assertArrayHasKey('series', $data);
        $this->assertArrayHasKey('tables', $data);
        $this->assertArrayHasKey('breakdowns', $data);

        $this->assertArrayHasKey('visitas_por_dia', $data['series']);
        $this->assertArrayHasKey('resenas_por_dia', $data['series']);
        $this->assertArrayHasKey('promedio_score_por_dia', $data['series']);

        $this->assertArrayHasKey('top_platos_resenas', $data['tables']);
        $this->assertArrayHasKey('top_platos_score', $data['tables']);
        $this->assertArrayHasKey('productos_sin_stock', $data['tables']);

        $this->assertArrayHasKey('score_distribution', $data['breakdowns']);
        $this->assertArrayHasKey('promociones_por_estado', $data['breakdowns']);
        $this->assertArrayHasKey('productos_por_estado', $data['breakdowns']);
    }

    public function test_service_scope_respects_user_ownership(): void
    {
        $otherUser = Usuario::factory()->create(['rol_id' => 2, 'nombre' => 'Other User']);
        $otherRestaurante = Restaurante::create([
            'usuario_id' => $otherUser->id,
            'nombre' => 'Not Mine',
            'telefono' => '555-6666',
            'email_reservas' => 'notmine@test.com',
            'direccion' => 'Other St',
            'zona' => 'Other',
            'latitud' => -16.5,
            'longitud' => -68.1,
            'estado' => 'activo',
            'es_principal' => true,
            'fecha_registro' => now()->toDateString(),
        ]);

        Producto::create([
            'restaurante_id' => $otherRestaurante->id,
            'nombre' => 'Should Not Appear',
            'precio' => 50,
            'stock' => 5,
            'activo' => true,
        ]);

        $data = app(RestaurantReportService::class)->generate($this->user->id, [
            'scope' => 'sucursal_activa',
            'restaurante_id' => $otherRestaurante->id,
        ]);

        $this->assertEquals(0, $data['kpis']['productos_activos']);
    }

    public function test_excel_export_returns_ok(): void
    {
        config(['excel.temporary_files.local_path' => '/tmp/laravel-excel']);

        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'Export Product',
            'precio' => 10,
            'stock' => 5,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.reportes.export.excel'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_pdf_export_returns_ok(): void
    {
        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'PDF Product',
            'precio' => 10,
            'stock' => 5,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.reportes.export.pdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_excel_export_requires_auth(): void
    {
        auth('restaurante')->logout();
        $response = $this->get(route('restaurante.reportes.export.excel'));
        $response->assertRedirect(route('login'));
    }

    public function test_pdf_export_requires_auth(): void
    {
        auth('restaurante')->logout();
        $response = $this->get(route('restaurante.reportes.export.pdf'));
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_scope_selector(): void
    {
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Alcance');
        $response->assertSee('Sucursal activa');
        $response->assertSee('Todas mis sucursales');
    }

    public function test_dashboard_shows_activity_chart_section(): void
    {
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Actividad (30 días)');
    }

    public function test_dashboard_shows_top_platos_section(): void
    {
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Top Platos más Reseñados');
        $response->assertSee('Mejor Calificados');
    }

    public function test_dashboard_shows_donut_charts(): void
    {
        $response = $this->get(route('restaurante.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Distribución Score');
    }

    public function test_dashboard_todas_mis_sucursales_shows_combined_kpis(): void
    {
        $sucursal2 = Restaurante::create([
            'usuario_id' => $this->user->id,
            'nombre' => 'Sucursal 2 Dashboard',
            'telefono' => '555-4444',
            'email_reservas' => 's2dash@test.com',
            'direccion' => 'Av. 2 Dash',
            'zona' => 'Zona',
            'latitud' => -16.5,
            'longitud' => -68.1,
            'estado' => 'activo',
            'es_principal' => false,
            'fecha_registro' => now()->toDateString(),
        ]);

        Producto::create([
            'restaurante_id' => $this->restaurante->id,
            'nombre' => 'P1',
            'precio' => 10,
            'stock' => 5,
            'activo' => true,
        ]);
        Producto::create([
            'restaurante_id' => $sucursal2->id,
            'nombre' => 'P2',
            'precio' => 15,
            'stock' => 3,
            'activo' => true,
        ]);

        $response = $this->get(route('restaurante.dashboard', ['scope' => 'todas_mis_sucursales']));
        $response->assertStatus(200);
        $response->assertSee('Platos Activos');
    }
}
