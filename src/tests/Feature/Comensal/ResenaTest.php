<?php

namespace Tests\Feature\Comensal;

use App\Models\Comensal;
use App\Models\Menu;
use App\Models\Resena;
use App\Models\Restaurante;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ResenaTest extends TestCase
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

    private function restaurante(): Restaurante
    {
        return Restaurante::create([
            'nombre' => 'Test Restaurant',
            'descripcion' => 'A test restaurant',
            'telefono' => '77710002',
            'direccion' => 'Calle 1',
            'zona' => 'Centro',
            'latitud' => -16.4965,
            'longitud' => -68.1376,
            'estado' => 'activo',
            'fecha_registro' => now()->toDateString(),
        ]);
    }

    public function test_comensal_can_create_restaurant_review_without_visita(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 5,
            'comentario' => 'Excelente restaurante, muy recomendado.',
        ]);

        $response->assertRedirect(route('restaurante.show', $r->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'score' => 5,
            'comentario' => 'Excelente restaurante, muy recomendado.',
            'menu_id' => null,
        ]);
    }

    public function test_comensal_can_edit_existing_review(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'score' => 3,
            'comentario' => 'Regular.',
        ]);

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 4,
            'comentario' => 'Mejor de lo que recordaba.',
        ]);

        $response->assertRedirect(route('restaurante.show', $r->id));

        $this->assertDatabaseCount('resenas', 1);
        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'score' => 4,
            'comentario' => 'Mejor de lo que recordaba.',
        ]);
    }

    public function test_review_requires_score(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'comentario' => 'Sin puntuación.',
        ]);

        $response->assertSessionHasErrors('score');
    }

    public function test_detail_page_shows_rating_and_reviews(): void
    {
        $r = $this->restaurante();

        $c1 = $this->comensal();
        $c2 = Comensal::create([
            'nombre' => 'Luis',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'B',
            'email' => 'luis@test.com',
            'password' => bcrypt('password'),
            'telefono' => '77710003',
            'estado' => 'activo',
        ]);

        Resena::create([
            'comensal_id' => $c1->id,
            'restaurante_id' => $r->id,
            'score' => 5,
            'comentario' => 'Increíble!',
        ]);
        Resena::create([
            'comensal_id' => $c2->id,
            'restaurante_id' => $r->id,
            'score' => 4,
            'comentario' => 'Muy bueno.',
        ]);

        $response = $this->actingAs($c1, 'comensal')->get(route('restaurante.show', $r->id));

        $response->assertOk();
        $response->assertSeeText('Increíble!');
        $response->assertSeeText('Muy bueno');
        $response->assertSeeText('4.5'); // avg
        $response->assertSeeText('2 reseñas');
        $response->assertSeeText('Editar mi reseña');
    }

    public function test_detail_page_shows_leave_review_button_for_unreviewed_restaurant(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();

        $response = $this->actingAs($c, 'comensal')->get(route('restaurante.show', $r->id));

        $response->assertOk();
        $response->assertSeeText('Dejar reseña');
        $response->assertDontSeeText('Editar mi reseña');
    }

    public function test_restaurant_can_see_review_in_their_panel(): void
    {
        $r = $this->restaurante();
        $c = $this->comensal();

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'score' => 5,
            'comentario' => 'Espectacular!',
        ]);

        $roleId = DB::table('roles')->insertGetId(['nombre' => 'Restaurante']);
        $user = Usuario::create([
            'nombre' => 'Dueño',
            'email' => 'dueno@test.com',
            'password' => bcrypt('password'),
            'telefono' => '77710004',
            'rol_id' => $roleId,
        ]);
        $r->usuario_id = $user->id;
        $r->save();

        $response = $this->actingAs($user, 'restaurante')->get(route('restaurante.resenas'));

        $response->assertOk();
        $response->assertSeeText('Espectacular!');
    }

    public function test_admin_can_see_restaurant_review(): void
    {
        $r = $this->restaurante();
        $c = $this->comensal();

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'score' => 4,
            'comentario' => 'Buena atención.',
        ]);

        $roleId = DB::table('roles')->insertGetId(['nombre' => 'Admin']);
        $admin = Usuario::create([
            'nombre' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'telefono' => '77710005',
            'rol_id' => $roleId,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_top_platos_resenados_still_works(): void
    {
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Top',
            'precio' => 25,
            'estado' => 'activo',
        ]);
        $c = $this->comensal();

        Resena::create([
            'comensal_id' => $c->id,
            'menu_id' => $menu->id,
            'score' => 5,
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('restaurante.show', $r->id));
        $response->assertOk();
    }

    public function test_comensal_can_create_dish_review(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Rico',
            'precio' => 30,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 4,
            'comentario' => 'Muy buen plato.',
            'menu_id' => $menu->id,
        ]);

        $response->assertRedirect(route('restaurante.show', $r->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'menu_id' => $menu->id,
            'score' => 4,
            'comentario' => 'Muy buen plato.',
        ]);
    }

    public function test_comensal_can_edit_dish_review(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Edit',
            'precio' => 35,
            'estado' => 'activo',
        ]);

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'menu_id' => $menu->id,
            'score' => 2,
            'comentario' => 'No me gustó.',
        ]);

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 5,
            'comentario' => 'Ahora sí me gustó.',
            'menu_id' => $menu->id,
        ]);

        $response->assertRedirect(route('restaurante.show', $r->id));

        $this->assertDatabaseCount('resenas', 1);
        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'menu_id' => $menu->id,
            'score' => 5,
            'comentario' => 'Ahora sí me gustó.',
        ]);
    }

    public function test_dish_review_does_not_override_restaurant_review(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Test',
            'precio' => 40,
            'estado' => 'activo',
        ]);

        // Create restaurant review first
        $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 3,
            'comentario' => 'Restaurante ok.',
        ]);

        // Create dish review
        $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 5,
            'comentario' => 'Plato excelente.',
            'menu_id' => $menu->id,
        ]);

        $this->assertDatabaseCount('resenas', 2);
        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'menu_id' => null,
            'score' => 3,
        ]);
        $this->assertDatabaseHas('resenas', [
            'comensal_id' => $c->id,
            'menu_id' => $menu->id,
            'score' => 5,
        ]);
    }

    public function test_detail_page_shows_menu_review_buttons_for_authenticated_user(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Visible',
            'precio' => 45,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('restaurante.show', $r->id));

        $response->assertOk();
        $response->assertSeeText('Plato Visible');
        $response->assertSeeText('45.00 Bs.');
        $response->assertSeeText('Reseñar plato');
    }

    public function test_detail_page_shows_edit_menu_review_button_when_already_reviewed(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Revisado',
            'precio' => 50,
            'estado' => 'activo',
        ]);

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'menu_id' => $menu->id,
            'score' => 4,
            'comentario' => 'Buen plato.',
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('restaurante.show', $r->id));

        $response->assertOk();
        $response->assertSeeText('Editar reseña');
    }

    public function test_detail_page_shows_dish_reviews_in_reviews_section(): void
    {
        $r = $this->restaurante();
        $menu = Menu::create([
            'restaurante_id' => $r->id,
            'nombre' => 'Plato Delicia',
            'precio' => 55,
            'estado' => 'activo',
        ]);
        $c = $this->comensal();

        Resena::create([
            'comensal_id' => $c->id,
            'restaurante_id' => $r->id,
            'menu_id' => $menu->id,
            'score' => 5,
            'comentario' => 'Delicioso plato.',
        ]);

        $response = $this->actingAs($c, 'comensal')->get(route('restaurante.show', $r->id));

        $response->assertOk();
        $response->assertSeeText('Delicioso plato.');
        $response->assertSeeText('Plato Delicia');
        $response->assertSeeText('Reseñó el plato');
    }

    public function test_review_for_nonexistent_menu_fails_validation(): void
    {
        $c = $this->comensal();
        $r = $this->restaurante();

        $response = $this->actingAs($c, 'comensal')->post(route('comensal.resena.save', $r->id), [
            'score' => 5,
            'menu_id' => 99999,
        ]);

        $response->assertSessionHasErrors('menu_id');
    }
}
