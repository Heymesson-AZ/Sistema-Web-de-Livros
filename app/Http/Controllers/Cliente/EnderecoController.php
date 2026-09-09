<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EnderecoController extends Controller
{
    /**
     * Listar os endereços do usuário autenticado (JSON para consumo assíncrono).
     */
    public function listar(Request $request): JsonResponse
    {
        $enderecos = $request->user()->enderecos()->orderByDesc('principal')->latest()->get();

        return response()->json($enderecos);
    }

    /**
     * Salvar um novo endereço vinculado ao usuário conectado.
     */
    public function salvar(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'tipo' => ['required', 'string', 'max:30'],
            'cep' => ['required', 'string', 'min:8', 'max:10'],
            'rua' => ['required', 'string', 'min:2', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'bairro' => ['required', 'string', 'min:2', 'max:100'],
            'cidade' => ['required', 'string', 'min:2', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'principal' => ['nullable', 'boolean'],
        ], [
            'tipo.required' => 'O tipo de endereço é obrigatório (ex: Casa, Trabalho).',
            'cep.required' => 'O CEP é obrigatório.',
            'rua.required' => 'A rua/logradouro é obrigatória.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado (UF) é obrigatório.',
            'estado.size' => 'O estado deve ter exatamente 2 letras (ex: SP).',
        ]);

        $user = $request->user();
        $isFirst = $user->enderecos()->count() === 0;
        $definirPrincipal = $request->boolean('principal') || $isFirst;

        DB::transaction(function () use ($user, $dados, $definirPrincipal) {
            if ($definirPrincipal) {
                $user->enderecos()->update(['principal' => false]);
            }

            $user->enderecos()->create([
                'tipo' => mb_convert_case(trim($dados['tipo']), MB_CASE_TITLE, 'UTF-8'),
                'cep' => trim($dados['cep']),
                'rua' => trim($dados['rua']),
                'numero' => trim($dados['numero']),
                'bairro' => trim($dados['bairro']),
                'cidade' => trim($dados['cidade']),
                'estado' => strtoupper(trim($dados['estado'])),
                'pais' => 'Brasil',
                'complemento' => $dados['complemento'] ? trim($dados['complemento']) : null,
                'principal' => $definirPrincipal,
            ]);
        });

        return redirect()->route('painel', ['tab' => 'enderecos'])
            ->with('status', 'Endereço cadastrado com sucesso!');
    }

    /**
     * Atualizar um endereço existente do usuário conectado.
     */
    public function atualizar(Request $request, Endereco $endereco): RedirectResponse
    {
        if ($endereco->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado a este endereço.');
        }

        $dados = $request->validate([
            'tipo' => ['required', 'string', 'max:30'],
            'cep' => ['required', 'string', 'min:8', 'max:10'],
            'rua' => ['required', 'string', 'min:2', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'bairro' => ['required', 'string', 'min:2', 'max:100'],
            'cidade' => ['required', 'string', 'min:2', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'principal' => ['nullable', 'boolean'],
        ], [
            'tipo.required' => 'O tipo de endereço é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'rua.required' => 'A rua/logradouro é obrigatória.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado (UF) é obrigatório.',
            'estado.size' => 'O estado deve ter exatamente 2 letras.',
        ]);

        $user = $request->user();
        $definirPrincipal = $request->boolean('principal');

        DB::transaction(function () use ($user, $endereco, $dados, $definirPrincipal) {
            if ($definirPrincipal) {
                $user->enderecos()->where('id', '!=', $endereco->id)->update(['principal' => false]);
            }

            $endereco->update([
                'tipo' => mb_convert_case(trim($dados['tipo']), MB_CASE_TITLE, 'UTF-8'),
                'cep' => trim($dados['cep']),
                'rua' => trim($dados['rua']),
                'numero' => trim($dados['numero']),
                'bairro' => trim($dados['bairro']),
                'cidade' => trim($dados['cidade']),
                'estado' => strtoupper(trim($dados['estado'])),
                'complemento' => $dados['complemento'] ? trim($dados['complemento']) : null,
                'principal' => $definirPrincipal || $endereco->principal,
            ]);
        });

        return redirect()->route('painel', ['tab' => 'enderecos'])
            ->with('status', 'Endereço atualizado com sucesso!');
    }

    /**
     * Definir um endereço específico como principal.
     */
    public function definirPrincipal(Request $request, Endereco $endereco): RedirectResponse
    {
        if ($endereco->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        DB::transaction(function () use ($request, $endereco) {
            $request->user()->enderecos()->update(['principal' => false]);
            $endereco->update(['principal' => true]);
        });

        return redirect()->route('painel', ['tab' => 'enderecos'])
            ->with('status', 'Endereço principal atualizado com sucesso!');
    }

    /**
     * Excluir endereço do usuário.
     */
    public function deletar(Request $request, Endereco $endereco): RedirectResponse
    {
        if ($endereco->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $foiPrincipal = $endereco->principal;
        $user = $request->user();

        DB::transaction(function () use ($user, $endereco, $foiPrincipal) {
            $endereco->delete();

            // Se o endereço excluído era o principal, define o próximo como principal
            if ($foiPrincipal) {
                $proximo = $user->enderecos()->first();
                if ($proximo) {
                    $proximo->update(['principal' => true]);
                }
            }
        });

        return redirect()->route('painel', ['tab' => 'enderecos'])
            ->with('status', 'Endereço removido com sucesso.');
    }
}

