<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Cria um User do tipo 'admin' automaticamente se um user_id não for passado
            'user_id' => User::factory()->create(['tipo' => 'admin'])->id,

            // Sorteia um telefone formatado
            'telefone_urgencia' => fake()->cellphoneNumber(),

            // Sorteia um cargo e departamento usando os helpers do Model Admin
            'cargo' => fake()->randomElement(Admin::getCargos()),
            'departamento' => fake()->randomElement(Admin::getDepartamentos()),
        ];
    }
}
