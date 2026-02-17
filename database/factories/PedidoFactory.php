<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Cupom;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Gera um número único para o pedido, ex: PED-123456
            'numero_pedido' => 'PED-' . fake()->unique()->numerify('######'),

            'status' => fake()->randomElement(['pendente', 'processando', 'enviado', 'entregue', 'cancelado']),

            'total' => 0, // O total será calculado posteriormente com base nos itens do pedido

            'data_pedido' => fake()->dateTimeBetween('-1 year', 'now'), // Pedidos do último ano

            // Sorteia um usuário do tipo cliente ou cria um novo
            'user_id' => User::where('tipo', 'cliente')->inRandomOrder()->first()?->id ?? User::factory()->cliente(),

            // Sorteia um cupom existente ou deixa nulo (50% de chance de ter cupom)
            'cupom_id' => fake()->boolean(50) ? Cupom::inRandomOrder()->first()?->id : null,
        ];
    }
}
