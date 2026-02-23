<?php

// FormRequest responsável por lidar com a validação e atualização das informações do perfil do usuário,
// incluindo a validação do nome e email, e garantindo que o email seja único, exc
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Aplica as regras de validação para os campos do perfil do usuário, 
     * garantindo que o nome seja obrigatório,
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 1. Regras que SEMPRE existem (para todos)
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // A regra de unicidade IGNORA o email do usuário atual, permitindo que ele mantenha seu email sem erro
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        // 2. Adicionando regras específicas baseadas no tipo
        if ($this->user()->isVendedor()) {
            $rules['telefone'] = ['required', 'string', 'max:20'];
            $rules['razao_social'] = ['required', 'string', 'max:255'];
            $rules['nome_fantasia'] = ['required', 'string', 'max:255'];
            $rules['inscricao_estadual'] = ['nullable', 'string', 'max:50'];
        } elseif ($this->user()->isCliente()) {
            $rules['telefone'] = ['required', 'string', 'max:20'];
            // Note que NÃO incluímos o CPF aqui para ele não ser alterado!
        }
        return $rules;
    }
}
