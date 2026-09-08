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
use Illuminate\Support\Facades\Hash;
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
        Cliente::factory(50)->create();
        Vendedor::factory(30)->create();
        Admin::factory(5)->create();

        // Super Admin Principal Solicitado
        $superAdmin = User::updateOrCreate(
            ['email' => 'heymesson.az2017@gmail.com'],
            [
                'name' => 'Heymesson Azevêdo',
                'tipo' => 'admin',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('50672253'),
                'email_verified_at' => now(),
            ]
        );

        Admin::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'telefone_urgencia' => '(11) 99999-9999',
                'cargo' => Admin::CARGO_SUPER_ADMIN,
                'departamento' => Admin::DEPARTAMENTO_TECNOLOGIA,
            ]
        );

        // Usuário de Teste Admin Secundário
        $adminTeste = User::updateOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Administrador do Sistema',
                'tipo' => 'admin',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Admin::updateOrCreate(
            ['user_id' => $adminTeste->id],
            [
                'telefone_urgencia' => '(11) 98888-7777',
                'cargo' => Admin::CARGO_ADMINISTRADOR,
                'departamento' => Admin::DEPARTAMENTO_OPERACIONAL,
            ]
        );

        // Usuário de Teste Cliente
        $meuCliente = User::updateOrCreate(
            ['email' => 'cliente@teste.com'],
            [
                'name' => 'Cliente Teste',
                'tipo' => 'cliente',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Cliente::updateOrCreate(
            ['user_id' => $meuCliente->id],
            [
                'cpf' => '111.222.333-44',
                'celular_contato' => '(11) 98888-8888',
                'data_nascimento' => '1995-05-15',
            ]
        );

        // Usuário de Teste Vendedor (APROVADO)
        $vendedorAprovado = User::updateOrCreate(
            ['email' => 'vendedor@teste.com'],
            [
                'name' => 'Livraria Universo (Aprovado)',
                'tipo' => 'vendedor',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Vendedor::updateOrCreate(
            ['user_id' => $vendedorAprovado->id],
            [
                'cnpj' => '12.345.678/0001-90',
                'razao_social' => 'Livraria Universo LTDA',
                'nome_fantasia' => 'Livraria Universo',
                'inscricao_estadual' => '123456789',
                'telefone_comercial' => '(11) 97777-7777',
                'status_aprovacao' => 'aprovado',
            ]
        );

        // Usuário de Teste Vendedor (PENDENTE DE APROVAÇÃO)
        $vendedorPendente = User::updateOrCreate(
            ['email' => 'vendedor.pendente@teste.com'],
            [
                'name' => 'Livraria Saber Novo (Pendente)',
                'tipo' => 'vendedor',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Vendedor::updateOrCreate(
            ['user_id' => $vendedorPendente->id],
            [
                'cnpj' => '98.765.432/0001-10',
                'razao_social' => 'Livraria Saber Novo Comércio ME',
                'nome_fantasia' => 'Saber Novo Livros',
                'inscricao_estadual' => '987654321',
                'telefone_comercial' => '(11) 96666-5555',
                'status_aprovacao' => 'pendente',
            ]
        );

        // Usuário de Teste Vendedor (REJEITADO)
        $vendedorRejeitado = User::updateOrCreate(
            ['email' => 'vendedor.rejeitado@teste.com'],
            [
                'name' => 'Livraria Alpha (Rejeitado)',
                'tipo' => 'vendedor',
                'status' => 'ativo',
                'foto_perfil' => null,
                'password' => Hash::make('suasenha123'),
                'email_verified_at' => now(),
            ]
        );

        Vendedor::updateOrCreate(
            ['user_id' => $vendedorRejeitado->id],
            [
                'cnpj' => '55.444.333/0001-22',
                'razao_social' => 'Alpha Distribuidora de Livros EIRELI',
                'nome_fantasia' => 'Livraria Alpha',
                'inscricao_estadual' => '554443332',
                'telefone_comercial' => '(11) 95555-4444',
                'status_aprovacao' => 'rejeitado',
            ]
        );

        // 3. ENDEREÇOS (Importante: Criar ANTES dos pedidos para o snapshot funcionar)
        Endereco::factory(150)->create();

        // 4. O CATÁLOGO
        Livro::factory(100)->create();

        // 5. LÓGICA DE PEDIDOS (Snapshot de Endereço e Cálculo de Total)
        Pedido::factory(40)->create()->each(function ($pedido) {
            // Buscamos livros do mesmo vendedor do pedido (ou criamos se não houver)
            $livrosVendedor = Livro::where('vendedor_id', $pedido->vendedor_id)->get();
            if ($livrosVendedor->isEmpty()) {
                $livrosVendedor = Livro::factory(2)->create(['vendedor_id' => $pedido->vendedor_id]);
            }

            // Criamos os itens (1 a 3 itens distintos pertencentes a este vendedor)
            $quantidadeItens = min(fake()->numberBetween(1, 3), $livrosVendedor->count());
            $livrosSorteados = $livrosVendedor->random($quantidadeItens);

            $itens = collect();
            foreach ($livrosSorteados as $livro) {
                $itens->push(PedidoItem::factory()->create([
                    'pedido_id' => $pedido->id,
                    'livro_id' => $livro->id,
                    'valor_unitario' => $livro->preco,
                ]));
            }

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
                    'numero'      => $enderecoReal->numero,
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

        // Combinações únicas para evitar duplicidade em favoritos (sem crossJoin pesado em memória)
        if ($usuariosIds->isNotEmpty() && $livrosIds->isNotEmpty()) {
            $paresFavoritos = [];
            $tentativas = 0;
            while (count($paresFavoritos) < 30 && $tentativas < 300) {
                $tentativas++;
                $uId = $usuariosIds->random();
                $lId = $livrosIds->random();
                $chave = "{$uId}-{$lId}";
                if (!isset($paresFavoritos[$chave])) {
                    $paresFavoritos[$chave] = true;
                    Favorito::create([
                        'user_id'  => $uId,
                        'livro_id' => $lId,
                    ]);
                }
            }
        }

        // Avaliações de pedidos reais (garantindo que cliente e vendedor batem com o pedido e sem duplicação)
        $pedidosParaAvaliar = Pedido::inRandomOrder()->take(15)->get();
        foreach ($pedidosParaAvaliar as $pedido) {
            Avaliacao::factory()->create([
                'pedido_id'   => $pedido->id,
                'cliente_id'  => $pedido->cliente_id,
                'vendedor_id' => $pedido->vendedor_id,
            ]);
        }

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
