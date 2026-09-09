<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.administradores.index') }}"
                        class="text-decoration-none text-muted">Administradores</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Novo Administrador</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-person-plus text-primary me-2"></i>
                    Cadastrar Administrador
                </h1>
                <p class="text-muted small mb-0">
                    Crie um novo usuário com privilégios administrativos no sistema da Universo de Papel.
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

                        <form method="POST" action="{{ route('admin.administradores.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- FOTO DE PERFIL -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Foto de Perfil (Opcional)</label>
                                <div class="avatar-upload-box">
                                    <img src="https://ui-avatars.com/api/?name=Admin&color=7F9CF5&background=EBF4FF"
                                        id="avatarPreviewNewAdmin" alt="Foto" class="avatar-preview-img">
                                    <div class="avatar-upload-meta">
                                        <input type="file" name="foto_perfil" id="foto_perfil"
                                            class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                            accept="image/png, image/jpeg, image/jpg, image/webp"
                                            data-preview-target="avatarPreviewNewAdmin">
                                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                            Formatos aceitos: JPG, PNG, WEBP até 2MB.
                                        </small>
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
                                        value="{{ old('name') }}" placeholder="Ex: Maria Silva" required
                                        minlength="3" maxlength="100">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label small fw-bold text-secondary">E-mail
                                        Corporativo *</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="admin@universodepapel.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label small fw-bold text-secondary">Senha de
                                        Acesso *</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Mínimo de 8 caracteres" required minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label small fw-bold text-secondary">Confirmar Senha *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" placeholder="Repita a senha" required minlength="8">
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
                                        <option value="">Selecione um cargo...</option>
                                        @foreach ($cargos as $cargo)
                                            <option value="{{ $cargo }}"
                                                {{ old('cargo') === $cargo ? 'selected' : '' }}>
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
                                        <option value="">Selecione um departamento...</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep }}"
                                                {{ old('departamento') === $dep ? 'selected' : '' }}>
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
                                        value="{{ old('telefone_urgencia') }}" placeholder="(00) 00000-0000">
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
                                            {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                        <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>
                                            Inativo</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <span>Salvar Administrador</span>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
