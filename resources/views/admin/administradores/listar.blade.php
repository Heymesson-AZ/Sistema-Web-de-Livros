<x-layouts.principal>
    <div class="container py-4">

        <!-- CABEÇALHO DA PÁGINA -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-shield-check text-primary me-2"></i>
                    Gestão de Administradores
                </h1>
                <p class="text-muted small mb-0">
                    Gerencie o acesso da equipe executiva, gerencial e de suporte da Universo de Papel.
                </p>
            </div>
            <a href="{{ route('admin.administradores.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i>
                <span>Novo Administrador</span>
            </a>
        </div>

        <!-- MENSAGENS DE STATUS E ERRO -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- CARDS DE MÉTRICAS (KPIS) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total de Administradores</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalAdmins }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Super Administradores</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalSuperAdmins }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fdf4ff; color: #9333ea;">
                            <i class="bi bi-star-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Departamentos Ativos</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalDepartamentos }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #f0fdf4; color: #16a34a;">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS E BUSCA -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <form method="GET" action="{{ route('admin.administradores.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="busca" class="form-control border-start-0 ps-0" placeholder="Buscar por nome, e-mail ou telefone..." value="{{ request('busca') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="departamento" class="form-select">
                        <option value="">Todos Departamentos</option>
                        @foreach ($departamentos as $dep)
                            <option value="{{ $dep }}" {{ request('departamento') === $dep ? 'selected' : '' }}>{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <select name="cargo" class="form-select">
                        <option value="">Todos os Cargos</option>
                        @foreach ($cargos as $cg)
                            <option value="{{ $cg }}" {{ request('cargo') === $cg ? 'selected' : '' }}>{{ $cg }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                        <i class="bi bi-filter"></i>
                    </button>
                    @if(request()->hasAny(['busca', 'departamento', 'cargo']))
                        <a href="{{ route('admin.administradores.index') }}" class="btn btn-outline-secondary" title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABELA DE ADMINISTRADORES -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Administrador</th>
                            <th class="py-3">Cargo</th>
                            <th class="py-3">Departamento</th>
                            <th class="py-3">Telefone de Urgência</th>
                            <th class="py-3">Data de Ingresso</th>
                            <th class="text-end pe-4 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($administradores as $admin)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                            style="width: 42px; height: 42px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); font-size: 14px;">
                                            {{ strtoupper(substr($admin->user->name ?? 'A', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <span>{{ $admin->user->name ?? 'Sem Nome' }}</span>
                                                @if(Auth::id() === $admin->user_id)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 10px;">Você</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $admin->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @php
                                        $badgeClass = match($admin->cargo) {
                                            \App\Models\Admin::CARGO_SUPER_ADMIN => 'bg-purple-subtle text-purple border-purple-subtle',
                                            \App\Models\Admin::CARGO_GERENTE_CATALOGO, \App\Models\Admin::CARGO_GERENTE_COMERCIAL => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                            default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill" style="font-size: 11px;">
                                        {{ $admin->cargo }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size: 11px;">
                                        {{ $admin->departamento }}
                                    </span>
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $admin->telefone_urgencia ?: 'Não informado' }}
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $admin->created_at ? $admin->created_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.administradores.show', $admin) }}" class="btn btn-sm btn-outline-secondary" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.administradores.edit', $admin) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        @if(Auth::id() !== $admin->user_id)
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $admin->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- Modal de Confirmação de Exclusão -->
                                            <div class="modal fade" id="deleteModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
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
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary disabled" title="Você não pode excluir sua própria conta">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-search text-muted fs-1 mb-3 d-block"></i>
                                        <h5 class="text-secondary fw-semibold">Nenhum administrador encontrado</h5>
                                        <p class="text-muted small">Tente ajustar os termos de busca ou filtros aplicados.</p>
                                        @if(request()->hasAny(['busca', 'departamento', 'cargo']))
                                            <a href="{{ route('admin.administradores.index') }}" class="btn btn-sm btn-outline-primary rounded-3">
                                                Limpar Filtros
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINAÇÃO -->
            @if ($administradores->hasPages())
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $administradores->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>
