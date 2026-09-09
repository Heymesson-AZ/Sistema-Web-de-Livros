<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LivroCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $vendedorUser;
    protected Vendedor $vendedor;
    protected User $outroVendedorUser;
    protected Vendedor $outroVendedor;
    protected Autor $autor;
    protected Genero $genero;
    protected Editora $editora;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Admin
        $this->adminUser = User::factory()->create([
            'tipo' => 'admin',
            'status' => 'ativo',
        ]);
        Admin::create([
            'user_id' => $this->adminUser->id,
            'cargo' => Admin::CARGO_SUPER_ADMIN,
            'departamento' => 'Tecnologia da Informação',
        ]);

        // 2. Setup Vendedor Aprovado
        $rand1 = rand(1000, 9999);
        $this->vendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'status' => 'ativo',
        ]);
        $this->vendedor = Vendedor::create([
            'user_id' => $this->vendedorUser->id,
            'cnpj' => "12.{$rand1}.678/0001-90",
            'telefone_comercial' => '(11) 99999-8888',
            'razao_social' => 'Livraria Alpha LTDA',
            'nome_fantasia' => 'Alpha Books',
            'inscricao_estadual' => '123.456.789.000',
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
        ]);

        // 3. Setup Segundo Vendedor para teste de isolamento/permissões
        $rand2 = rand(1000, 9999);
        $this->outroVendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'status' => 'ativo',
        ]);
        $this->outroVendedor = Vendedor::create([
            'user_id' => $this->outroVendedorUser->id,
            'cnpj' => "98.{$rand2}.432/0001-10",
            'telefone_comercial' => '(21) 98888-7777',
            'razao_social' => 'Sebo Beta LTDA',
            'nome_fantasia' => 'Beta Livros',
            'inscricao_estadual' => '987.654.321.000',
            'status_aprovacao' => Vendedor::STATUS_APROVADO,
        ]);

        // 4. Entidades relacionadas
        $this->autor = Autor::first() ?? Autor::create([
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileira',
            'data_nascimento' => '1839-06-21',
            'email' => 'machado.' . rand(100, 9999) . '@literatura.com',
        ]);
        $this->genero = Genero::first() ?? Genero::create([
            'nome' => 'Literatura Brasileira',
        ]);
        $this->editora = Editora::first() ?? Editora::create([
            'nome' => 'Companhia das Letras',
        ]);
    }

    /**
     * Testa acesso público da página inicial e elementos requisitados (Carrossel, Filtros, Rodapé com Heymesson Azevedo).
     */
    public function test_pagina_inicial_renderiza_com_carrosel_filtros_e_rodape(): void
    {
        $isbn = '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-' . rand(0, 9);
        $livro = Livro::create([
            'titulo' => 'Dom Casmurro Edição Especial ' . rand(10, 99),
            'isbn' => $isbn,
            'data_publicacao' => '1899-01-01',
            'preco' => 49.90,
            'quantidade' => 15,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'Obra-prima de Machado de Assis com Bentinho e Capitu.',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Verifica Carrossel Vanilla JS
        $response->assertSee('carousel-track');
        // Verifica Filtros estilo Amazon
        $response->assertSee('Filtros');
        $response->assertSee('faixa_preco');
        // Verifica Rodapé
        $response->assertSee('Heymesson Azevedo');
        $response->assertSee('Universo de Papel');
    }

    /**
     * Testa visualização pública dos detalhes do livro.
     */
    public function test_visitante_pode_visualizar_detalhes_do_livro(): void
    {
        $isbn = '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-' . rand(0, 9);
        $livro = Livro::create([
            'titulo' => 'Memórias Póstumas de Brás Cubas ' . rand(10, 99),
            'isbn' => $isbn,
            'data_publicacao' => '1881-01-01',
            'preco' => 59.90,
            'quantidade' => 8,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'Ao verme que primeiro roeu as frias carnes do meu cadáver...',
        ]);

        $response = $this->get(route('livros.show', $livro));

        $response->assertStatus(200);
        $response->assertSee($this->autor->nome);
        $response->assertSee($this->vendedor->nome_fantasia);
        $response->assertSee('59,90');
    }

    /**
     * Testa CRUD de livros pelo Administrador.
     */
    public function test_admin_pode_gerenciar_crud_completo_de_livros(): void
    {
        // 1. Acesso à listagem
        $response = $this->actingAs($this->adminUser)->get(route('admin.livros.index'));
        $response->assertStatus(200);
        $response->assertSee('Catálogo de Livros');
        $response->assertSee('Moderação do Catálogo');

        // 2. Formulário de cadastro
        // 2. Formulário de cadastro redireciona admin para listagem (publicação exclusiva de lojistas)
        $response = $this->actingAs($this->adminUser)->get(route('admin.livros.create'));
        $response->assertStatus(200);
        $response->assertRedirect(route('admin.livros.index'));
        $response->assertSessionHas('info');

        // 3. Cadastrar livro via POST
        // 3. Livro cadastrado para moderação e inspeção pelo admin
        $isbn = '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-' . rand(0, 9);
        $response = $this->actingAs($this->adminUser)->post(route('admin.livros.store'), [
        $isbnLimpo = preg_replace('/[^0-9Xx]/', '', $isbn);
        $livro = Livro::create([
            'titulo' => 'O Alienista Teste',
            'isbn' => $isbn,
            'isbn' => $isbnLimpo,
            'data_publicacao' => '1882-03-15',
            'preco' => 35.50,
            'quantidade' => 20,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'Simão Bacamarte e o manicômio de Itaguaí.',
            'status_moderacao' => Livro::STATUS_MODERACAO_ATIVO,
        ]);

        $response->assertRedirect(route('admin.livros.index'));
        $response->assertSessionHas('status');
        $isbnLimpo = preg_replace('/[^0-9Xx]/', '', $isbn);
        $this->assertDatabaseHas('livros', [
            'titulo' => 'O Alienista Teste',
            'isbn' => $isbnLimpo,
        ]);

        $livro = Livro::where('isbn', $isbnLimpo)->first();

        // 4. Detalhes administrativos
        $response = $this->actingAs($this->adminUser)->get(route('admin.livros.show', $livro));
        $response->assertStatus(200);
        $response->assertSee('O Alienista Teste');

        // 5. Atualização via PUT
        $response = $this->actingAs($this->adminUser)->put(route('admin.livros.update', $livro), [
            'titulo' => 'O Alienista - Edição Luxo',
            'isbn' => $isbn,
            'data_publicacao' => '1882-03-15',
            'preco' => 45.00,
            'quantidade' => 25,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
            'sinopse' => 'Edição revisada com notas e capa dura.',
        ]);

        $response->assertRedirect(route('admin.livros.index'));
        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'titulo' => 'O Alienista - Edição Luxo',
            'preco' => 45.00,
        ]);

        // 6. Exclusão segura (Soft Delete)
        $response = $this->actingAs($this->adminUser)->delete(route('admin.livros.destroy', $livro));
        $response->assertRedirect(route('admin.livros.index'));
        $this->assertSoftDeleted('livros', ['id' => $livro->id]);
    }

    /**
     * Testa CRUD de livros pelo Vendedor e isolamento da loja.
     */
    public function test_vendedor_pode_gerenciar_seus_proprios_livros_e_nao_de_outros(): void
    {
        // 1. Listagem do vendedor
        $response = $this->actingAs($this->vendedorUser)->get(route('vendedor.livros.index'));
        $response->assertStatus(200);
        $response->assertSee('Catálogo da Minha Loja');

        // 2. Cadastrar livro na sua loja
        $isbn = '978-' . rand(100, 999) . '-' . rand(1000, 9999) . '-' . rand(0, 9);
        $response = $this->actingAs($this->vendedorUser)->post(route('vendedor.livros.store'), [
            'titulo' => 'Quincas Borba Teste',
            'isbn' => $isbn,
            'data_publicacao' => '1891-05-10',
            'preco' => 42.00,
            'quantidade' => 12,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'sinopse' => 'Ao vencedor, as batatas!',
        ]);

        $response->assertRedirect(route('vendedor.livros.index'));
        $this->assertDatabaseHas('livros', [
            'titulo' => 'Quincas Borba Teste',
            'vendedor_id' => $this->vendedor->id,
        ]);

        $isbnLimpo = preg_replace('/[^0-9Xx]/', '', $isbn);
        $livro = Livro::where('isbn', $isbnLimpo)->first();

        // 3. Vendedor pode editar seu livro
        $response = $this->actingAs($this->vendedorUser)->put(route('vendedor.livros.update', $livro), [
            'titulo' => 'Quincas Borba - Atualizado',
            'isbn' => $isbn,
            'data_publicacao' => '1891-05-10',
            'preco' => 48.00,
            'quantidade' => 10,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'sinopse' => 'Sinopse atualizada.',
        ]);
        $response->assertRedirect(route('vendedor.livros.index'));
        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'titulo' => 'Quincas Borba - Atualizado',
        ]);

        // 4. Segurança / Isolamento: Outro vendedor NÃO PODE editar ou deletar este livro
        $response = $this->actingAs($this->outroVendedorUser)->get(route('vendedor.livros.edit', $livro));
        $response->assertStatus(403);

        $response = $this->actingAs($this->outroVendedorUser)->delete(route('vendedor.livros.destroy', $livro));
        $response->assertStatus(403);

        // 5. O próprio vendedor pode excluir seu livro (Soft Delete)
        $response = $this->actingAs($this->vendedorUser)->delete(route('vendedor.livros.destroy', $livro));
        $response->assertRedirect(route('vendedor.livros.index'));
        $this->assertSoftDeleted('livros', ['id' => $livro->id]);
    }
}
