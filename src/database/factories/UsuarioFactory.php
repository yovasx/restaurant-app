<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'telefono' => fake()->phoneNumber(),
            'estado' => 'activo',
            'rol_id' => 2,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => 1,
        ]);
    }
}
