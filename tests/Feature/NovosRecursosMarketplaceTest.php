<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Cupom;
use App\Models\Livro;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NovosRecursosMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa que a home page renderiza com código 200 e inclui a meta tag csrf-token.
     */
    public function test_home_page_contem_csrf_token_e_responde_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('name="csrf-token"', false);
    }

    /**
     * Testa que rota inexistente retorna 404 sem erro 500.
     */
    public function test_rota_inexistente_retorna_404_sem_erro_500(): void
    {
        $response = $this->get('/rota-que-nao-existe-no-sistema-12345');

        $response->assertStatus(404);
    }

    /**
     * Testa que usuário não autenticado recebe 401 ao tentar alternar favorito via AJAX.
     */
    public function test_favorito_toggle_sem_login_retorna_401(): void
    {
        $livro = Livro::factory()->create();

        $response = $this->withHeaders(['Accept' => 'application/json'])
                         ->postJson("/favoritos/toggle/{$livro->id}");

        $response->assertStatus(401);
    }

    /**
     * Testa que usuário autenticado consegue favoritar e desfavoritar um livro.
     */
    public function test_favorito_toggle_com_usuario_autenticado(): void
    {
        $user = User::factory()->create(['tipo' => 'cliente', 'status' => 'ativo']);
        Cliente::factory()->create(['user_id' => $user->id]);
        $livro = Livro::factory()->create();

        // Primeira chamada adiciona aos favoritos
        $response = $this->actingAs($user)
                         ->withHeaders(['Accept' => 'application/json'])
                         ->postJson("/favoritos/toggle/{$livro->id}");

        $response->assertStatus(200);
        $response->assertJson(['favoritado' => true]);

        // Segunda chamada remove dos favoritos
        $response = $this->actingAs($user)
                         ->withHeaders(['Accept' => 'application/json'])
                         ->postJson("/favoritos/toggle/{$livro->id}");

        $response->assertStatus(200);
        $response->assertJson(['favoritado' => false]);
    }

    /**
     * Testa que a vitrine exibe apenas livros disponíveis (quantidade > 0, moderacao ativa e vendedor ativo).
     */
    public function test_vitrine_filtra_apenas_livros_disponiveis(): void
    {
        $vendedorAtivo = Vendedor::factory()->create(['status_aprovacao' => 'aprovado']);
        $vendedorAtivo->user->update(['status' => 'ativo']);

        $livroDisponivel = Livro::factory()->create([
            'vendedor_id' => $vendedorAtivo->id,
            'quantidade' => 5,
            'status_moderacao' => 'ativo',
            'titulo' => 'Livro Em Estoque e Aprovado',
        ]);

        $livroEsgotado = Livro::factory()->create([
            'vendedor_id' => $vendedorAtivo->id,
            'quantidade' => 0,
            'status_moderacao' => 'ativo',
            'titulo' => 'Livro Esgotado Indisponivel',
        ]);

        $livroSobAnalise = Livro::factory()->create([
            'vendedor_id' => $vendedorAtivo->id,
            'quantidade' => 10,
            'status_moderacao' => 'sob_analise',
            'titulo' => 'Livro Sob Analise',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($livroDisponivel->titulo);
        $response->assertDontSee($livroEsgotado->titulo);
        $response->assertDontSee($livroSobAnalise->titulo);
    }

    /**
     * Testa que cupom de vendedor aplica desconto somente aos livros da sua loja.
     */
    public function test_cupom_de_vendedor_aplica_apenas_aos_itens_da_sua_loja(): void
    {
        $vendedorA = Vendedor::factory()->create(['status_aprovacao' => 'aprovado']);
        $vendedorB = Vendedor::factory()->create(['status_aprovacao' => 'aprovado']);

        $livroA = Livro::factory()->create([
            'vendedor_id' => $vendedorA->id,
            'preco' => 100.00,
            'quantidade' => 5,
            'status_moderacao' => 'ativo',
        ]);

        $livroB = Livro::factory()->create([
            'vendedor_id' => $vendedorB->id,
            'preco' => 100.00,
            'quantidade' => 5,
            'status_moderacao' => 'ativo',
        ]);

        $cupomLojaA = Cupom::create([
            'codigo' => 'LOJAA10',
            'tipo_desconto' => 'percentual',
            'valor_desconto' => 10, // 10%
            'tipo_origem' => 'vendedor',
            'vendedor_id' => $vendedorA->id,
            'validade_cupom' => now()->addDays(10),
        ]);

        // Itens do carrinho com Livro A e Livro B
        $itens = collect([
            (object) ['livro' => $livroA, 'preco' => 100.00, 'quantidade' => 1],
            (object) ['livro' => $livroB, 'preco' => 100.00, 'quantidade' => 1],
        ]);

        $resultado = $cupomLojaA->calcularDescontoParaItens($itens);

        $this->assertTrue($resultado['aplicavel']);
        // Deve descontar 10% apenas sobre os 100.00 do Livro A = R$ 10.00 (e não 20.00)
        $this->assertEquals(10.00, $resultado['desconto']);
    }

    /**
     * Testa que cupom de incentivo da plataforma com requer_concordancia só desconta se o vendedor aderiu.
     */
    public function test_cupom_incentivo_requer_concordancia(): void
    {
        $vendedorAderiu = Vendedor::factory()->create(['status_aprovacao' => 'aprovado']);
        $vendedorNaoAderiu = Vendedor::factory()->create(['status_aprovacao' => 'aprovado']);

        $livroA = Livro::factory()->create([
            'vendedor_id' => $vendedorAderiu->id,
            'preco' => 50.00,
            'quantidade' => 5,
            'status_moderacao' => 'ativo',
        ]);

        $livroB = Livro::factory()->create([
            'vendedor_id' => $vendedorNaoAderiu->id,
            'preco' => 50.00,
            'quantidade' => 5,
            'status_moderacao' => 'ativo',
        ]);

        $cupomIncentivo = Cupom::create([
            'codigo' => 'INCENTIVO20',
            'tipo_desconto' => 'percentual',
            'valor_desconto' => 20, // 20%
            'tipo_origem' => 'plataforma',
            'requer_concordancia' => true,
            'validade_cupom' => now()->addDays(10),
        ]);

        // Vendedor A adere à campanha
        $vendedorAderiu->campanhasAderidas()->attach($cupomIncentivo->id, ['concordou' => true, 'data_adesao' => now()]);

        $itens = collect([
            (object) ['livro' => $livroA, 'preco' => 50.00, 'quantidade' => 1],
            (object) ['livro' => $livroB, 'preco' => 50.00, 'quantidade' => 1],
        ]);

        $resultado = $cupomIncentivo->calcularDescontoParaItens($itens);

        $this->assertTrue($resultado['aplicavel']);
        // Apenas o Livro A recebe o desconto de 20% = R$ 10.00
        $this->assertEquals(10.00, $resultado['desconto']);
    }

    /**
     * Testa que cliente com pedidos ativos não pode excluir a própria conta.
     */
    public function test_cliente_com_pedidos_ativos_nao_pode_excluir_conta(): void
    {
        $user = User::factory()->create(['tipo' => 'cliente', 'status' => 'ativo', 'password' => bcrypt('Senha@123')]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);
        $vendedor = Vendedor::factory()->create();

        // Criar pedido ativo
        Pedido::create([
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'numero_pedido' => 'PED-TEST-001',
            'status' => 'processando',
            'total' => 50.00,
            'data_pedido' => now(),
        ]);

        $response = $this->actingAs($user)
                         ->from('/perfil-cliente/editar')
                         ->delete(route('cliente.perfil.deletar'), [
                             'password' => 'Senha@123',
                         ]);

        $response->assertRedirect('/perfil-cliente/editar');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Testa o fallback da foto de perfil para SVG Data URI (não quebra nem depende de rede).
     */
    public function test_fallback_foto_perfil_gera_svg_data_uri(): void
    {
        $user = User::factory()->create([
            'name' => 'Carlos Drummond',
            'foto_perfil' => null,
        ]);

        $foto = $user->foto;

        $this->assertStringStartsWith('data:image/svg+xml', $foto);
    }
}
