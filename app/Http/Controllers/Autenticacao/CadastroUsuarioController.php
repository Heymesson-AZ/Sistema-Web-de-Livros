<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CadastroUsuarioController extends Controller
{
    /**
     * Redireciona para a tela inicial (onde o modal de registro está presente).
     */
    public function exibirFormulario(): RedirectResponse
    {
        return redirect('/');
    }

    /**
     * Cadastra um novo usuário e seu registro de cliente associado.
     */
    public function cadastrar(Request $request): RedirectResponse
    {
        $datalimite = Carbon::now()->subYears(18)->format('Y-m-d');

        // Normaliza campos para formato padronizado antes da validação
        if ($request->has('cpf')) {
            $request->merge(['cpf' => preg_replace('/\D/', '', (string) $request->input('cpf'))]);
        }
        if ($request->has('telefone')) {
            $request->merge(['telefone' => preg_replace('/\D/', '', (string) $request->input('telefone'))]);
        }

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'size:11', 'unique:cliente,cpf'],
            'telefone' => ['required', 'string', 'min:10', 'max:15'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:' . $datalimite],
        ], [
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 100 caracteres.',
            'data_nascimento.before_or_equal' => 'Você precisa ter pelo menos 18 anos para se cadastrar.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Esse CPF já está cadastrado.',
            'email.unique' => 'O email informado já está em uso.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'cliente',
        ]);

        // Cria o registro do cliente associado ao usuário
        $user->cliente()->create([
            'cpf' => $request->cpf,
            'celular_contato' => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
        ]);

        // Dispara o evento de usuário registrado (envio de e-mail de confirmação)
        event(new Registered($user));

        Auth::login($user, true);

        return redirect(route('dashboard', absolute: false));
    }

    // =========================================================================
    // MÉTODOS DE COMPATIBILIDADE RETROATIVA (RESOURCE / LEGACY)
    // =========================================================================

    public function create(): RedirectResponse
    {
        return $this->exibirFormulario();
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->cadastrar($request);
    }
}
