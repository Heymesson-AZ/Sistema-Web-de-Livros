<?php

namespace Database\Seeders;

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
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. AS BASES (Dados mestre que não dependem de outros)
        Autor::factory(20)->create();
        Editora::factory(10)->create();
        Genero::factory(8)->create();
        Cupom::factory(5)->create();

        // 2. USUÁRIOS E PERFIS
        // Criamos os perfis (que por sua vez criam os Users vinculados via Factory)
        Cliente::factory(100)->create();
        Vendedor::factory(60)->create();
        Admin::factory(5)->create();

        // Usuário de Teste Pessoal / Admin Master
        $meuAdmin = User::updateOrCreate(
            ['email' => 'heymesson@teste.com'],
            [
                'name' => 'Heymesson',
                'tipo' => 'admin',
                'foto_perfil' => null,
                'password' => bcrypt('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Admin::updateOrCreate(
            ['user_id' => $meuAdmin->id],
            [
                'telefone_urgencia' => '(11) 99999-9999',
                'cargo' => Admin::CARGO_SUPER_ADMIN,
                'departamento' => Admin::DEPARTAMENTO_TECNOLOGIA,
            ]
        );

        // 3. ENDEREÇOS (Importante: Criar ANTES dos pedidos para o snapshot funcionar)
        Endereco::factory(150)->create();

        // 4. O CATÁLOGO
        Livro::factory(100)->create();

        // 5. LÓGICA DE PEDIDOS (Snapshot de Endereço e Cálculo de Total)
        Pedido::factory(40)->create()->each(function ($pedido) {
            // Criamos os itens (1 a 4 por pedido)
            $itens = PedidoItem::factory(fake()->numberBetween(1, 4))->create([
                'pedido_id' => $pedido->id,
            ]);

            // Calculamos o total real dos itens
            $valorTotal = $itens->sum(fn($item) => $item->quantidade_itens * $item->valor_unitario);

            // Atualizamos o cabeçalho do pedido
            $pedido->update(['total' => $valorTotal]);

            // Registramos o pagamento
            Pagamento::factory()->create([
                'pedido_id' => $pedido->id,
                'valor_pago' => $valorTotal,
            ]);

            // PULO DO GATO: Buscamos um endereço real do usuário dono do perfil cliente
            $enderecoReal = $pedido->cliente->user->enderecos()->inRandomOrder()->first();

            $dadosEntrega = ['pedido_id' => $pedido->id];

            if ($enderecoReal) {
                // Se houver endereço, "congelamos" os dados na tabela de entrega
                $dadosEntrega = array_merge($dadosEntrega, [
                    'rua'         => $enderecoReal->rua,
                    'bairro'      => $enderecoReal->bairro,
                    'cidade'      => $enderecoReal->cidade,
                    'estado'      => $enderecoReal->estado,
                    'cep'         => $enderecoReal->cep,
                    'pais'        => $enderecoReal->pais,
                    'complemento' => $enderecoReal->complemento,
                ]);
            }

            PedidoEntrega::factory()->create($dadosEntrega);
        });

        // 6. INTERAÇÕES SOCIAIS (Favoritos e Avaliações)
        $usuariosIds = User::pluck('id');
        $livrosIds = Livro::pluck('id');

        // Combinações únicas para evitar duplicidade em favoritos
        $usuariosIds->crossJoin($livrosIds)->shuffle()->take(30)->each(function ($par) {
            Favorito::create([
                'user_id'  => $par[0],
                'livro_id' => $par[1],
            ]);
        });

        Avaliacao::factory(15)->create();

        // 7. Preenchimento de Carrinhos (Lógica de Cabeçalho + Pivô)
        $clientes = Cliente::all();
        $livros = Livro::all();

        $clientes->each(function ($perfilCliente) use ($livros) {
            $quantidadeDeLivros = fake()->numberBetween(0, 5);

            if ($quantidadeDeLivros > 0) {
                // Cria ou obtém o carrinho do usuário (somente user_id)
                $carrinho = Carrinho::updateOrCreate(
                    ['user_id' => $perfilCliente->user_id],
                    ['updated_at' => now()]
                );

                // Sorteia os livros e associa na tabela pivô carrinho_livro
                $livrosSorteados = $livros->random($quantidadeDeLivros);

                $livrosSorteados->each(function ($livro) use ($carrinho) {
                    $carrinho->livros()->syncWithoutDetaching([
                        $livro->id => ['quantidade' => fake()->numberBetween(1, 3)]
                    ]);
                });
            }
        });

        // 8. CARTÕES SALVOS
        CartaoSalvo::factory(10)->create();
    }
}
