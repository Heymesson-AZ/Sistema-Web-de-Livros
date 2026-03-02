<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <h2 class="font-bold text-xl text-green-600">✅ Cadastro e Login realizados com sucesso!</h2>
            <p class="mt-4">Bem-vindo, <strong>{{ Auth::user()->name }}</strong> (Tipo: {{ Auth::user()->tipo }})</p>

            <hr class="my-4">

            <h3 class="font-semibold text-lg">🔍 Verificação de Dados do Perfil:</h3>

            @if(Auth::user()->cliente)
            <ul class="mt-2">
                <li><strong>CPF:</strong> {{ Auth::user()->cliente->cpf }}</li>
                <li><strong>Telefone:</strong> {{ Auth::user()->cliente->telefone }}</li>
                <li><strong>Data de Nasc.:</strong> {{ Auth::user()->cliente->data_nascimento }}</li>
            </ul>
            @else
            <p class="text-red-500">❌ Erro: O registro na tabela 'clientes' não foi criado.</p>
            @endif

        </div>
    </div>
</div>