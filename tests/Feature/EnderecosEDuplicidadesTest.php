<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Cliente;
use App\Models\Editora;
use App\Models\Endereco;
use App\Models\Genero;
use App\Models\Livro;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnderecosEDuplicidadesTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;
    protected User $adminUser;
    protected Autor $autor;
    protected Genero $genero;
    protected Editora $editora;
    protected Vendedor $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clienteUser = User::factory()->create([
            'tipo' => 'cliente',
            'name' => 'Cliente Leitor',
            'email' => 'cliente@teste.com',
        ]);
        Cliente::create([
            'user_id' => $this->clienteUser->id,
            'cpf' => '11122233344',
            'celular_contato' => '11988887777',
            'data_nascimento' => '1995-05-15',
        ]);

        $this->adminUser = User::factory()->create([
            'tipo' => 'admin',
            'name' => 'Admin Chefe',
            'email' => 'admin@teste.com',
        ]);

        $vendedorUser = User::factory()->create([
            'tipo' => 'vendedor',
            'name' => 'Livraria Central',
            'email' => 'vendedor@teste.com',
        ]);
        $this->vendedor = Vendedor::create([
            'user_id' => $vendedorUser->id,
            'cnpj' => '12345678000199',
            'razao_social' => 'Livraria Central LTDA',
            'nome_fantasia' => 'Livraria Central',
            'inscricao_estadual' => '123456789',
            'status_aprovacao' => 'aprovado',
        ]);

        $this->autor = Autor::first() ?? Autor::create([
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileira',
            'data_nascimento' => '1839-06-21',
            'email' => 'machado.' . uniqid() . '@literatura.com',
        ]);
        $this->genero = Genero::first() ?? Genero::create(['nome' => 'Literatura Brasileira']);
        $this->editora = Editora::first() ?? Editora::create(['nome' => 'Companhia das Letras']);
    }

    public function test_usuario_pode_cadastrar_multiplos_enderecos_com_tipos_diferentes(): void
    {
        // 1. Cadastra endereço residencial principal
        $res1 = $this->actingAs($this->clienteUser)->post(route('enderecos.salvar'), [
            'tipo' => 'residencial',
            'cep' => '01310-100',
            'rua' => 'Avenida Paulista',
            'numero' => '1000',
            'complemento' => 'Apto 101',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'principal' => '1',
        ]);

        $res1->assertRedirect();
        $this->assertDatabaseHas('enderecos', [
            'user_id' => $this->clienteUser->id,
            'tipo' => 'residencial',
            'rua' => 'Avenida Paulista',
            'cep' => '01310-100',
            'principal' => 1,
        ]);

        // 2. Cadastra segundo endereço comercial não-principal
        $res2 = $this->actingAs($this->clienteUser)->post(route('enderecos.salvar'), [
            'tipo' => 'comercial',
            'cep' => '70040-010',
            'rua' => 'Esplanada dos Ministérios',
            'numero' => 'Bloco A',
            'complemento' => 'Sala 200',
            'bairro' => 'Zona Cívico-Administrativa',
            'cidade' => 'Brasília',
            'estado' => 'DF',
        ]);

        $res2->assertRedirect();
        $this->assertDatabaseHas('enderecos', [
            'user_id' => $this->clienteUser->id,
            'tipo' => 'comercial',
            'cidade' => 'Brasília',
            'principal' => 0,
        ]);

        $this->assertCount(2, $this->clienteUser->enderecos);
    }

    public function test_definir_novo_endereco_principal_desmarca_o_anterior(): void
    {
        $end1 = Endereco::create([
            'user_id' => $this->clienteUser->id,
            'tipo' => 'residencial',
            'cep' => '01310-100',
            'rua' => 'Rua Um',
            'numero' => '10',
            'bairro' => 'Bairro Um',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'principal' => true,
        ]);

        $end2 = Endereco::create([
            'user_id' => $this->clienteUser->id,
            'tipo' => 'comercial',
            'cep' => '20040-000',
            'rua' => 'Rua Dois',
            'numero' => '20',
            'bairro' => 'Centro',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'principal' => false,
        ]);

        // Define $end2 como principal
        $response = $this->actingAs($this->clienteUser)
            ->post(route('enderecos.definir-principal', $end2));

        $response->assertRedirect();

        $this->assertDatabaseHas('enderecos', ['id' => $end1->id, 'principal' => 0]);
        $this->assertDatabaseHas('enderecos', ['id' => $end2->id, 'principal' => 1]);
    }

    public function test_usuario_nao_pode_excluir_endereco_de_outro_usuario(): void
    {
        $outroUser = User::factory()->create();
        $endOutro = Endereco::create([
            'user_id' => $outroUser->id,
            'tipo' => 'residencial',
            'cep' => '01310-100',
            'rua' => 'Rua Privada',
            'numero' => '99',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'principal' => true,
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->delete(route('enderecos.deletar', $endOutro));

        $response->assertStatus(403);
        $this->assertDatabaseHas('enderecos', ['id' => $endOutro->id]);
    }

    public function test_bloqueio_de_duplicidade_de_isbn_no_cadastro_de_livros(): void
    {
        Livro::create([
            'titulo' => 'Dom Casmurro Edição 1',
            'isbn' => '9788535902778',
            'data_publicacao' => '1899-01-01',
            'preco' => 35.00,
            'quantidade' => 10,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
        ]);

        // Tentativa de cadastrar outro livro com o mesmo ISBN
        $response = $this->actingAs($this->adminUser)->post(route('admin.livros.store'), [
            'titulo' => 'Dom Casmurro Edição 2',
            'isbn' => '978-85-359-0277-8', // Com formatação (o mutator/controller normaliza e detecta)
            'data_publicacao' => '1900-01-01',
            'preco' => 40.00,
            'quantidade' => 5,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
        ]);

        $response->assertSessionHasErrors('isbn');
    }

    public function test_bloqueio_de_duplicidade_de_titulo_para_o_mesmo_autor(): void
    {
        Livro::create([
            'titulo' => 'Memórias Póstumas de Brás Cubas',
            'isbn' => '9788535900001',
            'data_publicacao' => '1881-01-01',
            'preco' => 39.90,
            'quantidade' => 8,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
        ]);

        // Tentativa de cadastrar o mesmo título para o mesmo autor com ISBN diferente
        $response = $this->actingAs($this->adminUser)->post(route('admin.livros.store'), [
            'titulo' => 'Memórias Póstumas de Brás Cubas',
            'isbn' => '9788535900002',
            'data_publicacao' => '1881-01-01',
            'preco' => 45.00,
            'quantidade' => 5,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
        ]);

        $response->assertSessionHasErrors('titulo');
    }

    public function test_sanitizacao_de_preco_formatado_em_reais(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.livros.store'), [
            'titulo' => 'Esaú e Jacó',
            'isbn' => '9788535901111',
            'data_publicacao' => '1904-01-01',
            'preco' => 'R$ 54,90',
            'quantidade' => 15,
            'autor_id' => $this->autor->id,
            'genero_id' => $this->genero->id,
            'editora_id' => $this->editora->id,
            'vendedor_id' => $this->vendedor->id,
        ]);

        $response->assertRedirect(route('admin.livros.index'));
        $this->assertDatabaseHas('livros', [
            'titulo' => 'Esaú e Jacó',
            'preco' => 54.90,
        ]);
    }
}
