<?php

namespace Database\Seeders;

// trazendo todos os caminho dos modelos para facilitar a criação dos registros
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Cupom;
use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\User;
use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\Pagamento;
use App\Models\PedidoEntrega;
use App\Models\Favorito;
use App\Models\Avaliacao;
use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\Livro;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. As Bases (Tabelas que não dependem de ninguém)
        //usando o caminho ja instaciado para criar os registros usando as factories
        $autores = Autor::factory(10)->create();
        $editoras = Editora::factory(5)->create();
        $generos = Genero::factory(8)->create();
        Cupom::factory(5)->create();

        // 2. Os Usuários e Perfis
        // Criamos clientes e vendedores (as factories cuidam de criar o User)
        Cliente::factory(15)->create();
        Vendedor::factory(5)->create();

        // Criar um admin fixo para você conseguir logar
        User::factory()->admin()->create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
        ]);

        // 3. O Catálogo
        Livro::factory(40)->create();

        // 4. A Lógica de Pedidos Realistas 💰
        Pedido::factory(20)->create()->each(function ($pedido) {
            // Para cada pedido, criamos entre 1 e 4 itens
            $itens = PedidoItem::factory(fake()->numberBetween(1, 4))->create([
                'pedido_id' => $pedido->id,
            ]);

            // Calculamos o total: Soma de (quantidade * valor_unitario)
            $valorTotal = $itens->sum(function ($item) {
                return $item->quantidade_itens * $item->valor_unitario;
            });

            // Atualizamos o total do pedido com o cálculo real
            $pedido->update(['total' => $valorTotal]);

            // Criamos o pagamento e a entrega para este pedido específico
            Pagamento::factory()->create([
                'pedido_id' => $pedido->id,
                'valor_pago' => $valorTotal,
            ]);

            PedidoEntrega::factory()->create([
                'pedido_id' => $pedido->id,
            ]);
        });

        // 5. Interações Sociais
        // 1. Pegamos todos os IDs disponíveis
        $usuariosIds = User::pluck('id');
        $livrosIds = Livro::pluck('id');

        // 2. Criamos uma coleção com todas as combinações possíveis e embaralhamos
        $combinacoes = $usuariosIds->crossJoin($livrosIds)->shuffle();

        // 3. Pegamos as primeiras 30 (ou quantas você quiser) e criamos os registros
        $combinacoes->take(30)->each(function ($par) {
            Favorito::create([
                'user_id' => $par[0],
                'livro_id' => $par[1],
            ]);
        });

    
        Avaliacao::factory(15)->create();
        Endereco::factory(20)->create();

        // 6. Preenchimento de Carrinhos (Etapa Separada) 🛒
        $clientes = User::where('tipo', 'cliente')->get();
        $livros = Livro::all();

        $clientes->each(function ($cliente) use ($livros) {
            // Sorteamos quantos livros este cliente terá no carrinho (de 0 a 5)
            $quantidadeDeLivros = fake()->numberBetween(0, 5);

            if ($quantidadeDeLivros > 0) {
                // Pegamos livros aleatórios para este cliente
                $livrosSorteados = $livros->random($quantidadeDeLivros);

                $livrosSorteados->each(function ($livro) use ($cliente) {
                    Carrinho::factory()->create([
                        'user_id' => $cliente->id,
                        'livro_id' => $livro->id,
                        'quantidade' => fake()->numberBetween(1, 3), // Até 3 unidades de cada
                    ]);
                });
            }
        });
    }
}
