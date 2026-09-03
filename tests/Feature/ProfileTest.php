<?php

use App\Models\User;

test('profile page is displayed for client', function () {
    $user = User::factory()->cliente()->create();

    $user->cliente()->create([
        'cpf' => fake()->unique()->numerify('###########'),
        'celular_contato' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/cliente/perfil-cliente');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->cliente()->create();

    $user->cliente()->create([
        'cpf' => fake()->unique()->numerify('###########'),
        'celular_contato' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
    ]);
    $newEmail = fake()->unique()->safeEmail();
    $response = $this
        ->actingAs($user)
        ->patch('/cliente/perfil-cliente', [
            'name' => 'Test User',
            'email' => $newEmail,
            'telefone' => '(61) 98888-8888',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/cliente/perfil-cliente');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame($newEmail, $user->email);
    $this->assertNull($user->email_verified_at);

    $this->assertDatabaseHas('cliente', [
        'user_id' => $user->id,
        'celular_contato' => '(61) 98888-8888',
    ]);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->cliente()->create();

    $user->cliente()->create([
        'cpf' => fake()->unique()->numerify('###########'),
        'celular_contato' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/cliente/perfil-cliente', [
            'name' => 'Test User',
            'email' => $user->email,
            'telefone' => '(61) 98888-8888',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/cliente/perfil-cliente');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->cliente()->create();

    $user->cliente()->create([
        'cpf' => fake()->unique()->numerify('###########'),
        'celular_contato' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/cliente/perfil-cliente', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertSoftDeleted($user);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->cliente()->create();

    $user->cliente()->create([
        'cpf' => fake()->unique()->numerify('###########'),
        'celular_contato' => '(61) 99999-9999',
        'data_nascimento' => '1990-01-01',
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/cliente/perfil-cliente')
        ->delete('/cliente/perfil-cliente', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/cliente/perfil-cliente');

    $this->assertNotNull($user->fresh());
});
