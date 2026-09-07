<?php

namespace Database\Factories;
use \App\Models\Vendedor;
use \App\Models\Cliente;
use \App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Avaliacao>
 */
class AvaliacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pedido = Pedido::doesntHave('avaliacoes')->inRandomOrder()->first();

        return [
            'pedido_id' => $pedido?->id ?? Pedido::factory(),
            'vendedor_id' => fn (array $attributes) => Pedido::find($attributes['pedido_id'])?->vendedor_id ?? Vendedor::inRandomOrder()->first()?->id ?? Vendedor::factory(),
            'cliente_id' => fn (array $attributes) => Pedido::find($attributes['pedido_id'])?->cliente_id ?? Cliente::inRandomOrder()->first()?->id ?? Cliente::factory(),
            'avaliacao' => fake()->numberBetween(1, 5),
            'comentario' => fake()->optional()->sentence(),
            'recomenda' => fake()->boolean(80), // 80% de chance de ser true
        ];
    }
}
