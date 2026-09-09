<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SegurancaEAutenticacaoAvancadaTest extends TestCase
{
    use RefreshDatabase;

    public function test_sessao_esta_configurada_para_15_dias_de_persistencia(): void
    {
        // 15 dias = 15 * 24 * 60 = 21600 minutos
        $this->assertEquals(21600, Config::get('session.lifetime'));
        $this->assertFalse(Config::get('session.expire_on_close'));
    }

    public function test_usuario_pode_autenticar_com_remember_me_ativo(): void
    {
        $user = User::factory()->create([
            'email' => 'leitor@universodepapel.com',
            'password' => Hash::make('SenhaForte123!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'leitor@universodepapel.com',
            'password' => 'SenhaForte123!',
            'remember' => '1',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        // Verifica se o cookie remember foi anexado
        $response->assertCookieNotExpired(auth()->guard()->getRecallerName());
    }

    public function test_login_normaliza_email_insensivel_a_maiusculas_e_minusculas(): void
    {
        $user = User::factory()->create([
            'email' => 'usuario.teste@universodepapel.com',
            'password' => Hash::make('SenhaValida123!'),
        ]);

        // Envia email com letras maiúsculas misturadas e espaços
        $response = $this->post('/login', [
            'email' => '  UsUaRiO.TeStE@UnIvErSoDePaPeL.CoM  ',
            'password' => 'SenhaValida123!',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_protecao_contra_ataques_de_forca_bruta_bloqueia_apos_5_tentativas_falhas(): void
    {
        $user = User::factory()->create([
            'email' => 'alvo@universodepapel.com',
            'password' => Hash::make('SenhaCerta123!'),
        ]);

        // 5 tentativas consecutivas com senha errada
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'alvo@universodepapel.com',
                'password' => 'SenhaErrada' . $i,
            ]);
        }

        // A 6ª tentativa deve ser bloqueada pelo RateLimiter (Too Many Requests / Throttle)
        $response = $this->post('/login', [
            'email' => 'alvo@universodepapel.com',
            'password' => 'SenhaErradaFinal',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $erros = session('errors')->get('email');
        $this->assertTrue(
            collect($erros)->contains(fn ($msg) => str_contains(strtolower($msg), 'tentativas') || str_contains(strtolower($msg), 'segundos'))
        );
    }

    public function test_padronizacao_interna_de_nome_em_title_case(): void
    {
        $user = User::create([
            'name' => '  joao   pedro   da   silva  ',
            'email' => 'JOAO@TESTE.COM',
            'password' => Hash::make('Senha12345!'),
            'tipo' => 'cliente',
        ]);

        $this->assertEquals('Joao Pedro Da Silva', $user->name);
        $this->assertEquals('joao@teste.com', $user->email);
    }
}
