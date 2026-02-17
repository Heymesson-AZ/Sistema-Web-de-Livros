<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor>
 */
class AutorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'nacionalidade' => fake()->country(),
            'data_nascimento' => fake()->date('Y-m-d', '-20 years'), // Autores com mais de 20 anos
            'email' => fake()->unique()->safeEmail(),
            'biografia' => fake()->paragraph(), // Gera um texto curto
            'foto_perfil' => 'autores/default.png', // Simulando um caminho de arquivo
        ];
    }
}
