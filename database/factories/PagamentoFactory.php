<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PagamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pendente', 'aprovado', 'rejeitado']);

        return [
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),
            'metodo_pagamento' => fake()->randomElement(['cartao_credito', 'boleto', 'pix', 'cartao_debito']),
            'status_pagamento' => $status,
            'id_transacao' => fake()->uuid(), // Simula o código do gateway
            'valor_pago' => fake()->randomFloat(2, 20, 500),
            'data_confirmacao_pagamento' => $status === 'aprovado' ? now() : null,
        ];
    }
}
