<x-layouts.principal>
    <div class="container py-3 py-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-3">
                    <i data-lucide="check-circle" class="text-success me-2" style="width: 28px; height: 28px;"></i>
                    <h2 class="h4 mb-0 text-success fw-bold">Login realizado com sucesso!</h2>
                </div>

                <p class="text-muted">Seja bem-vindo ao nosso sistema, <strong>{{ Auth::user()->name }}</strong>!</p>

                <hr class="my-3">

                <!-- Dados do Usuário logado -->
                <h5 class="fw-bold mb-3">Informações da Conta</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><strong>Nome:</strong> {{ Auth::user()->name }}</li>
                    <li class="mb-2"><strong>E-mail:</strong> {{ Auth::user()->email }}</li>
                    <li class="mb-2"><strong>Tipo de Conta:</strong> <span
                            class="badge bg-primary text-capitalize">{{ Auth::user()->tipo }}</span></li>
                    <li class="mb-2"><strong>Status:</strong> <span
                            class="badge bg-success text-capitalize">{{ Auth::user()->status }}</span></li>

                    @if (Auth::user()->tipo === 'cliente')
                        <li class="mb-2"><strong>Celular de Contato:</strong>
                            {{ Auth::user()->cliente?->celular_contato ?? 'Não informado' }}</li>
                    @elseif(Auth::user()->tipo === 'vendedor')
                        <li class="mb-2"><strong>WhatsApp Comercial:</strong>
                            {{ Auth::user()->vendedor?->whatsapp_comercial ?? 'Não informado' }}</li>
                    @elseif(Auth::user()->tipo === 'admin')
                        <li class="mb-2"><strong>Telefone de Urgência:</strong>
                            {{ Auth::user()->admin?->telefone_urgencia ?? 'Não informado' }}</li>
                        <li class="mb-2"><strong>Cargo:</strong> {{ Auth::user()->admin?->cargo ?? 'Administrador' }}
                        </li>
                        <li class="mb-2"><strong>Departamento:</strong>
                            {{ Auth::user()->admin?->departamento ?? 'Geral' }}</li>
                    @endif
                </ul>

                @if (Auth::user()->tipo === 'admin')
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('admin.administradores.index') }}"
                            class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-4 py-2">
                            <i data-lucide="shield-check"></i>
                            <span>Gerenciar Administradores</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.principal>
