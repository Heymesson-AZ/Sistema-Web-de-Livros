<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <h2 class="font-bold text-xl text-green-600">Login realizado com sucesso!</h2>
            <p class="mt-4">Seja Bem-vindo ao nosso sistema!</p>

            <!-- Dados do Usuário logado -->
            <p class="text-gray-600">Você está logado como: {{ Auth::user()->name }}</p>
            <p class="text-gray-600">Email: {{ Auth::user()->email }}</p>
            <p class="text-gray-600">Tipo: {{ Auth::user()->tipo }}</p>
            <p class="text-gray-600">Status: {{ Auth::user()->status }}</p>

            @if(Auth::user()->tipo === 'cliente')
            <p class="text-gray-600">Celular de Contato: {{ Auth::user()->cliente?->celular_contato ?? 'Não informado' }}</p>

            @elseif(Auth::user()->tipo === 'vendedor')
            <p class="text-gray-600">WhatsApp Comercial: {{ Auth::user()->vendedor?->whatsapp_comercial ?? 'Não informado' }}</p>

            @elseif(Auth::user()->tipo === 'admin')
            <p class="text-gray-600">Telefone de Urgência: {{ Auth::user()->admin?->telefone_urgencia ?? 'Não informado' }}</p>
            <p class="text-gray-600">Cargo: {{ Auth::user()->admin?->cargo }}</p>
            @endif
        </div>
    </div>
</div>