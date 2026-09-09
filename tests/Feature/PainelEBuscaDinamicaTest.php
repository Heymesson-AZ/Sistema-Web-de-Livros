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
use Tests\TestCase;

class PainelEBuscaDinamicaTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $vendedorUser;
    protected User $clienteUser;
    protected Vendedor $vendedor;
    protected Autor $autor;
    protected Genero $genero;
    protected Editora $editora;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
        ]);
        Admin::create([
            'user_id' => $this->adminUser->id,
            'cargo' => Admin::CARGO_SUPER_ADMIN,
            'departamento' => 'TI',
        ]);

        // 2. Vendedor
        $this->vendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'status' => 'ativo',
        ]);
        $rand = rand(1000, 9999);
        $this->vendedor = Vendedor::create([
            'user_id' => $this->vendedorUser->id,
            'cnpj' => "55.{$rand}.111/0001-99",
            'razao_social' => 'Livraria Central LTDA',
            'nome_fantasia' => 'Livraria Central',
            'inscricao_estadual' => '123456789',
            'telefone_comercial' => '(11) 98888-1111',
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
        ]);

        // 3. Cliente
        $this->clienteUser = User::factory()->create([
            'tipo' => 'cliente',
            'status' => 'ativo',
        ]);
        Cliente::create([
            'user_id' => $this->clienteUser->id,
            'cpf' => rand(10000000000, 99999999999),
            'celular_contato' => '(11) 97777-2222',
            'data_nascimento' => '1995-05-15',
        ]);

        // 4. Entidades
        $this->autor = Autor::first() ?? Autor::create([
            'nome' => 'Clarice Lispector',
            'nacionalidade' => 'Brasileira',
            'data_nascimento' => '1920-12-10',
            'email' => 'clarice.' . rand(100, 9999) . '@literatura.com',
        ]);
        $this->genero = Genero::first() ?? Genero::create([
            'nome' => 'Ficção Moderna',
        ]);
        $this->editora = Editora::first() ?? Editora::create([
            'nome' => 'Rocco',
        ]);
    }

    /**
     * Testa endpoint de busca rápida/dinâmica via fetch retornando JSON.
     */
    public function test_endpoint_busca_rapida_retorna_json_correto(): void
    {
        $livro = Livro::create([
            'titulo' => 'A Hora da Estrela ' . rand(100, 999),
            'isbn' => '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-0',
            'data_publicacao' => '1977-10-01',
            'preco' => 39.90,
            'quantidade' => 10,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'História de Macabéa.',
        ]);

        // 1. Busca com termo menor que 2 caracteres retorna vazio
        $responseVazio = $this->getJson('/busca-rapida?q=a');
        $responseVazio->assertStatus(200);
        $responseVazio->assertExactJson([]);

        // 2. Busca válida com o título do livro
        $response = $this->getJson('/busca-rapida?q=' . urlencode('Hora da Estrela'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'titulo',
                'autor',
                'genero',
                'preco_formatado',
                'capa_url',
                'url',
            ]
        ]);
        $response->assertSee('A Hora da Estrela');
    }

    /**
     * Testa que o painel centralizado não exibe a mensagem antiga e reúne perfil e abas.
     */
    public function test_painel_centralizado_nao_exibe_mensagem_login_com_sucesso(): void
    {
        $response = $this->actingAs($this->clienteUser)->get(route('painel'));

        $response->assertStatus(200);
        // Garante que a mensagem desnecessária foi removida
        $response->assertDontSee('Login realizado com sucesso!');
        // Garante ambiente unificado de perfil e abas
        $response->assertSee('Visão Geral', false);
        $response->assertSee('Meus Dados & Perfil', false);
        $response->assertSee('Segurança & Conta', false);
    }

    /**
     * Testa exibição de notificações relevantes para os perfis.
     */
    public function test_painel_renderiza_notificacoes_relevantes_por_tipo_de_usuario(): void
    {
        // 1. Notificação para Admin: Vendedor Pendente
        $userNovoVendedor = User::factory()->create(['tipo' => 'vendedor']);
        Vendedor::create([
            'user_id' => $userNovoVendedor->id,
            'cnpj' => '11.' . rand(100, 999) . '.000/0001-99',
            'razao_social' => 'Sebo Teste LTDA',
            'nome_fantasia' => 'Sebo Teste Pendente',
            'inscricao_estadual' => '987654321',
            'telefone_comercial' => '(11) 98888-0000',
            'status_aprovacao' => Vendedor::STATUS_PENDENTE,
        ]);

        $responseAdmin = $this->actingAs($this->adminUser)->get(route('painel'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Solicitações de Vendedores Pendentes');

        // 2. Notificação para Vendedor: Livro com Estoque Baixo
        Livro::create([
            'titulo' => 'Livro Quase Esgotado ' . rand(100, 999),
            'isbn' => '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-1',
            'data_publicacao' => '2020-01-01',
            'preco' => 29.90,
            'quantidade' => 2, // Estoque baixo <= 3
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'Livro com poucas unidades restantes.',
        ]);

        $responseVendedor = $this->actingAs($this->vendedorUser)->get(route('painel'));
        $responseVendedor->assertStatus(200);
        $responseVendedor->assertSee('Estoque Baixo na sua Loja');

        // 3. Notificação para Cliente: Convite para virar vendedor
        $responseCliente = $this->actingAs($this->clienteUser)->get(route('painel'));
        $responseCliente->assertStatus(200);
        $responseCliente->assertSee('Abra sua Loja Parceira');
    }

    /**
     * Testa que a página inicial possui o botão de aplicar filtros e não recarrega automaticamente.
     */
    public function test_filtros_da_pagina_inicial_sao_disparados_por_botao_aplicar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Aplicar Filtros');
        $response->assertSee('Limpar Filtros');
        $response->assertDontSee('onchange="this.form.submit()"');
    }
}
