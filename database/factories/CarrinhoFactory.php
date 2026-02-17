<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarrinhoFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Sorteia um cliente existente ou cria um novo
            'user_id' => User::where('tipo', 'cliente')->inRandomOrder()->first()?->id ?? User::factory()->cliente(),

            // Sorteia um livro do catálogo
            'livro_id' => Livro::inRandomOrder()->first()?->id ?? Livro::factory(),

            // Quantidade de 1 a 3 unidades do mesmo livro
            'quantidade' => fake()->numberBetween(1, 3),
        ];
    }
}
