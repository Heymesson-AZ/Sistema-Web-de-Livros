<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CupomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(['percentual', 'valor_fixo']);

        return [
            'codigo' => strtoupper(fake()->bothify('????##')), // Gera algo como 'ABCD12'
            'tipo_desconto' => $tipo,
            'valor_desconto' => $tipo === 'percentual' ? fake()->numberBetween(5, 50) : fake()->numberBetween(10, 100),
            'limite_uso' => fake()->numberBetween(10, 100),
            'validade_cupom' => fake()->dateTimeBetween('now', '+6 months'),
        ];
    }
}
