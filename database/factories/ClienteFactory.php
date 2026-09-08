<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClienteFactory extends Factory
{
    /**
     * Defindo o estado padrão do modelo Cliente.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $cpf = null;
        try {
            $cpf = fake('pt_BR')->unique()->cpf();
        } catch (\Throwable $e) {
            $n = fake()->numerify('###########');
            $cpf = substr($n, 0, 3) . '.' . substr($n, 3, 3) . '.' . substr($n, 6, 3) . '-' . substr($n, 9, 2);
        }

        return [
            // Garante que cada cliente tenha seu próprio User do tipo cliente
            'user_id' => User::factory()->cliente(),

            'cpf' => $cpf,
            'celular_contato' => fake('pt_BR')->phoneNumber(),
            'data_nascimento' => fake()->date('Y-m-d', '-18 years'), // Clientes maiores de 18
        ];
    }
}

/**
 * Explicação:
 * where('tipo', 'cliente'): "Ei banco, procure apenas por usuários que tenham o cargo de cliente. Não me traga administradores nem vendedores."
 *
 * whereDoesntHave('cliente'): "Desses usuários que você achou, filtre apenas os que ainda não possuem um registro na tabela de clientes.
 * Não quero duplicar o perfil de ninguém." (Isso evita erros de chave única).
 *
 * inRandomOrder(): "Agora, embaralhe esses usuários como se fosse um baralho de cartas."
 *
 * first()?->id: "Pegue o ID do primeiro que aparecer. Se não encontrar ninguém (o banco estiver vazio), não dê erro ainda."
 *
 * ?? \App\Models\User::factory()->cliente(): "Este é o plano B. Se você não encontrou nenhum usuário disponível lá em cima,
 * use a fábrica de usuários para criar um novo agora mesmo com o tipo cliente."
 */
