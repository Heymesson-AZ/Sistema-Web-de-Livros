<?php

use App\Models\User;
use App\Models\Cliente;
use Carbon\Carbon;

test('registration fails when name is shorter than 3 characters', function () {
    $response = $this->post('/register', [
        'name' => 'Ab',
        'email' => 'valid@example.com',
        'cpf' => '111.222.333-44',
        'telefone' => '(61) 98888-7777',
        'data_nascimento' => '1995-05-10',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['name' => 'O nome deve ter pelo menos 3 caracteres.']);
    $this->assertGuest();
});

test('registration fails when name is longer than 100 characters', function () {
    $response = $this->post('/register', [
        'name' => str_repeat('A', 101),
        'email' => 'valid@example.com',
        'cpf' => '111.222.333-44',
        'telefone' => '(61) 98888-7777',
        'data_nascimento' => '1995-05-10',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['name' => 'O nome não pode ter mais de 100 caracteres.']);
    $this->assertGuest();
});

test('registration fails when user is under 18 years old', function () {
    $underageBirthdate = Carbon::now()->subYears(17)->format('Y-m-d');

    $response = $this->post('/register', [
        'name' => 'Menor de Idade',
        'email' => 'menor@example.com',
        'cpf' => '222.333.444-55',
        'telefone' => '(61) 98888-7777',
        'data_nascimento' => $underageBirthdate,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['data_nascimento' => 'Você precisa ter pelo menos 18 anos para se cadastrar.']);
    $this->assertGuest();
});

test('registration fails when email is already registered', function () {
    User::factory()->create(['email' => 'duplicado@example.com']);

    $response = $this->post('/register', [
        'name' => 'Novo Usuário',
        'email' => 'duplicado@example.com',
        'cpf' => '333.444.555-66',
        'telefone' => '(61) 98888-7777',
        'data_nascimento' => '1990-01-01',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'O email informado já está em uso.']);
    $this->assertGuest();
});

test('registration fails when cpf is already registered', function () {
    $user = User::factory()->create();
    Cliente::create([
        'user_id' => $user->id,
        'cpf' => '444.555.666-77',
        'celular_contato' => '(61) 91111-2222',
        'data_nascimento' => '1985-06-15',
    ]);

    $response = $this->post('/register', [
        'name' => 'Outro Cliente',
        'email' => 'outro@example.com',
        'cpf' => '444.555.666-77',
        'telefone' => '(61) 99999-0000',
        'data_nascimento' => '1992-03-20',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['cpf' => 'Esse CPF já está cadastrado.']);
    $this->assertGuest();
});

