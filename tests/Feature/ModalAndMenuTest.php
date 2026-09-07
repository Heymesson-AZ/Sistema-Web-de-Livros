<?php

use App\Models\User;

test('home page contains authentication modals and alpine components', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('id="loginModal"', false);
    $response->assertSee('id="registerModal"', false);
    $response->assertSee('id="forgotPasswordModal"', false);
});

test('auth routes redirect to home page where modals are loaded', function () {
    $this->get('/login')->assertRedirect('/');
    $this->get('/register')->assertRedirect('/');
    $this->get('/forgot-password')->assertRedirect('/');
});

test('authenticated user can view dashboard and modern navigation menu', function () {
    $user = User::factory()->cliente()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee('Sair');
});
