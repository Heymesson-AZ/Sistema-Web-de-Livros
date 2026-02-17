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
        return [
            // Sorteia quem está avaliando, quem está sendo avaliado e qual foi o pedido
            // Tenta pegar um vendedor, cliente e pedido aleatório. Se não encontrar, cria um novo.
            //usando a chamada do use no topo, não precisa usar o caminho completo
            'vendedor_id' => Vendedor::inRandomOrder()->first()?->id ?? Vendedor::factory(),
            'cliente_id' => Cliente::inRandomOrder()->first()?->id ?? Cliente::factory(),
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),

            'avaliacao' => fake()->numberBetween(1, 5),
            'comentario' => fake()->optional()->sentence(),
            'recomenda' => fake()->boolean(80), // 80% de chance de ser true
        ];
    }
}
