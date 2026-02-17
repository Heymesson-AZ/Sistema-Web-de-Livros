<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),

            'tipo' => fake()->randomElement(['cliente', 'vendedor', 'admin']),
            'status' => fake()->randomElement(['ativo', 'inativo', 'banido']),

            'remember_token' => Str::random(10),
        ];
    }

    /**
     * indica que o email do usuário não foi verificado, definindo o campo 'email_verified_at' como null.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // Adicionando métodos para criar usuários do tipo 'admin'
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'admin',
        ]);
    }
    // Adicionando um método para criar usuários do tipo 'vendedor'
    public function vendedor(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'vendedor',
        ]);
    }

    public function cliente(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'cliente',
        ]);
    }
}
