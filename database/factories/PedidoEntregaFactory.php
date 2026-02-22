<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PedidoEntregaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pendente', 'em_transito', 'entregue', 'cancelada']);

        return [
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),
            'status' => $status,
            'codigo_rastreamento' => strtoupper(fake()->bothify('??#########BR')),
            'url_nota_fiscal' => fake()->url(),

            // Dados de Endereço "Congelados"
            'rua' => fake()->streetName(),
            'bairro' => fake()->word(),
            'cidade' => fake()->city(),
            'estado' => strtoupper(fake()->lexify('??')), // ex: SP, RJ
            'cep' => fake()->postcode(),
            'pais' => 'Brasil',
            'complemento' => fake()->secondaryAddress(),

            'data_previsao_entrega' => fake()->dateTimeBetween('now', '+15 days'),
            'data_entrega' => $status === 'entregue' ? fake()->dateTimeBetween('-5 days', 'now') : null,
            'nome_recebedor' => $status === 'entregue' ? fake()->name() : null,
            'cpf_recebedor' => $status === 'entregue' ? fake()->cpf() : null,
            'valor_frete' => fake()->randomFloat(2, 10, 50),
            'metodo_envio' => fake()->randomElement(['correios', 'transportadora']),
        ];
    }
}
