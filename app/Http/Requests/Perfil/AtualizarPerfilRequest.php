<?php

namespace App\Http\Requests\Perfil;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarPerfilRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para os campos do perfil do usuário.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Regras comuns a todos os usuários
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        // Regras específicas por tipo de usuário
        if ($this->user()->isVendedor()) {
            $rules['telefone'] = ['required', 'string', 'max:20'];
            $rules['razao_social'] = ['required', 'string', 'max:255'];
            $rules['nome_fantasia'] = ['required', 'string', 'max:255'];
            $rules['inscricao_estadual'] = ['nullable', 'string', 'max:50'];
        } elseif ($this->user()->isCliente()) {
            $rules['telefone'] = ['required', 'string', 'max:20'];
        } elseif ($this->user()->isAdmin()) {
            $rules['telefone_urgencia'] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    /**
     * Mensagens de validação customizadas em português.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode exceder 100 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
            'telefone.required' => 'O telefone é obrigatório.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
        ];
    }
}
