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
        static $generos = [
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
            'Poesia',
            'Drama',
            'Quadrinhos',
            'Tecnologia',
            'Filosofia',
            'Psicologia',
        ];

        $nome = !empty($generos) ? array_shift($generos) : fake()->unique()->words(2, true);

        return [
            'nome' => ucfirst($nome),
        ];
    }
}
