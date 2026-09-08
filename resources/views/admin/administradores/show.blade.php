<x-layouts.app>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.administradores.index') }}" class="text-decoration-none text-muted">Administradores</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $admin->user->name }}</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-person-badge text-primary me-2"></i>
                    Perfil do Administrador
                </h1>
                <p class="text-muted small mb-0">
                    Detalhes do membro da equipe executiva e administrativa.
                </p>
            </div>
            <a href="{{ route('admin.administradores.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <!-- CARD PRINCIPAL -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <!-- BANNER SUPERIOR -->
                    <div class="p-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-white fw-bold shadow"
                                style="width: 64px; height: 64px; font-size: 22px;">
                                {{ strtoupper(substr($admin->user->name ?? 'A', 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">{{ $admin->user->name }}</h4>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-semibold">
                                        {{ $admin->cargo }}
                                    </span>
                                    <span class="badge bg-light bg-opacity-25 text-white px-3 py-1 rounded-pill">
                                        {{ $admin->departamento }}
                                    </span>
                                    @if(Auth::id() === $admin->user_id)
                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Sua Conta</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CORPO DOS DETALHES -->
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Informações Cadastrais</h5>

                        <div class="row g-4">
                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">E-mail Corporativo</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-envelope text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->user->email }}</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Telefone de Urgência</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-telephone text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->telefone_urgencia ?: 'Não informado' }}</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Departamento</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->departamento }}</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Nível de Função (Cargo)</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-award text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->cargo }}</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Data de Ingresso</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Última Atualização</span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-primary"></i>
                                    <span class="text-dark fw-medium">{{ $admin->updated_at ? $admin->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- AÇÕES -->
                        <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                            <a href="{{ route('admin.administradores.index') }}" class="btn btn-light rounded-3 px-3">
                                <i class="bi bi-arrow-left me-1"></i> Voltar
                            </a>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.administradores.edit', $admin) }}" class="btn btn-primary rounded-3 px-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Editar Administrador</span>
                                </a>

                                @if(Auth::id() !== $admin->user_id)
                                    <button type="button" class="btn btn-outline-danger rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#deleteModalShow">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Modal de Exclusão -->
                                    <div class="modal fade" id="deleteModalShow" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 pt-4 px-4">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 40px; height: 40px; background-color: #fef2f2; color: #dc2626;">
                                                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-0">Confirmar Exclusão</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body px-4 py-3 text-start">
                                                    <p class="text-muted mb-2">
                                                        Tem certeza de que deseja excluir o administrador <strong>{{ $admin->user->name }}</strong>?
                                                    </p>
                                                    <p class="small text-danger mb-0">
                                                        <i class="bi bi-info-circle me-1"></i> Esta ação removerá o acesso executivo e a conta do usuário do sistema.
                                                    </p>
                                                </div>
                                                <div class="modal-footer border-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST" action="{{ route('admin.administradores.destroy', $admin) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-3 px-3">
                                                            Excluir Administrador
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>
