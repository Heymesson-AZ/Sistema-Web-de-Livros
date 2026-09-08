<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                            class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendedores.index') }}"
                            class="text-decoration-none text-muted">Vendedores</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Cadastrar</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-shop text-primary me-2"></i>
                        Cadastrar Novo Vendedor
                    </h1>
                    <p class="text-muted small mb-0">Cadastre uma nova livraria ou vendedor parceiro no sistema.</p>
                </div>
                <a href="{{ route('admin.vendedores.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- FORMULÁRIO DE CADASTRO -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <form method="POST" action="{{ route('admin.vendedores.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- FOTO / LOGOTIPO DA LOJA -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Logotipo / Foto da Loja
                                (Opcional)</label>
                            <div class="avatar-upload-box">
                                <img src="https://ui-avatars.com/api/?name=Loja&color=7F9CF5&background=EBF4FF"
                                    id="avatarPreviewNewVendedor" alt="Logo" class="avatar-preview-img">
                                <div class="avatar-upload-meta">
                                    <input type="file" name="foto_perfil" id="foto_perfil"
                                        class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="window.previewImage(this, 'avatarPreviewNewVendedor')">
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
                                <label class="form-label small fw-bold text-secondary">Nome do Responsável <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Nome do representante legal" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">E-mail Profissional <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="contato@empresa.com" required>
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

                        <!-- SEÇÃO 2: DADOS CORPORATIVOS -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i>
                            Dados Corporativos da Loja
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Nome Fantasia da Loja <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nome_fantasia"
                                    class="form-control @error('nome_fantasia') is-invalid @enderror"
                                    value="{{ old('nome_fantasia') }}" placeholder="Ex: Livraria Estrela Guia"
                                    required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Razão Social <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="razao_social"
                                    class="form-control @error('razao_social') is-invalid @enderror"
                                    value="{{ old('razao_social') }}" placeholder="Ex: Estrela Guia Livros Ltda"
                                    required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">CNPJ <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="cnpj"
                                    class="form-control @error('cnpj') is-invalid @enderror"
                                    value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Inscrição Estadual <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="inscricao_estadual"
                                    class="form-control @error('inscricao_estadual') is-invalid @enderror"
                                    value="{{ old('inscricao_estadual') }}"
                                    placeholder="Ex: 123.456.789.000 ou ISENTO" required>
                                @error('inscricao_estadual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Telefone Comercial</label>
                                <input type="text" name="telefone_comercial"
                                    class="form-control @error('telefone_comercial') is-invalid @enderror"
                                    value="{{ old('telefone_comercial') }}" placeholder="(00) 00000-0000">
                                @error('telefone_comercial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- SEÇÃO 3: STATUS DE ACESSO E APROVAÇÃO -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary"></i>
                            Status da Conta & Aprovação
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
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

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Status de Aprovação da Loja
                                    <span class="text-danger">*</span></label>
                                <select name="status_aprovacao"
                                    class="form-select @error('status_aprovacao') is-invalid @enderror" required>
                                    <option value="aprovado"
                                        {{ old('status_aprovacao', 'aprovado') === 'aprovado' ? 'selected' : '' }}>
                                        Aprovado (Permite publicar e vender)</option>
                                    <option value="pendente"
                                        {{ old('status_aprovacao') === 'pendente' ? 'selected' : '' }}>Pendente
                                        (Aguardando análise)</option>
                                    <option value="rejeitado"
                                        {{ old('status_aprovacao') === 'rejeitado' ? 'selected' : '' }}>Rejeitado
                                        (Bloqueado)</option>
                                </select>
                                @error('status_aprovacao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.vendedores.index') }}" class="btn btn-light rounded-3 px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="bi bi-check-lg me-1"></i> Salvar Vendedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
