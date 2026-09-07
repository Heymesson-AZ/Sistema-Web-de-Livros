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
        return [
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),
            'livro_id' => Livro::inRandomOrder()->first()?->id ?? Livro::factory(),
            'quantidade_itens' => fake()->numberBetween(1, 5),
            'valor_unitario' => fn (array $attributes) => Livro::find($attributes['livro_id'])?->preco ?? fake()->randomFloat(2, 20, 100),
        ];
    }
}
