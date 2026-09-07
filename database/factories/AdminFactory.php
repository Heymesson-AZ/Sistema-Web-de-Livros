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
            // Garante que cada admin tenha seu próprio User do tipo admin
            'user_id' => User::factory()->admin(),

            // Sorteia um telefone formatado
            'telefone_urgencia' => fake()->cellphoneNumber(),

            // Sorteia um cargo e departamento usando os helpers do Model Admin
            'cargo' => fake()->randomElement(Admin::getCargos()),
            'departamento' => fake()->randomElement(Admin::getDepartamentos()),
        ];
    }
}
