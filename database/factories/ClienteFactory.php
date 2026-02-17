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
        return [
            // Sorteia um User que seja do tipo 'cliente' e que ainda não tenha um perfil de cliente
            'user_id' => User::where('tipo', 'cliente')
                ->whereDoesntHave('cliente') // Garante 1 para 1
                ->inRandomOrder()
                ->first()?->id ?? User::factory()->cliente(),

            'cpf' => fake()->unique()->cpf(), // O Faker Laravel tem suporte a formatos PT-BR
            'telefone' => fake()->phoneNumber(),
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