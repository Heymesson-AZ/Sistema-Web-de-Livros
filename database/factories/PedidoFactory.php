<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\Cupom;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_pedido' => 'PED-' . fake()->unique()->numerify('######'),
            'status' => fake()->randomElement(['pendente', 'processando', 'enviado', 'entregue', 'cancelado']),
            'total' => 0, 
            'data_pedido' => fake()->dateTimeBetween('-1 year', 'now'),

            // Seleciona um cliente e um vendedor aleatórios já existentes
            'cliente_id' => Cliente::inRandomOrder()->first()?->id ?? Cliente::factory(),
            'vendedor_id' => Vendedor::inRandomOrder()->first()?->id ?? Vendedor::factory(),
            
            'cupom_id' => fake()->boolean(50) ? Cupom::inRandomOrder()->first()?->id : null,
        ];
    }
}

