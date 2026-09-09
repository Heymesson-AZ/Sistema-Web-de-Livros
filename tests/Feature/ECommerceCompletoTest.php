<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Avaliacao;
use App\Models\Carrinho;
use App\Models\CartaoSalvo;
use App\Models\Cliente;
use App\Models\Cupom;
use App\Models\Editora;
use App\Models\Endereco;
use App\Models\Favorito;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ECommerceCompletoTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;
    protected User $adminUser;
    protected Vendedor $vendedor;
    protected Livro $livro;
    protected Endereco $endereco;

    protected function setUp(): void
    {
        parent::setUp();

        // Usuário Cliente
        $this->clienteUser = User::factory()->create([
            'name' => 'Heymesson Azevedo Silva',
            'email' => 'heymesson@teste.com',
            'tipo' => 'cliente',
        ]);
        Cliente::create([
            'user_id' => $this->clienteUser->id,
            'cpf' => '12345678901',
            'celular_contato' => '11999998888',
            'data_nascimento' => '1995-05-15',
        ]);

        // Endereço do Cliente
        $this->endereco = Endereco::create([
            'user_id' => $this->clienteUser->id,
            'tipo' => 'Residencial',
            'cep' => '01310-100',
            'rua' => 'Avenida Paulista',
            'numero' => '1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'pais' => 'Brasil',
            'principal' => true,
        ]);

        // Usuário Administrador
        $this->adminUser = User::factory()->create([
            'name' => 'Administrador Geral',
            'email' => 'admin@livros.com',
            'tipo' => 'admin',
        ]);

        // Usuário Vendedor
        $vendedorUser = User::factory()->create([
            'name' => 'Livraria Dom Quixote',
            'email' => 'loja@domquixote.com',
            'tipo' => 'vendedor',
        ]);
        $this->vendedor = Vendedor::create([
            'user_id' => $vendedorUser->id,
            'cnpj' => '98.765.432/0001-88',
            'razao_social' => 'Dom Quixote Livros Ltda',
            'nome_fantasia' => 'Dom Quixote Livros',
            'telefone_comercial' => '1133334444',
            'inscricao_estadual' => '123456789',
            'status_aprovacao' => 'aprovado',
        ]);

        // Criação de Autor, Editora, Gênero e Livro
        $autor = Autor::factory()->create(['nome' => 'Machado de Assis']);
        $editora = Editora::factory()->create(['nome' => 'Editora Clássicos']);
        $genero = Genero::factory()->create(['nome' => 'Ficção']);

        $this->livro = Livro::factory()->create([
            'titulo' => 'Dom Casmurro Edição Especial',
            'preco' => 59.90,
            'quantidade' => 10,
            'autor_id' => $autor->id,
            'editora_id' => $editora->id,
            'genero_id' => $genero->id,
            'vendedor_id' => $this->vendedor->id,
        ]);
    }

    /**
     * Teste: Menu exibe somente o primeiro nome do usuário autenticado.
     */
    public function test_primeiro_nome_no_menu_e_dropdown(): void
    {
        $this->assertEquals('Heymesson', $this->clienteUser->primeiro_nome);

        $response = $this->actingAs($this->clienteUser)->get(route('painel'));
        $response->assertStatus(200);
        $response->assertSee('Heymesson');
        $response->assertDontSee('Heymesson Azevedo Silva, você está no painel');
    }

    /**
     * Teste: Fluxo completo do Carrinho de compras.
     */
    public function test_carrinho_adicionar_atualizar_remover_e_limpar(): void
    {
        // 1. Adicionar ao carrinho
        $response = $this->actingAs($this->clienteUser)->post(route('carrinho.adicionar'), [
            'livro_id' => $this->livro->id,
            'quantidade' => 2,
        ]);
        $response->assertRedirect(route('carrinho.index'));

        $this->assertDatabaseHas('carrinho_livro', [
            'livro_id' => $this->livro->id,
            'quantidade' => 2,
        ]);

        // 2. Atualizar quantidade
        $response = $this->actingAs($this->clienteUser)->patch(route('carrinho.atualizar', $this->livro), [
            'quantidade' => 4,
        ]);
        $response->assertRedirect(route('carrinho.index'));

        $this->assertDatabaseHas('carrinho_livro', [
            'livro_id' => $this->livro->id,
            'quantidade' => 4,
        ]);

        // 3. Remover item
        $response = $this->actingAs($this->clienteUser)->delete(route('carrinho.remover', $this->livro));
        $response->assertRedirect(route('carrinho.index'));

        $this->assertDatabaseMissing('carrinho_livro', [
            'livro_id' => $this->livro->id,
        ]);

        // 4. Esvaziar carrinho
        $this->actingAs($this->clienteUser)->post(route('carrinho.adicionar'), [
            'livro_id' => $this->livro->id,
            'quantidade' => 1,
        ]);
        $response = $this->actingAs($this->clienteUser)->post(route('carrinho.limpar'));
        $response->assertRedirect(route('carrinho.index'));

        $carrinho = Carrinho::where('user_id', $this->clienteUser->id)->first();
        $this->assertTrue($carrinho->isEmpty());
    }

    /**
     * Teste: Carrinho valida limite de estoque disponível.
     */
    public function test_carrinho_respeita_estoque_maximo(): void
    {
        $this->livro->update(['quantidade' => 3]);

        // Tentar adicionar 5 exemplares (estoque é 3)
        $this->actingAs($this->clienteUser)->post(route('carrinho.adicionar'), [
            'livro_id' => $this->livro->id,
            'quantidade' => 5,
        ]);

        // Quantidade no banco deve ser limitada a 3
        $this->assertDatabaseHas('carrinho_livro', [
            'livro_id' => $this->livro->id,
            'quantidade' => 3,
        ]);
    }

    /**
     * Teste: Aplicação, validação e remoção de Cupom de Desconto.
     */
    public function test_cupons_validacao_aplicacao_e_crud_admin(): void
    {
        // 1. Criar cupom percentual de 10%
        $cupom = Cupom::create([
            'codigo' => 'BEMVINDO10',
            'tipo_desconto' => 'percentual',
            'valor_desconto' => 10.00,
            'limite_uso' => 100,
            'validade_cupom' => now()->addDays(10),
        ]);

        $this->assertTrue($cupom->isValido(60.00));
        $this->assertEquals(6.00, $cupom->calcularDesconto(60.00));

        // 2. Aplicar cupom no carrinho
        $this->actingAs($this->clienteUser)->post(route('carrinho.adicionar'), [
            'livro_id' => $this->livro->id,
            'quantidade' => 1, // Livro custa 59.90
        ]);

        $response = $this->actingAs($this->clienteUser)->post(route('carrinho.cupom.aplicar'), [
            'codigo' => 'bemvindo10', // Testa mutator para maiúsculas
        ]);
        $response->assertRedirect(route('carrinho.index'));
        $response->assertSessionHas('status');

        // 3. Remover cupom
        $response = $this->actingAs($this->clienteUser)->delete(route('carrinho.cupom.remover'));
        $response->assertRedirect(route('carrinho.index'));
        $this->assertNull(session('cupom_codigo'));

        // 4. Admin CRUD de Cupons
        $response = $this->actingAs($this->adminUser)->get(route('admin.cupons.index'));
        $response->assertStatus(200);
        $response->assertSee('BEMVINDO10');

        $response = $this->actingAs($this->adminUser)->post(route('admin.cupons.store'), [
            'codigo' => 'FRETEGRATIS',
            'tipo_desconto' => 'valor_fixo',
            'valor_desconto' => 15.00,
            'limite_uso' => 30,
            'validade_cupom' => now()->addDays(30)->toDateString(),
        ]);
        $response->assertRedirect(route('admin.cupons.index'));
        $this->assertDatabaseHas('cupons', ['codigo' => 'FRETEGRATIS']);
    }

    /**
     * Teste: Edição e gerenciamento de endereços de entrega.
     */
    public function test_edicao_e_padronizacao_de_enderecos(): void
    {
        $this->assertTrue($this->endereco->is_principal);
        $this->assertTrue($this->endereco->padrao);

        // Atualizar endereço existente
        $response = $this->actingAs($this->clienteUser)->put(route('enderecos.atualizar', $this->endereco), [
            'tipo' => 'Comercial',
            'cep' => '04538-133',
            'rua' => 'Rua Joaquim Floriano',
            'numero' => '466',
            'bairro' => 'Itaim Bibi',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'complemento' => 'Sala 101',
            'principal' => '1',
        ]);

        $response->assertRedirect(route('painel', ['tab' => 'enderecos']));
        $this->assertDatabaseHas('enderecos', [
            'id' => $this->endereco->id,
            'tipo' => 'Comercial',
            'rua' => 'Rua Joaquim Floriano',
            'numero' => '466',
            'complemento' => 'Sala 101',
            'principal' => 1,
        ]);
    }

    /**
     * Teste: Checkout e Finalização de Pedido com baixa de estoque, snapshot e pagamento.
     */
    public function test_checkout_e_criacao_de_pedido_completo(): void
    {
        // 1. Adicionar livro ao carrinho
        $this->actingAs($this->clienteUser)->post(route('carrinho.adicionar'), [
            'livro_id' => $this->livro->id,
            'quantidade' => 2,
        ]);

        $estoqueOriginal = $this->livro->quantidade; // 10

        // 2. Tela de checkout
        $response = $this->actingAs($this->clienteUser)->get(route('checkout.index'));
        $response->assertStatus(200);
        $response->assertSee('Finalizar Pedido');

        // 3. Finalizar pedido via PIX
        $response = $this->actingAs($this->clienteUser)->post(route('pedidos.finalizar'), [
            'endereco_id' => $this->endereco->id,
            'metodo_pagamento' => 'pix',
        ]);

        $pedido = Pedido::where('cliente_id', $this->clienteUser->cliente->id)->latest()->first();
        $this->assertNotNull($pedido);
        $response->assertRedirect(route('pedidos.sucesso', $pedido));

        // 4. Verificações do Pedido, Itens e Pagamento
        $this->assertEquals('pendente', $pedido->status);
        $this->assertEquals(2, $pedido->itens()->sum('quantidade_itens'));
        $this->assertDatabaseHas('pagamentos', [
            'pedido_id' => $pedido->id,
            'metodo_pagamento' => 'pix',
            'status_pagamento' => 'pendente',
        ]);

        // 5. Verificar baixa no estoque do livro
        $this->livro->refresh();
        $this->assertEquals($estoqueOriginal - 2, $this->livro->quantidade);

        // 6. Verificar carrinho esvaziado
        $carrinho = Carrinho::where('user_id', $this->clienteUser->id)->first();
        $this->assertTrue($carrinho->isEmpty());

        // 7. Acessar detalhes do pedido
        $response = $this->actingAs($this->clienteUser)->get(route('pedidos.show', $pedido));
        $response->assertStatus(200);
        $response->assertSee($pedido->numero_pedido);
    }

    /**
     * Teste: Cartões salvos simulados para compras em 1 clique.
     */
    public function test_cartoes_salvos_cadastro_padrao_e_exclusao(): void
    {
        // 1. Salvar novo cartão
        $response = $this->actingAs($this->clienteUser)->post(route('cartoes.salvar'), [
            'numero_cartao' => '5502 0987 6543 2109',
            'nome_titular' => 'HEYMESSON AZEVEDO',
            'validade' => '12/28',
            'padrao' => '1',
        ]);

        $response->assertRedirect(route('painel', ['tab' => 'cartoes']));

        $cartao = CartaoSalvo::where('user_id', $this->clienteUser->id)->first();
        $this->assertNotNull($cartao);
        $this->assertEquals('2109', $cartao->ultimos_digitos);
        $this->assertEquals('mastercard', $cartao->bandeira_cartao);
        $this->assertTrue($cartao->cartao_padrao);
        $this->assertTrue($cartao->padrao);

        // 2. Salvar segundo cartão sem padrão
        $this->actingAs($this->clienteUser)->post(route('cartoes.salvar'), [
            'numero_cartao' => '4111 1111 1111 8888',
            'nome_titular' => 'HEYMESSON AZEVEDO',
            'validade' => '10/29',
            'padrao' => '0',
        ]);

        $cartao2 = CartaoSalvo::where('user_id', $this->clienteUser->id)->where('ultimos_digitos', '8888')->first();
        $this->assertFalse($cartao2->cartao_padrao);

        // 3. Alterar padrão para o segundo cartão
        $this->actingAs($this->clienteUser)->post(route('cartoes.definir-padrao', $cartao2));
        $cartao2->refresh();
        $cartao->refresh();
        $this->assertTrue($cartao2->cartao_padrao);
        $this->assertFalse($cartao->cartao_padrao);

        // 4. Excluir cartão
        $response = $this->actingAs($this->clienteUser)->delete(route('cartoes.deletar', $cartao));
        $this->assertDatabaseMissing('cartoes_salvos', ['id' => $cartao->id]);
    }

    /**
     * Teste: Favoritar e desfavoritar livros (Toggle).
     */
    public function test_favoritos_toggle(): void
    {
        // 1. Adicionar aos favoritos
        $response = $this->actingAs($this->clienteUser)->postJson(route('favoritos.toggle', $this->livro));
        $response->assertStatus(200);
        $response->assertJson([
            'sucesso' => true,
            'favoritado' => true,
            'total_favoritos' => 1,
        ]);
        $this->assertTrue($this->livro->isFavoritadoPor($this->clienteUser));

        // 2. Remover dos favoritos pelo toggle
        $response = $this->actingAs($this->clienteUser)->postJson(route('favoritos.toggle', $this->livro));
        $response->assertStatus(200);
        $response->assertJson([
            'sucesso' => true,
            'favoritado' => false,
            'total_favoritos' => 0,
        ]);
        $this->assertFalse($this->livro->isFavoritadoPor($this->clienteUser));
    }

    /**
     * Teste: Avaliação de pedido pelo cliente.
     */
    public function test_avaliacao_de_pedido_entregue(): void
    {
        // Criação de pedido entregue
        $pedido = Pedido::create([
            'cliente_id' => $this->clienteUser->cliente->id,
            'vendedor_id' => $this->vendedor->id,
            'numero_pedido' => 'PED-TESTE-999',
            'status' => 'entregue',
            'total' => 59.90,
            'data_pedido' => now(),
        ]);

        $response = $this->actingAs($this->clienteUser)->post(route('avaliacoes.salvar', $pedido), [
            'avaliacao' => 5,
            'comentario' => 'Entrega super rápida e livro impecável!',
            'recomenda' => '1',
        ]);

        $response->assertRedirect(route('pedidos.show', $pedido));
        $this->assertDatabaseHas('avaliacoes', [
            'cliente_id' => $this->clienteUser->cliente->id,
            'pedido_id' => $pedido->id,
            'avaliacao' => 5,
            'recomenda' => 1,
        ]);
    }
}
