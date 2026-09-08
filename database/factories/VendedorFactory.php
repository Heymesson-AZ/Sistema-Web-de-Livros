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
        $cnpj = null;
        try {
            $cnpj = fake('pt_BR')->unique()->cnpj();
        } catch (\Throwable $e) {
            $n = fake()->numerify('##############');
            $cnpj = substr($n, 0, 2) . '.' . substr($n, 2, 3) . '.' . substr($n, 5, 3) . '/' . substr($n, 8, 4) . '-' . substr($n, 12, 2);
        }

        return [
            // Garante que cada vendedor tenha seu próprio User do tipo vendedor
            'user_id' => User::factory()->vendedor(),

            'cnpj' => $cnpj,
            'telefone_comercial' => fake('pt_BR')->phoneNumber(),
            'razao_social' => fake('pt_BR')->company() . ' Ltda',
            'nome_fantasia' => fake('pt_BR')->company(),
            'inscricao_estadual' => fake()->regexify('[0-9]{9,14}'),
            'status_aprovacao' => fake()->randomElement(['pendente', 'aprovado', 'rejeitado']),
        ];
    }
}
