<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.administradores.index') }}"
                        class="text-decoration-none text-muted">Administradores</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar:
                    {{ $admin->user->name }}</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Editar Administrador
                </h1>
                <p class="text-muted small mb-0">
                    Atualize os dados cadastrais, cargo e permissões do administrador
                    <strong>{{ $admin->user->name }}</strong>.
                </p>
            </div>
            <a href="{{ route('admin.administradores.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <form method="POST" action="{{ route('admin.administradores.update', $admin) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- FOTO DE PERFIL -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Foto de Perfil</label>
                                <div class="avatar-upload-box">
                                    <img src="{{ $admin->user->foto }}" id="avatarPreviewAdminEdit"
                                        alt="{{ $admin->user->name }}" class="avatar-preview-img">
                                    <div class="avatar-upload-meta">
                                        <input type="file" name="foto_perfil" id="foto_perfil"
                                            class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                            accept="image/png, image/jpeg, image/jpg, image/webp"
                                            data-preview-target="avatarPreviewAdminEdit">
                                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                            JPG, PNG ou WEBP até 2MB. Selecione um novo arquivo para substituir a foto
                                            atual.
                                        </small>
                                        @if ($admin->user->foto_perfil)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="remover_foto_perfil" value="1" id="remover_foto_perfil">
                                                <label class="form-check-label small text-danger"
                                                    for="remover_foto_perfil">
                                                    <i class="bi bi-trash me-1"></i> Remover foto atual
                                                </label>
                                            </div>
                                        @endif
                                        @error('foto_perfil')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 1: DADOS DE ACESSO -->
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bi bi-person-lock text-primary fs-5"></i>
                                <h5 class="fw-bold text-dark mb-0">Dados de Acesso</h5>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label small fw-bold text-secondary">Nome Completo
                                        *</label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $admin->user->name) }}" required minlength="3"
                                        maxlength="100">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label small fw-bold text-secondary">E-mail
                                        Corporativo *</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $admin->user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label small fw-bold text-secondary">Nova Senha
                                        (Opcional)</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Deixe em branco para não alterar">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" style="font-size: 11px;">Preencha somente se desejar
                                        redefinir a senha do usuário.</small>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label small fw-bold text-secondary">Confirmar Nova Senha</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" placeholder="Confirme a nova senha">
                                </div>
                            </div>

                            <!-- SEÇÃO 2: DADOS CORPORATIVOS -->
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bi bi-briefcase text-primary fs-5"></i>
                                <h5 class="fw-bold text-dark mb-0">Dados Corporativos & Função</h5>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-3">
                                    <label for="cargo" class="form-label small fw-bold text-secondary">Cargo
                                        *</label>
                                    <select id="cargo" name="cargo"
                                        class="form-select @error('cargo') is-invalid @enderror" required>
                                        @foreach ($cargos as $cargo)
                                            <option value="{{ $cargo }}"
                                                {{ old('cargo', $admin->cargo) === $cargo ? 'selected' : '' }}>
                                                {{ $cargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cargo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="departamento"
                                        class="form-label small fw-bold text-secondary">Departamento *</label>
                                    <select id="departamento" name="departamento"
                                        class="form-select @error('departamento') is-invalid @enderror" required>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep }}"
                                                {{ old('departamento', $admin->departamento) === $dep ? 'selected' : '' }}>
                                                {{ $dep }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="telefone_urgencia"
                                        class="form-label small fw-bold text-secondary">Telefone de Urgência</label>
                                    <input type="text" id="telefone_urgencia" name="telefone_urgencia"
                                        class="form-control @error('telefone_urgencia') is-invalid @enderror"
                                        value="{{ old('telefone_urgencia', $admin->telefone_urgencia) }}"
                                        placeholder="(00) 00000-0000">
                                    @error('telefone_urgencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="status" class="form-label small fw-bold text-secondary">Status da
                                        Conta *</label>
                                    <select id="status" name="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="ativo"
                                            {{ old('status', $admin->user->status) === 'ativo' ? 'selected' : '' }}>
                                            Ativo</option>
                                        <option value="inativo"
                                            {{ old('status', $admin->user->status) === 'inativo' ? 'selected' : '' }}>
                                            Inativo</option>
                                        <option value="banido"
                                            {{ old('status', $admin->user->status) === 'banido' ? 'selected' : '' }}>
                                            Banido</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            <!-- CONFIRMAÇÃO DE SEGURANÇA (SENHA DO ADMINISTRADOR) -->
                            <div class="p-3 mb-4 rounded-3 border border-warning bg-warning bg-opacity-10">
                                <div class="d-flex align-items-center gap-2 mb-2 text-warning-emphasis">
                                    <i class="bi bi-shield-lock-fill fs-5"></i>
                                    <h6 class="fw-bold mb-0">Confirmação de Segurança do Administrador</h6>
                                </div>
                                <p class="text-muted small mb-2">
                                    Para autorizar esta alteração crítica nas informações deste administrador, digite a <strong>sua senha de administrador</strong>:
                                </p>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="input-group input-group-sm">
                                            <input type="password" name="senha_confirmacao_admin" id="senha_confirmacao_admin"
                                                class="form-control @error('senha_confirmacao_admin') is-invalid @enderror"
                                                placeholder="Digite sua senha de administrador" required>
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle="password" data-target="#senha_confirmacao_admin" title="Mostrar/Ocultar Senha">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('senha_confirmacao_admin')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- BOTÕES DE AÇÃO -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('admin.administradores.index') }}"
                                    class="btn btn-light rounded-3 px-4">
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="btn btn-primary rounded-3 px-4 d-flex align-items-center gap-2">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>Salvar Alterações</span>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
