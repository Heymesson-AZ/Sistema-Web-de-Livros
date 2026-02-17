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
            // Sorteamos um pedido existente ou criamos um novo se não houver nenhum
            'pedido_id' => Pedido::inRandomOrder()->first()?->id ?? Pedido::factory(),
            // status de entrega
            'status' => $status,
            // Geramos um código de rastreamento realista
            'codigo_rastreamento' => strtoupper(fake()->bothify('??#########BR')),
            // Geramos uma URL de nota fiscal falsa
            'url_nota_fiscal' => fake()->url(),
            
            // Geramos datas de previsão e entrega realistas
            // A data de previsão de entrega é sempre no futuro, enquanto a data de entrega só é gerada se o status for 'entregue'
            'data_previsao_entrega' => fake()->dateTimeBetween('now', '+15 days'),
            'data_entrega' => $status === 'entregue' ? fake()->dateTimeBetween('-5 days', 'now') : null,


            // Se o status for 'entregue', geramos um nome e CPF do recebedor, caso contrário, deixamos nulos
            'nome_recebedor' => $status === 'entregue' ? fake()->name() : null,
            'cpf_recebedor' => $status === 'entregue' ? fake()->cpf() : null,
            // Geramos um valor de frete realista
            'valor_frete' => fake()->randomFloat(2, 10, 50),
            // Geramos um método de envio aleatório
            'metodo_envio' => fake()->randomElement(['correios', 'transportadora']),
        ];
    }
}
