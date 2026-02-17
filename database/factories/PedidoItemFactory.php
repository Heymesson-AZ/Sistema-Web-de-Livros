<?php

namespace Database\Factories;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Livro;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PedidoItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Primeiro sorteamos um livro
        $livro = Livro::inRandomOrder()->first() ?? Livro::factory()->create();

        return [
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),
            'livro_id' => $livro->id,
            'quantidade_itens' => fake()->numberBetween(1, 5),
            'valor_unitario' => $livro->preco, // Usamos o preço real do livro sorteado 💰
        ];
    }
}
