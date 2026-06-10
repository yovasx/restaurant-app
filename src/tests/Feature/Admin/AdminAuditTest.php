<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Auditoria;
use App\Models\Usuario;
use App\Models\Comensal;
use App\Models\Restaurante;
use App\Models\Categoria;
use App\Services\Admin\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAuditTest extends TestCase
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

    public function test_logger_creates_audit_record(): void
    {
        $logger = app(AuditLogger::class);
        $audit = $logger->log('test_modulo', 'test_accion', 'Test', '1', 'Prueba de auditoría');

        $this->assertDatabaseHas('auditorias', [
            'id' => $audit->id,
            'modulo' => 'test_modulo',
            'accion' => 'test_accion',
        ]);
    }

    public function test_logger_stores_usuario_id(): void
    {
        $logger = app(AuditLogger::class);
        $audit = $logger->log('test', 'test', 'Test', null, 'test');

        $this->assertNotNull($audit->usuario_id);
    }

    public function test_logger_stores_antes_despues(): void
    {
        $logger = app(AuditLogger::class);
        $antes = ['estado' => 'activo'];
        $despues = ['estado' => 'inactivo'];
        $audit = $logger->log('test', 'test', 'Test', null, 'cambio estado', $antes, $despues);

        $this->assertEquals('activo', $audit->antes['estado']);
        $this->assertEquals('inactivo', $audit->despues['estado']);
    }

    public function test_logger_stores_meta(): void
    {
        $logger = app(AuditLogger::class);
        $meta = ['archivo' => 'test.txt'];
        $audit = $logger->log('test', 'test', 'Test', null, 'con meta', null, null, $meta);

        $this->assertEquals('test.txt', $audit->meta['archivo']);
        $this->assertArrayHasKey('ip', $audit->meta);
        $this->assertArrayHasKey('ruta', $audit->meta);
    }

    public function test_audit_page_requires_auth(): void
    {
        auth('admin')->logout();
        $response = $this->get(route('admin.auditoria.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_audit_page_shows_events(): void
    {
        $logger = app(AuditLogger::class);
        $logger->log('test_modulo', 'test_accion', 'TestEnt', '42', 'Evento de prueba visible');

        $response = $this->get(route('admin.auditoria.index'));
        $response->assertStatus(200);
        $response->assertSee('Evento de prueba visible');
        $response->assertSee('test_modulo');
    }

    public function test_dashboard_shows_latest_audits_section(): void
    {
        $logger = app(AuditLogger::class);
        $logger->log('backups', 'generar_backup', 'Backup', null, 'Backup desde test');

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Últimos eventos');
        $response->assertSee('Backup desde test');
    }

    public function test_audit_page_filters_by_modulo(): void
    {
        app(AuditLogger::class)->log('backups', 'generar_backup', 'Backup', null, 'Evento backups');
        app(AuditLogger::class)->log('reportes', 'exportar_excel', 'Reporte', null, 'Evento reportes');

        $response = $this->get(route('admin.auditoria.index', ['modulo' => 'backups']));
        $response->assertStatus(200);
        $response->assertSee('Evento backups');
        $response->assertDontSee('Evento reportes');
    }

    public function test_audit_page_filters_by_q(): void
    {
        app(AuditLogger::class)->log('backups', 'generar_backup', 'Backup', null, 'Búsqueda única');
        app(AuditLogger::class)->log('reportes', 'exportar_excel', 'Reporte', null, 'Otro evento');

        $response = $this->get(route('admin.auditoria.index', ['q' => 'Búsqueda']));
        $response->assertStatus(200);
        $response->assertSee('Búsqueda única');
        $response->assertDontSee('Otro evento');
    }

    public function test_audit_page_filters_by_entidad(): void
    {
        app(AuditLogger::class)->log('backups', 'generar_backup', 'Backup', '1', 'Backup event');
        app(AuditLogger::class)->log('reportes', 'exportar_excel', 'Reporte', '1', 'Report event');

        $response = $this->get(route('admin.auditoria.index', ['entidad' => 'Backup']));
        $response->assertStatus(200);
        $response->assertSee('Backup event');
        $response->assertDontSee('Report event');
    }

    public function test_audit_page_filters_by_entidad_id(): void
    {
        app(AuditLogger::class)->log('backups', 'generar_backup', 'Backup', '42', 'Backup #42');
        app(AuditLogger::class)->log('backups', 'generar_backup', 'Backup', '99', 'Backup #99');

        $response = $this->get(route('admin.auditoria.index', ['entidad_id' => '42']));
        $response->assertStatus(200);
        $response->assertSee('Backup #42');
        $response->assertDontSee('Backup #99');
    }

    public function test_audit_model_cambios_returns_diff(): void
    {
        $logger = app(AuditLogger::class);
        $antes = ['estado' => 'activo', 'telefono' => '555-111'];
        $despues = ['estado' => 'inactivo', 'telefono' => '555-222'];
        $audit = $logger->log('test', 'test', 'Test', null, 'cambios', $antes, $despues);

        $this->assertCount(2, $audit->cambios());
        $this->assertEquals('activo', $audit->cambios()[0]['antes']);
        $this->assertEquals('inactivo', $audit->cambios()[0]['despues']);
    }

    public function test_audit_model_cambios_empty_when_no_diff(): void
    {
        $logger = app(AuditLogger::class);
        $audit = $logger->log('test', 'test', 'Test', null, 'sin cambios');

        $this->assertEmpty($audit->cambios());
    }

    public function test_audit_model_accion_label_returns_friendly_name(): void
    {
        $logger = app(AuditLogger::class);
        $audit = $logger->log('test', 'generar_backup', 'Test', null, 'test');
        $this->assertEquals('Generar backup', $audit->accionLabel());

        $audit2 = $logger->log('test', 'login_exitoso', 'Test', null, 'test');
        $this->assertEquals('Inicio de sesión', $audit2->accionLabel());
    }

    public function test_admin_login_exitoso_creates_audit(): void
    {
        Usuario::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => 1,
        ]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post('/login', [
                'email' => 'admin@test.com',
                'password' => 'password',
                'login_type' => 'admin',
            ]);

        $this->assertDatabaseHas('auditorias', [
            'modulo' => 'auth',
            'accion' => 'login_exitoso',
        ]);
    }

    public function test_admin_login_fallido_creates_audit(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post('/login', [
                'email' => 'admin@test.com',
                'password' => 'wrong_password',
                'login_type' => 'admin',
            ]);

        $this->assertDatabaseHas('auditorias', [
            'modulo' => 'auth',
            'accion' => 'login_fallido',
        ]);
    }

    public function test_admin_logout_creates_audit(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post(route('logout.admin'));

        $this->assertDatabaseHas('auditorias', [
            'modulo' => 'auth',
            'accion' => 'logout',
        ]);
    }
}
