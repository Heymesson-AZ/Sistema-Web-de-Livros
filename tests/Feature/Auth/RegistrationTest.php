<?php

use App\Models\User;

test('registration screen redirects to home page', function () {
    $response = $this->get('/register');

    $response->assertRedirect('/');
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'cpf' => '123.456.789-00',
        'telefone' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);


    $this->assertAuthenticated();

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'tipo' => 'cliente',
    ]);

    $this->assertDatabaseHas('cliente', [
        'cpf' => '123.456.789-00',
        'celular_contato' => '(61) 99999-9999',
    ]);
});
