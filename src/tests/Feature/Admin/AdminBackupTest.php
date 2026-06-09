<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Admin\DatabaseBackupService;

class AdminBackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert(['id' => 1, 'nombre' => 'administrador', 'estado' => 'activo']);

        $admin = Usuario::factory()->create(['rol_id' => 1]);
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_access_backups_page(): void
    {
        $response = $this->get(route('admin.backups.index'));
        $response->assertStatus(200);
    }

    public function test_backups_page_shows_backup_list(): void
    {
        $response = $this->get(route('admin.backups.index'));
        $response->assertSee('Descargar');
        $response->assertSee('.dump');
    }

    public function test_unauthenticated_user_cannot_access_backups(): void
    {
        auth('admin')->logout();

        $response = $this->get(route('admin.backups.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_download_nonexistent_backup_returns_404(): void
    {
        $response = $this->get(route('admin.backups.download', 'no-existe.dump'));
        $response->assertStatus(404);
    }

    public function test_generate_backup_route_requires_auth(): void
    {
        auth('admin')->logout();

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post(route('admin.backups.generate'));
        $response->assertRedirect(route('login'));
    }

    public function test_generate_backup_success(): void
    {
        $this->mock(DatabaseBackupService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn([
                    'dump' => 'laravel_db_test.dump',
                    'sql'  => 'laravel_db_test.sql.gz',
                    'cloud' => false,
                ]);
        });

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post(route('admin.backups.generate'));
        $response->assertRedirect(route('admin.backups.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('success', fn ($msg) => str_contains($msg, 'laravel_db_test.dump'));
    }

    public function test_generate_backup_cloud_upload_message(): void
    {
        $this->mock(DatabaseBackupService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn([
                    'dump' => 'laravel_db_test.dump',
                    'sql'  => 'laravel_db_test.sql.gz',
                    'cloud' => true,
                ]);
        });

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post(route('admin.backups.generate'));
        $response->assertRedirect(route('admin.backups.index'));
        $response->assertSessionHas('success', fn ($msg) => str_contains($msg, 'Subidos a la nube'));
    }

    public function test_generate_backup_error(): void
    {
        $this->mock(DatabaseBackupService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new \RuntimeException('pg_dump falló'));
        });

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post(route('admin.backups.generate'));
        $response->assertRedirect(route('admin.backups.index'));
        $response->assertSessionHas('error', fn ($msg) => str_contains($msg, 'pg_dump falló'));
    }
}
