<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Meu Perfil de Administrador</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">

                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                style="width: 56px; height: 56px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); font-size: 20px;">
                                {{ strtoupper(substr($user->name ?? 'A', 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Meu Perfil de Administrador</h4>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        {{ $admin->cargo ?? 'Administrador' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border rounded-pill">
                                        {{ $admin->departamento ?? 'Geral' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if (session('status') === 'perfil-atualizado')
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>Perfil atualizado com sucesso!</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.perfil.atualizar') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold text-secondary">Nome Completo</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" minlength="3" maxlength="100" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-secondary">E-mail</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="telefone_urgencia" class="form-label small fw-bold text-secondary">Telefone de Urgência / Plantão</label>
                                <input type="text" id="telefone_urgencia" name="telefone_urgencia" class="form-control @error('telefone_urgencia') is-invalid @enderror"
                                    value="{{ old('telefone_urgencia', $admin->telefone_urgencia ?? '') }}" placeholder="(00) 00000-0000">
                                @error('telefone_urgencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3 px-4">Voltar</a>
                                <button type="submit" class="btn btn-primary rounded-3 px-4">
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>
