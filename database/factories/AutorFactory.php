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
            'nome' => $this->faker->name(),
            'nacionalidade' => $this->faker->country(),
            'data_nascimento' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            'biografia' => $this->faker->text(200),
        ];
    }
}
