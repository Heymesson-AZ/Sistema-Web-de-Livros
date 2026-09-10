<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Autor;
use App\Models\Cliente;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SegurancaAcoesCriticasENotificacoesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected Admin $admin;
    protected User $outroAdminUser;
    protected Admin $outroAdmin;
    protected User $vendedorUser;
    protected Vendedor $vendedor;
    protected User $clienteUser;
    protected Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin autenticado com senha conhecida
        $this->adminUser = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
            'password' => Hash::make('SenhaAdmin@123'),
        ]);
        $this->admin = Admin::create([
            'user_id' => $this->adminUser->id,
            'cargo' => Admin::CARGO_SUPER_ADMIN,
            'departamento' => 'Tecnologia da Informação',
        ]);

        // 2. Outro Admin a ser editado
        $this->outroAdminUser = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
            'email' => 'outro.admin.' . rand(1000, 9999) . '@teste.com',
        ]);
        $this->outroAdmin = Admin::create([
            'user_id' => $this->outroAdminUser->id,
            'cargo' => Admin::CARGO_ADMINISTRADOR,
            'departamento' => Admin::DEPARTAMENTO_OPERACOES,
        ]);

        // 3. Vendedor
        $rand = rand(1000, 9999);
        $this->vendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'status' => 'ativo',
        ]);
        $this->vendedor = Vendedor::create([
            'user_id' => $this->vendedorUser->id,
            'cnpj' => "33.{$rand}.999/0001-55",
            'telefone_comercial' => '(11) 98765-4321',
            'razao_social' => 'Livraria Central LTDA',
            'nome_fantasia' => 'Central Livros',
            'inscricao_estadual' => '999.888.777.000',
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
        ]);

        // 4. Cliente
        $this->clienteUser = User::factory()->create([
            'tipo' => 'cliente',
            'status' => 'ativo',
        ]);
        $this->cliente = Cliente::create([
            'user_id' => $this->clienteUser->id,
            'cpf' => "888.{$rand}.111-22",
            'celular_contato' => '(11) 91234-5678',
            'data_nascimento' => '1995-05-20',
        ]);
    }

    /**
     * Valida que o administrador DEVE fornecer sua senha atual para alterar outro administrador.
     */
    public function test_admin_precisa_confirmar_senha_para_editar_outro_administrador(): void
    {
        // Tentativa sem senha ou com senha incorreta
        $response = $this->actingAs($this->adminUser)->put(route('admin.administradores.update', $this->outroAdmin), [
            'name' => 'Nome Modificado',
            'email' => $this->outroAdminUser->email,
            'status' => 'ativo',
            'cargo' => Admin::CARGO_GERENTE_CATALOGO,
            'departamento' => Admin::DEPARTAMENTO_OPERACOES,
            'senha_confirmacao_admin' => 'SenhaErrada123',
        ]);

        $response->assertSessionHasErrors(['senha_confirmacao_admin']);

        // Tentativa com senha correta do administrador logado
        $responseCorreta = $this->actingAs($this->adminUser)->put(route('admin.administradores.update', $this->outroAdmin), [
            'name' => 'Nome Atualizado com Sucesso',
            'email' => $this->outroAdminUser->email,
            'status' => 'ativo',
            'cargo' => Admin::CARGO_GERENTE_CATALOGO,
            'departamento' => Admin::DEPARTAMENTO_OPERACOES,
            'senha_confirmacao_admin' => 'SenhaAdmin@123',
        ]);

        $responseCorreta->assertRedirect(route('admin.administradores.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->outroAdminUser->id,
            'name' => 'Nome Atualizado Com Sucesso', // Mutator title case
        ]);
    }

    /**
     * Valida que o administrador DEVE fornecer sua senha atual para alterar dados de um vendedor.
     */
    public function test_admin_precisa_confirmar_senha_para_editar_vendedor(): void
    {
        // Sem a senha correta
        $response = $this->actingAs($this->adminUser)->put(route('admin.vendedores.update', $this->vendedor), [
            'name' => $this->vendedorUser->name,
            'email' => $this->vendedorUser->email,
            'status' => 'ativo',
            'cnpj' => $this->vendedor->cnpj,
            'razao_social' => 'Livraria Central Nova LTDA',
            'nome_fantasia' => 'Central Livros',
            'inscricao_estadual' => $this->vendedor->inscricao_estadual,
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
            'senha_confirmacao_admin' => 'Incorreta',
        ]);

        $response->assertSessionHasErrors(['senha_confirmacao_admin']);

        // Com a senha correta
        $response = $this->actingAs($this->adminUser)->put(route('admin.vendedores.update', $this->vendedor), [
            'name' => $this->vendedorUser->name,
            'email' => $this->vendedorUser->email,
            'status' => 'ativo',
            'cnpj' => $this->vendedor->cnpj,
            'razao_social' => 'Livraria Central Nova LTDA',
            'nome_fantasia' => 'Central Livros',
            'inscricao_estadual' => $this->vendedor->inscricao_estadual,
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
            'senha_confirmacao_admin' => 'SenhaAdmin@123',
        ]);

        $response->assertRedirect(route('admin.vendedores.index'));
        $this->assertDatabaseHas('vendedores', [
            'id' => $this->vendedor->id,
            'razao_social' => 'Livraria Central Nova Ltda',
        ]);
    }

    /**
     * Valida que o administrador DEVE fornecer sua senha atual para alterar dados de um cliente.
     */
    public function test_admin_precisa_confirmar_senha_para_editar_cliente(): void
    {
        // Sem a senha correta
        $response = $this->actingAs($this->adminUser)->put(route('admin.clientes.update', $this->cliente), [
            'name' => $this->clienteUser->name,
            'email' => $this->clienteUser->email,
            'status' => 'ativo',
            'cpf' => $this->cliente->cpf,
            'data_nascimento' => '1995-05-20',
            'senha_confirmacao_admin' => 'Incorreta',
        ]);

        $response->assertSessionHasErrors(['senha_confirmacao_admin']);

        // Com a senha correta
        $response = $this->actingAs($this->adminUser)->put(route('admin.clientes.update', $this->cliente), [
            'name' => 'Cliente Alterado com Senha',
            'email' => $this->clienteUser->email,
            'status' => 'ativo',
            'cpf' => $this->cliente->cpf,
            'data_nascimento' => '1995-05-20',
            'senha_confirmacao_admin' => 'SenhaAdmin@123',
        ]);

        $response->assertRedirect(route('admin.clientes.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->clienteUser->id,
            'name' => 'Cliente Alterado Com Senha',
        ]);
    }

    /**
     * Valida que o dropdown de notificações é renderizado na barra superior para usuários logados.
     * Valida que o dropdown de notificações é renderizado na barra superior independente da página.
     */
    public function test_dropdown_de_notificacoes_renderiza_na_topbar(): void
    {
        // 1. Em páginas internas
        $response = $this->actingAs($this->adminUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('dropdownNotificacoesWrapper');
        $response->assertSee('dropdownNotificacoesBtn');
        $response->assertSee('Notificações');

        // 2. Na página inicial / vitrine
        $responseHome = $this->actingAs($this->adminUser)->get(url('/'));
        $responseHome->assertStatus(200);
        $responseHome->assertSee('dropdownNotificacoesWrapper');
        $responseHome->assertSee('dropdownNotificacoesBtn');
    }

    /**
     * Valida que o menu lateral não contém mais o botão 'Meu Painel & Perfil'.
     */
    public function test_menu_lateral_nao_contem_botao_painel_e_perfil(): void
    {
        $response = $this->actingAs($this->adminUser)->get(url('/'));
        $response->assertStatus(200);
        $response->assertDontSee('Meu Painel & Perfil');
    }

    /**
     * Valida que a situação unificada do vendedor evita aberração de aprovado e banido.
     */
    public function test_situacao_unificada_do_vendedor_sincroniza_sem_inconsistencia(): void
    {
        // Alterar para banido via atualização administrativa com confirmação de senha
        $response = $this->actingAs($this->adminUser)->put(route('admin.vendedores.update', $this->vendedor), [
            'name' => $this->vendedorUser->name,
            'email' => $this->vendedorUser->email,
            'situacao' => 'banido',
            'cnpj' => $this->vendedor->cnpj,
            'razao_social' => $this->vendedor->razao_social,
            'nome_fantasia' => $this->vendedor->nome_fantasia,
            'inscricao_estadual' => $this->vendedor->inscricao_estadual,
            'senha_confirmacao_admin' => 'SenhaAdmin@123',
        ]);

        $response->assertRedirect(route('admin.vendedores.index'));

        $this->vendedor->refresh();
        $this->assertEquals('banido', $this->vendedor->situacao);
        $this->assertEquals('Banido', $this->vendedor->situacao_rotulo);
        $this->assertEquals(Vendedor::STATUS_REJEITADO, $this->vendedor->status_aprovacao);
        $this->assertEquals('banido', $this->vendedor->user->status);

        // A listagem exibe badge de banido
        $responseListagem = $this->actingAs($this->adminUser)->get(route('admin.vendedores.index'));
        $responseListagem->assertSee('Banido');
        $responseListagem->assertDontSee('<span>Editar</span>'); // Botão de edição é icon-only
    }

    /**
     * Valida que o Admin não publica livros e a listagem centralizada atende ambos os perfis.
     */
    public function test_gestao_centralizada_de_livros_e_escopo_do_admin(): void
    {
        // 1. Admin acessa listagem centralizada
        $response = $this->actingAs($this->adminUser)->get(route('admin.livros.index'));
        $response->assertStatus(200);
        $response->assertSee('Catálogo de Livros');
        $response->assertDontSee('Publicar Novo Livro'); // Admin não publica livros

        // 2. Vendedor acessa listagem centralizada
        $responseVendedor = $this->actingAs($this->vendedorUser)->get(route('vendedor.livros.index'));
        $responseVendedor->assertStatus(200);
        $responseVendedor->assertSee('Catálogo da Minha Loja');
        $responseVendedor->assertSee('Publicar Novo Livro'); // Vendedor publica
    }

    /**
     * Valida as regras de negócio de aptidão para vender e cálculo de situação operacional.
     */
    public function test_aptidao_operacional_do_vendedor_regras_e_estados(): void
    {
        // 1. Aprovado e Ativo = Apto
        $this->vendedor->status_aprovacao = Vendedor::STATUS_APROVADO;
        $this->vendedorUser->status = 'ativo';
        $this->vendedorUser->save();
        $this->vendedor->save();

        $this->assertTrue($this->vendedor->isAptoParaVender());
        $this->assertNull($this->vendedor->motivo_inapto);

        // 2. Aprovado mas Inativo = Inapto
        $this->vendedorUser->status = 'inativo';
        $this->vendedorUser->save();
        $this->vendedor->refresh();

        $this->assertFalse($this->vendedor->isAptoParaVender());
        $this->assertStringContainsString('desativada', $this->vendedor->motivo_inapto);

        // 3. Pendente mas Ativo = Inapto
        $this->vendedorUser->status = 'ativo';
        $this->vendedorUser->save();
        $this->vendedor->status_aprovacao = Vendedor::STATUS_PENDENTE;
        $this->vendedor->save();
        $this->vendedor->refresh();

        $this->assertFalse($this->vendedor->isAptoParaVender());
        $this->assertStringContainsString('validação cadastral', $this->vendedor->motivo_inapto);
    }

    /**
     * Valida que a página de detalhes do vendedor exibe a gestão e moderação do catálogo de livros da loja.
     */
    public function test_detalhes_do_vendedor_exibe_catalogo_e_moderacao_de_livros(): void
    {
        $livro = Livro::factory()->create([
            'vendedor_id' => $this->vendedor->id,
            'titulo' => 'Livro Loja Moderacao ' . rand(100, 999),
            'status_moderacao' => Livro::STATUS_MODERACAO_ATIVO,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.vendedores.show', $this->vendedor));
        $response->assertStatus(200);
        $response->assertSee('Catálogo de Livros da Loja');
        $response->assertSee($livro->titulo);
        $response->assertSee('Moderar');
    }

    /**
     * Valida que a barra de filtros na página inicial fica visível para visitantes e usuários logados sem colapso.
     */
    public function test_barra_de_filtros_da_home_sempre_visivel(): void
    {
        $responseVisitante = $this->get(url('/'));
        $responseVisitante->assertStatus(200);
        $responseVisitante->assertSee('name="busca"', false);
        $responseVisitante->assertSee('name="genero"', false);
        $responseVisitante->assertSee('name="faixa_preco"', false);
        $responseVisitante->assertDontSee('id="filtrosAvancadosCollapse"');

        $responseLogado = $this->actingAs($this->clienteUser)->get(url('/'));
        $responseLogado->assertStatus(200);
        $responseLogado->assertSee('name="busca"', false);
        $responseLogado->assertDontSee('id="filtrosAvancadosCollapse"');
    }
}
