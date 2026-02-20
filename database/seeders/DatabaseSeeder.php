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
use App\Models\CartaoSalvo;
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
        Autor::factory(10)->create();
        Editora::factory(5)->create();
        Genero::factory(8)->create();
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

        // 6. Preenchimento de Carrinhos 
        $clientes = User::where('tipo', 'cliente')->get();
        $livros = Livro::all();


        // Para cada cliente, vamos criar um carrinho e adicionar livros aleatórios
        // o each () e um método de coleção do Laravel que itera sobre cada item da coleção e executa a função fornecida
        $clientes->each(function ($cliente) use ($livros) {
            // Sorteamos quantos livros este cliente terá no carrinho (de 0 a 5)
            $quantidadeDeLivros = fake()->numberBetween(0, 5);

            if ($quantidadeDeLivros > 0) {
                // 1. Criamos UM carrinho para este cliente
                $carrinho = Carrinho::factory()->create([
                    'user_id' => $cliente->id,
                ]);

                // 2. Sorteamos os livros que entrarão NESSE carrinho
                $livrosSorteados = $livros->random($quantidadeDeLivros);

                // 3. Conectamos os livros ao carrinho (Tabela carrinho_livro)
                $livrosSorteados->each(function ($livro) use ($carrinho) {
                    // Usamos o método attach() para preencher a tabela intermediária
                    $carrinho->livros()->attach($livro->id, [
                        'quantidade' => fake()->numberBetween(1, 3),
                    ]);
                });
            }
        });

        // 7. Cartões Salvos (Etapa Separada) 
        CartaoSalvo::factory(10)->create();
    }
}
