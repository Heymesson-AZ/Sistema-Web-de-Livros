<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CartaoSalvoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'token_cartao' => fake()->sha256(), // Simula o token seguro do gateway
            'ultimos_digitos' => fake()->numerify('####'),
            'bandeira_cartao' => fake()->randomElement(['Visa', 'Mastercard', 'Elo', 'American Express']),
            'cartao_padrao' => false, // Podemos criar um state para tornar padrão se precisar
        ];
    }
}
