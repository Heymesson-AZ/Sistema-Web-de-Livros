<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class GeneroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->randomElement([
                'Ficção Científica',
                'Romance',
                'Terror',
                'Biografia',
                'História',
                'Autoajuda',
                'Fantasia',
                'Suspense',
                'Aventura',
                'Infantil',
            ]),
        ];
    }
}
