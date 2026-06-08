<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Restaurante;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminReportTest extends TestCase
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

    public function test_admin_can_access_reports_page(): void
    {
        $response = $this->get(route('admin.reportes.index'));
        $response->assertStatus(200);
    }

    public function test_reports_page_shows_filters(): void
    {
        $response = $this->get(route('admin.reportes.index'));
        $response->assertSee('Desde');
        $response->assertSee('Hasta');
        $response->assertSee('Score');
        $response->assertSee('Exportar Excel');
        $response->assertSee('Exportar PDF');
    }

    public function test_reports_page_filters_by_date(): void
    {
        $response = $this->get(route('admin.reportes.index', [
            'from' => '2026-01-01',
            'to' => '2026-12-31',
        ]));
        $response->assertStatus(200);
    }

    public function test_reports_page_filters_by_estado_restaurante(): void
    {
        $response = $this->get(route('admin.reportes.index', [
            'estado_restaurante' => 'activo',
        ]));
        $response->assertStatus(200);
    }

    public function test_reports_page_filters_by_score(): void
    {
        $response = $this->get(route('admin.reportes.index', [
            'score' => 4,
        ]));
        $response->assertStatus(200);
    }

    public function test_reports_page_filters_by_restaurante_id(): void
    {
        $usuario = Usuario::factory()->create(['rol_id' => 2]);
        $restaurante = Restaurante::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Test Restaurant',
            'telefono' => '555-1234',
            'estado' => 'activo',
        ]);

        $response = $this->get(route('admin.reportes.index', [
            'restaurante_id' => $restaurante->id,
        ]));
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_reports(): void
    {
        auth('admin')->logout();

        $response = $this->get(route('admin.reportes.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        auth('admin')->logout();

        $user = Usuario::factory()->create(['rol_id' => 2]);
        $this->actingAs($user, 'restaurante');

        $response = $this->get(route('admin.reportes.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_excel_export_returns_ok(): void
    {
        $response = $this->get(route('admin.reportes.export.excel'));
        $response->assertStatus(200);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_excel_export_with_score_filter(): void
    {
        $response = $this->get(route('admin.reportes.export.excel', [
            'score' => 5,
        ]));
        $response->assertStatus(200);
    }

    public function test_pdf_export_returns_ok(): void
    {
        $response = $this->get(route('admin.reportes.export.pdf'));
        $response->assertStatus(200);
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_pdf_export_with_filters(): void
    {
        $response = $this->get(route('admin.reportes.export.pdf', [
            'from' => '2026-01-01',
            'to' => '2026-12-31',
            'estado_restaurante' => 'activo',
            'score' => 3,
        ]));
        $response->assertStatus(200);
    }
}
