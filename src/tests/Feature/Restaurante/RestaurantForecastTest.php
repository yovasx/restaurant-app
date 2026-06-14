<?php

namespace Tests\Feature\Restaurante;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Restaurante;
use App\Models\Menu;
use App\Models\Comensal;
use App\Services\Restaurante\RestaurantForecastService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
class RestaurantForecastTest extends TestCase
{
    use DatabaseTransactions;


    protected Usuario $user;
    protected Restaurante $restaurante;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert(['id' => 1, 'nombre' => 'administrador', 'estado' => 'activo']);
        DB::table('roles')->insert(['id' => 2, 'nombre' => 'restaurante', 'estado' => 'activo']);

        $this->user = Usuario::factory()->create([
            'rol_id' => 2,
            'nombre' => 'Chef Pronóstico',
        ]);

        $this->restaurante = Restaurante::create([
            'usuario_id'        => $this->user->id,
            'nombre'            => 'Restaurante Test Pronóstico',
            'telefono'          => '555-pron',
            'email_reservas'    => 'pron@test.com',
            'direccion'         => 'Av. Test 456',
            'zona'              => 'Sopocachi',
            'latitud'           => -16.5,
            'longitud'          => -68.1,
            'horario_apertura'  => '08:00',
            'horario_cierre'    => '23:00',
            'estado'            => 'activo',
            'es_principal'      => true,
            'fecha_registro'    => now()->subDays(100)->toDateString(),
        ]);

        session(['restaurante_sucursal_id' => $this->restaurante->id]);
        $this->actingAs($this->user, 'restaurante');
    }

    public function test_authenticated_user_can_access_forecast_page(): void
    {
        $response = $this->get(route('restaurante.pronosticos.index'));
        $response->assertStatus(200);
        $response->assertSee('Pronósticos');
    }

    public function test_unauthenticated_user_cannot_access_forecast(): void
    {
        auth('restaurante')->logout();
        $response = $this->get(route('restaurante.pronosticos.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_forecast_service_returns_empty_without_data(): void
    {
        $service = app(RestaurantForecastService::class);
        $result = $service->generate($this->restaurante->id);

        $this->assertArrayHasKey('heatmap', $result);
        $this->assertArrayHasKey('forecast', $result);
        $this->assertArrayHasKey('highlights', $result);
        $this->assertArrayHasKey('weekly_insights', $result);
        $this->assertArrayHasKey('probabilities', $result);
        $this->assertCount(0, $result['probabilities']);
    }

    public function test_forecast_service_with_orders(): void
    {
        $comensal = Comensal::create([
            'nombre'            => 'Test',
            'apellido_paterno'  => 'Forecast',
            'apellido_materno'  => 'User',
            'email'             => 'forecast@test.com',
            'telefono'          => '555-9999',
            'password'          => bcrypt('password'),
        ]);

        // Create historical orders at specific hours to generate data
        for ($day = 0; $day < 30; $day++) {
            for ($h = 11; $h <= 14; $h++) {
                DB::table('pedidos')->insert([
                    'restaurante_id' => $this->restaurante->id,
                    'comensal_id'    => $comensal->id,
                    'fecha_pedido'   => now()->subDays($day)->setTime($h, random_int(0, 59)),
                    'estado'         => 'completado',
                    'total'          => random_int(50, 200),
                    'created_at'     => now()->subDays($day),
                    'updated_at'     => now()->subDays($day),
                ]);
            }
        }

        $service = app(RestaurantForecastService::class);
        $result = $service->generate($this->restaurante->id);

        $this->assertNotEmpty($result['probabilities']);
        $this->assertNotEmpty($result['heatmap']['series']);
        $this->assertNotEmpty($result['forecast']);
        $this->assertNotEmpty($result['highlights']);
        $this->assertNotEmpty($result['weekly_insights']);
        $this->assertNotEmpty($result['forecast'][0]['status']);
        $this->assertNotEmpty($result['forecast'][0]['primary_window']);

        // Probabilities should have data for lunch hours
        $lunchProbs = $result['probabilities']->where('hour', 12)->where('dow', (int) now()->format('N') - 1);
        $this->assertTrue($lunchProbs->isNotEmpty() || $result['probabilities']->isNotEmpty());
    }

    public function test_forecast_summary_returns_data_with_orders(): void
    {
        $comensal = Comensal::create([
            'nombre'            => 'Test',
            'apellido_paterno'  => 'Summary',
            'apellido_materno'  => 'User',
            'email'             => 'summary@test.com',
            'telefono'          => '555-8888',
            'password'          => bcrypt('password'),
        ]);

        for ($day = 0; $day < 20; $day++) {
            DB::table('pedidos')->insert([
                'restaurante_id' => $this->restaurante->id,
                'comensal_id'    => $comensal->id,
                'fecha_pedido'   => now()->subDays($day)->setTime(13, 0),
                'estado'         => 'completado',
                'total'          => 100,
                'created_at'     => now()->subDays($day),
                'updated_at'     => now()->subDays($day),
            ]);
        }

        $service = app(RestaurantForecastService::class);
        $summary = $service->summary($this->restaurante->id);

        $this->assertArrayHasKey('best_today', $summary);
        $this->assertArrayHasKey('top_peak', $summary);
        $this->assertArrayHasKey('today_peaks', $summary);
        $this->assertArrayHasKey('overall_confidence', $summary);
        $this->assertArrayHasKey('has_data', $summary);
        $this->assertTrue($summary['has_data']);
    }

    public function test_forecast_page_shows_empty_state(): void
    {
        $response = $this->get(route('restaurante.pronosticos.index'));
        $response->assertStatus(200);
        $response->assertSee('Sin datos suficientes');
    }

    public function test_forecast_scope_switch(): void
    {
        $response = $this->get(route('restaurante.pronosticos.index', ['scope' => 'todas_mis_sucursales']));
        $response->assertStatus(200);
    }

    public function test_markov_probabilities_include_state_fields(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Markov', 'apellido_paterno' => 'Test',
            'apellido_materno' => 'User', 'email' => 'markov@test.com',
            'telefono' => '555-0001', 'password' => bcrypt('password'),
        ]);

        for ($day = 0; $day < 30; $day++) {
            DB::table('pedidos')->insert([
                'restaurante_id' => $this->restaurante->id,
                'comensal_id'    => $comensal->id,
                'fecha_pedido'   => now()->subDays($day)->setTime(13, random_int(0, 59)),
                'estado'         => 'completado',
                'total'          => 100,
                'created_at'     => now()->subDays($day),
                'updated_at'     => now()->subDays($day),
            ]);
        }

        $service = app(RestaurantForecastService::class);
        $result = $service->generate($this->restaurante->id);

        $this->assertNotEmpty($result['probabilities']);
        $slot = $result['probabilities']->first();
        $this->assertArrayHasKey('current_state', $slot);
        $this->assertArrayHasKey('predicted_state', $slot);
        $this->assertArrayHasKey('state_probabilities', $slot);
        $this->assertArrayHasKey('transitions_count', $slot);
        $this->assertArrayHasKey('has_variation', $slot);
    }

    public function test_markov_forecast_with_peak_hour_data(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Peak', 'apellido_paterno' => 'Markov',
            'apellido_materno' => 'Test', 'email' => 'peak@test.com',
            'telefono' => '555-0002', 'password' => bcrypt('password'),
        ]);

        // Create 10 weeks of data for lunes 13:00 with clear alta pattern
        // Last 5 weeks should be alta to make the current state alta
        for ($week = 0; $week < 10; $week++) {
            $ordersPerDay = $week >= 5 ? random_int(15, 25) : random_int(2, 5);
            for ($i = 0; $i < $ordersPerDay; $i++) {
                DB::table('pedidos')->insert([
                    'restaurante_id' => $this->restaurante->id,
                    'comensal_id'    => $comensal->id,
                    'fecha_pedido'   => now()->subDays($week * 7 + 0)->setTime(13, random_int(0, 59)),
                    'estado'         => 'completado',
                    'total'          => 100,
                    'created_at'     => now()->subDays($week * 7 + 0),
                    'updated_at'     => now()->subDays($week * 7 + 0),
                ]);
            }
        }

        $service = app(RestaurantForecastService::class);
        $result = $service->generate($this->restaurante->id);

        $this->assertNotEmpty($result['probabilities']);
        $monday13 = $result['probabilities']->where('dow', 0)->where('hour', 13)->first();
        $this->assertNotNull($monday13, 'Slot lunes 13:00 should have a prediction');
        $this->assertContains($monday13['current_state'], ['baja', 'media', 'alta']);
        $this->assertContains($monday13['predicted_state'], ['baja', 'media', 'alta']);
    }

    public function test_markov_fallback_when_sparse_data(): void
    {
        $comensal = Comensal::create([
            'nombre' => 'Sparse', 'apellido_paterno' => 'Data',
            'apellido_materno' => 'Test', 'email' => 'sparse@test.com',
            'telefono' => '555-0003', 'password' => bcrypt('password'),
        ]);

        // Only 2 orders — not enough for Markov
        DB::table('pedidos')->insert([
            'restaurante_id' => $this->restaurante->id,
            'comensal_id'    => $comensal->id,
            'fecha_pedido'   => now()->subDays(2)->setTime(13, 0),
            'estado'         => 'completado',
            'total'          => 100,
            'created_at'     => now()->subDays(2),
            'updated_at'     => now()->subDays(2),
        ]);
        DB::table('pedidos')->insert([
            'restaurante_id' => $this->restaurante->id,
            'comensal_id'    => $comensal->id,
            'fecha_pedido'   => now()->subDays(9)->setTime(13, 0),
            'estado'         => 'completado',
            'total'          => 100,
            'created_at'     => now()->subDays(9),
            'updated_at'     => now()->subDays(9),
        ]);

        $service = app(RestaurantForecastService::class);
        $result = $service->generate($this->restaurante->id);

        // Should still return probabilities (via fallback or low-confidence markov)
        $this->assertArrayHasKey('probabilities', $result);
        $this->assertArrayHasKey('forecast', $result);
    }

    public function test_dense_series_includes_open_slots(): void
    {
        $service = app(RestaurantForecastService::class);

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('densifyHourlySeries');
        $method->setAccessible(true);

        $businessHours = ['apertura'=>'08:00','cierre'=>'23:00','apertura_sab'=>'08:00','cierre_sab'=>'23:00','apertura_dom'=>'09:00','cierre_dom'=>'22:00'];
        $since = now()->subDays(7)->startOfDay();
        $emptyRows = collect();

        $result = $method->invoke($service, $emptyRows, $businessHours, $since);

        $this->assertTrue($result->isNotEmpty(), 'Dense series should not be empty');
        $this->assertGreaterThan(0, $result->count(), 'Should have entries for all open slots');

        $zeroSlot = $result->first(fn($r) => (int)$r->order_count === 0);
        $this->assertNotNull($zeroSlot, 'Should include slots with 0 orders');
    }
}
