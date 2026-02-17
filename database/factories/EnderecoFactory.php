<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EnderecoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'rua' => fake()->streetName(), //
        'bairro' => fake()->words(2, true), // Ex: "Jardim das Flores"
        'cidade' => fake()->city(),
        'estado' => fake()->stateAbbr(), // Gera 'SP', 'RJ', etc.
        'cep' => fake()->postcode(), // Gera um CEP realista
        'pais' => 'Brasil', // Pode ser fixo, já que estamos focando no Brasil
        'complemento' => fake()->optional()->secondaryAddress(), // Ex: "Casa", "Apto 101", ou null
        // Sorteia um usuário existente para o endereço
        'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
    ];
}
}
