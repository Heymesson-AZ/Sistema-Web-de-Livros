<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                            class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clientes.index') }}"
                            class="text-decoration-none text-muted">Clientes</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Cadastrar</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-person-plus text-primary me-2"></i>
                        Cadastrar Novo Cliente
                    </h1>
                    <p class="text-muted small mb-0">Crie uma nova conta de cliente comprador no sistema.</p>
                </div>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- FORMULÁRIO DE CADASTRO -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <form method="POST" action="{{ route('admin.clientes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- FOTO DE PERFIL -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Foto de Perfil (Opcional)</label>
                            <div class="avatar-upload-box">
                                <img src="https://ui-avatars.com/api/?name=Cliente&color=7F9CF5&background=EBF4FF"
                                    id="avatarPreviewNewCliente" alt="Foto" class="avatar-preview-img">
                                <div class="avatar-upload-meta">
                                    <input type="file" name="foto_perfil" id="foto_perfil"
                                        class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        data-preview-target="avatarPreviewNewCliente">
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                        Formatos aceitos: JPG, PNG, WEBP até 2MB.
                                    </small>
                                    @error('foto_perfil')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 1: CONTA DE ACESSO -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lock text-primary"></i>
                            Dados da Conta de Acesso
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Nome Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Nome do cliente" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">E-mail <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="cliente@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Senha de Acesso <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Confirmar Senha <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Repita a senha" required>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- SEÇÃO 2: DADOS PESSOAIS -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-vcard text-primary"></i>
                            Dados Pessoais do Cliente
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">CPF <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="cpf"
                                    class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}"
                                    placeholder="000.000.000-00" required>
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Celular de Contato</label>
                                <input type="text" name="celular_contato"
                                    class="form-control @error('celular_contato') is-invalid @enderror"
                                    value="{{ old('celular_contato') }}" placeholder="(00) 00000-0000">
                                @error('celular_contato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Data de Nascimento <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="data_nascimento"
                                    class="form-control @error('data_nascimento') is-invalid @enderror"
                                    value="{{ old('data_nascimento') }}" required>
                                @error('data_nascimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Status da Conta <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror"
                                    required>
                                    <option value="ativo" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>
                                        Ativo</option>
                                    <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>
                                        Inativo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.clientes.index') }}" class="btn btn-light rounded-3 px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="bi bi-check-lg me-1"></i> Salvar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
