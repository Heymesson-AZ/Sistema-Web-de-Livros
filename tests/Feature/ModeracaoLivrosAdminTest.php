<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeracaoLivrosAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $moderador;
    private User $gestorComercial;
    private User $vendedorUser;
    private Vendedor $vendedor;
    private Autor $autor;
    private Genero $genero;
    private Editora $editora;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Super Admin
        $this->superAdmin = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
        ]);
        Admin::create([
            'user_id' => $this->superAdmin->id,
            'cargo' => Admin::CARGO_SUPER_ADMIN,
            'departamento' => Admin::DEPARTAMENTO_DIRETORIA,
        ]);

        // 2. Moderador de Catálogo
        $this->moderador = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
        ]);
        Admin::create([
            'user_id' => $this->moderador->id,
            'cargo' => Admin::CARGO_MODERADOR,
            'departamento' => Admin::DEPARTAMENTO_MODERACAO,
        ]);

        // 3. Gestor Comercial
        $this->gestorComercial = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
        ]);
        Admin::create([
            'user_id' => $this->gestorComercial->id,
            'cargo' => Admin::CARGO_GESTOR_COMERCIAL,
            'departamento' => Admin::DEPARTAMENTO_COMERCIAL,
        ]);

        // 4. Vendedor e loja parceira
        $this->vendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'status' => 'ativo',
        ]);
        $this->vendedor = Vendedor::create([
            'user_id' => $this->vendedorUser->id,
            'cnpj' => '11.222.333/0001-44',
            'razao_social' => 'Livraria Central Ltda',
            'nome_fantasia' => 'Livraria Central',
            'telefone_comercial' => '1133334444',
            'inscricao_estadual' => '123456789',
            'status_aprovacao' => 'aprovado',
        ]);

        // Auxiliares
        $this->autor = Autor::first() ?? Autor::create([
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileira',
            'data_nascimento' => '1839-06-21',
            'email' => 'machado.' . rand(100, 9999) . '@literatura.com',
        ]);
        $this->genero = Genero::first() ?? Genero::create(['nome' => 'Romance']);
        $this->editora = Editora::first() ?? Editora::create(['nome' => 'Ática']);
    }

    private function criarLivro(array $atributos = []): Livro
    {
        return Livro::create(array_merge([
            'titulo' => 'Dom Casmurro ' . uniqid(),
            'isbn' => '97885' . rand(1000000, 9999999),
            'data_publicacao' => '1899-01-01',
            'preco' => 39.90,
            'quantidade' => 10,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'status_moderacao' => Livro::STATUS_MODERACAO_ATIVO,
        ], $atributos));
    }

    public function test_administrador_nao_publica_livros_e_e_redirecionado_com_aviso(): void
    {
        $responseCreate = $this->actingAs($this->superAdmin)->get(route('admin.livros.create'));
        $responseCreate->assertRedirect(route('admin.livros.index'));
        $responseCreate->assertSessionHas('info');

        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.livros.store'), [
            'titulo' => 'Tentativa de Publicacao',
            'isbn' => '9788500000001',
        ]);
        $responseStore->assertRedirect(route('admin.livros.index'));
        $responseStore->assertSessionHas('info');
    }

    public function test_moderacao_de_livros_por_super_admin_e_moderador(): void
    {
        $livro = $this->criarLivro();

        // 1. Colocar sob análise por suspeita de crime ou dados falsos
        $responseAnalise = $this->actingAs($this->moderador)->patch(route('admin.livros.status', $livro), [
            'status_moderacao' => Livro::STATUS_MODERACAO_SOB_ANALISE,
            'motivo_moderacao' => 'Suspeita de dados inválidos ou cópia não autorizada.',
        ]);
        $responseAnalise->assertRedirect();
        $responseAnalise->assertSessionHas('status');

        $livro->refresh();
        $this->assertEquals(Livro::STATUS_MODERACAO_SOB_ANALISE, $livro->status_moderacao);
        $this->assertEquals('Suspeita de dados inválidos ou cópia não autorizada.', $livro->motivo_moderacao);
        $this->assertEquals($this->moderador->id, $livro->moderado_por);
        $this->assertNotNull($livro->moderado_em);

        // 2. Bloquear temporariamente
        $responseBloqueio = $this->actingAs($this->superAdmin)->patch(route('admin.livros.status', $livro), [
            'status_moderacao' => Livro::STATUS_MODERACAO_BLOQUEADO,
            'motivo_moderacao' => 'Suspensão temporária para verificação jurídica.',
        ]);
        $responseBloqueio->assertRedirect();

        $livro->refresh();
        $this->assertEquals(Livro::STATUS_MODERACAO_BLOQUEADO, $livro->status_moderacao);
        $this->assertEquals($this->superAdmin->id, $livro->moderado_por);

        // 3. Banir livro por infração grave
        $responseBanir = $this->actingAs($this->superAdmin)->patch(route('admin.livros.status', $livro), [
            'status_moderacao' => Livro::STATUS_MODERACAO_BANIDO,
            'motivo_moderacao' => 'Violação gravíssima de termos legais.',
        ]);
        $responseBanir->assertRedirect();

        $livro->refresh();
        $this->assertEquals(Livro::STATUS_MODERACAO_BANIDO, $livro->status_moderacao);
        $this->assertTrue($livro->isBanido());

        // 4. Reaprovar / Ativar obra
        $responseAtivar = $this->actingAs($this->moderador)->patch(route('admin.livros.status', $livro), [
            'status_moderacao' => Livro::STATUS_MODERACAO_ATIVO,
            'motivo_moderacao' => 'Regularização confirmada.',
        ]);
        $responseAtivar->assertRedirect();

        $livro->refresh();
        $this->assertTrue($livro->isAtivo());
    }

    public function test_cargo_sem_permissao_de_moderacao_e_bloqueado(): void
    {
        $livro = $this->criarLivro();

        // Gestor comercial não tem permissão para moderar livros
        $response = $this->actingAs($this->gestorComercial)->patch(route('admin.livros.status', $livro), [
            'status_moderacao' => Livro::STATUS_MODERACAO_BANIDO,
            'motivo_moderacao' => 'Tentativa não autorizada.',
        ]);

        $response->assertStatus(403);
    }

    public function test_livros_sob_analise_bloqueados_ou_banidos_ficam_ocultos_da_vitrine_publica(): void
    {
        $livroAtivo = $this->criarLivro(['titulo' => 'Livro Liberado Visivel']);
        $livroSobAnalise = $this->criarLivro(['titulo' => 'Livro Suspeito em Analise', 'status_moderacao' => Livro::STATUS_MODERACAO_SOB_ANALISE]);
        $livroBloqueado = $this->criarLivro(['titulo' => 'Livro Bloqueado', 'status_moderacao' => Livro::STATUS_MODERACAO_BLOQUEADO]);
        $livroBanido = $this->criarLivro(['titulo' => 'Livro Ilegal Banido', 'status_moderacao' => Livro::STATUS_MODERACAO_BANIDO]);

        // Vitrine pública / Início
        $responseHome = $this->get(route('inicio'));
        $responseHome->assertOk();
        $responseHome->assertSee('Livro Liberado Visivel');
        $responseHome->assertDontSee('Livro Suspeito em Analise');
        $responseHome->assertDontSee('Livro Bloqueado');
        $responseHome->assertDontSee('Livro Ilegal Banido');

        // Busca rápida API
        $responseBusca = $this->getJson(route('livros.busca-rapida', ['q' => 'Livro']));
        $responseBusca->assertOk();
        $responseBusca->assertJsonFragment(['titulo' => 'Livro Liberado Visivel']);
        $responseBusca->assertJsonMissing(['titulo' => 'Livro Suspeito em Analise']);
        $responseBusca->assertJsonMissing(['titulo' => 'Livro Bloqueado']);
        $responseBusca->assertJsonMissing(['titulo' => 'Livro Ilegal Banido']);

        // Página individual pública
        $this->get(route('livros.show', $livroAtivo))->assertOk();
        $this->get(route('livros.show', $livroSobAnalise))->assertNotFound();
        $this->get(route('livros.show', $livroBloqueado))->assertNotFound();
        $this->get(route('livros.show', $livroBanido))->assertNotFound();
    }

    public function test_livros_nao_ativos_nao_podem_ser_adicionados_ao_carrinho(): void
    {
        $livroSobAnalise = $this->criarLivro([
            'titulo' => 'Exemplar Sob Moderacao',
            'status_moderacao' => Livro::STATUS_MODERACAO_SOB_ANALISE,
        ]);

        $response = $this->post(route('carrinho.adicionar'), [
            'livro_id' => $livroSobAnalise->id,
            'quantidade' => 1,
        ]);

        $response->assertSessionHas('erro');
    }

    public function test_somente_super_admin_pode_gerenciar_contas_administrativas(): void
    {
        // 1. Super Admin acessa criação de admin com sucesso
        $this->actingAs($this->superAdmin)->get(route('admin.administradores.create'))
            ->assertOk();

        // 2. Moderador tenta acessar criação de admin e toma 403
        $this->actingAs($this->moderador)->get(route('admin.administradores.create'))
            ->assertStatus(403);

        // 3. Gestor Comercial tenta acessar criação de admin e toma 403
        $this->actingAs($this->gestorComercial)->get(route('admin.administradores.create'))
            ->assertStatus(403);
    }

    public function test_menu_lateral_nao_contem_botao_de_notificacoes(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $response->assertOk();

        // O menu lateral offcanvas não contém o ícone/botão de notificações
        $this->assertStringNotContainsString('data-lucide="bell"', view('components.menu')->render());

        // A barra de topo (navbar) mantém o sino/dropdown de notificações
        $response->assertSee('dropdownNotificacoesBtn');
    }
}
