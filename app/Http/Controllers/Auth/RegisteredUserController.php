<?php

// Controlador responsável por lidar com o registro de novos usuários,
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


/// Controlador responsável por lidar com o registro de novos usuários, 
//  incluindo a exibição do formulário de registro e a 
//criação de um novo usuário após a validação dos dados do formulário.
class RegisteredUserController extends Controller
{
    /**
     * formulário de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Cria um novo usuário após a validação dos dados do formulário de registro.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    // O método store() é responsável por processar os dados do formulário de registro,
    // validar os dados, criar um novo usuário, disparar o evento Registered e autenticar o usuário recém-criado. 
    //Após o registro bem-sucedido, o usuário é redirecion
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', 'string', 'unique:clientes,cpf'],
            'telefone' => ['required', 'string'],
            'data_nascimento' => ['required', 'date'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Agora criamos o cliente ligado a esse usuário
        $user->cliente()->create([
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
