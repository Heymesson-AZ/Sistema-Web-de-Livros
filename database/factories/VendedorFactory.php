<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class VendedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Garante que cada vendedor tenha seu próprio User do tipo vendedor
            'user_id' => User::factory()->vendedor(),

            'cnpj' => fake()->unique()->cnpj(),
            'telefone_comercial' => fake()->phoneNumber(),
            'razao_social' => fake()->company() . ' Ltda',
            'nome_fantasia' => fake()->company(),
            'inscricao_estadual' => fake()->regexify('[0-9]{9,14}'),
            'status_aprovacao' => fake()->randomElement(['pendente', 'aprovado', 'rejeitado']),
        ];
    }
}
