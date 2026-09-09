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
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-primary me-2"></i>
                        Editar Vendedor: {{ $vendedor->nome_fantasia }}
                    </h1>
                    <p class="text-muted small mb-0">Atualize os dados cadastrais, informações de contato e status da
                        loja.</p>
                </div>
                <a href="{{ route('admin.vendedores.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- FORMULÁRIO DE EDIÇÃO -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <form method="POST" action="{{ route('admin.vendedores.update', $vendedor) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- FOTO / LOGOTIPO DA LOJA -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Logotipo / Foto da Loja</label>
                            <div class="avatar-upload-box">
                                <img src="{{ $vendedor->user?->foto }}" id="avatarPreviewVendedorEdit"
                                    alt="{{ $vendedor->nome_fantasia }}" class="avatar-preview-img">
                                <div class="avatar-upload-meta">
                                    <input type="file" name="foto_perfil" id="foto_perfil"
                                        class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        data-preview-target="avatarPreviewVendedorEdit">
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                        JPG, PNG ou WEBP até 2MB. Selecione um novo arquivo para substituir o logotipo
                                        atual.
                                    </small>
                                    @if ($vendedor->user?->foto_perfil)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remover_foto_perfil"
                                                value="1" id="remover_foto_perfil">
                                            <label class="form-check-label small text-danger" for="remover_foto_perfil">
                                                <i class="bi bi-trash me-1"></i> Remover logotipo atual
                                            </label>
                                        </div>
                                    @endif
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
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $vendedor->user?->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">E-mail Profissional <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $vendedor->user?->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Nova Senha <span
                                        class="text-muted fw-normal">(opcional)</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Deixe em branco para manter a atual">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Repita a nova senha se for alterar">
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
                                    value="{{ old('nome_fantasia', $vendedor->nome_fantasia) }}" required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Razão Social <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="razao_social"
                                    class="form-control @error('razao_social') is-invalid @enderror"
                                    value="{{ old('razao_social', $vendedor->razao_social) }}" required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">CNPJ <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="cnpj"
                                    class="form-control @error('cnpj') is-invalid @enderror"
                                    value="{{ old('cnpj', $vendedor->cnpj) }}" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Inscrição Estadual <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="inscricao_estadual"
                                    class="form-control @error('inscricao_estadual') is-invalid @enderror"
                                    value="{{ old('inscricao_estadual', $vendedor->inscricao_estadual) }}" required>
                                @error('inscricao_estadual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Telefone Comercial</label>
                                <input type="text" name="telefone_comercial"
                                    class="form-control @error('telefone_comercial') is-invalid @enderror"
                                    value="{{ old('telefone_comercial', $vendedor->telefone_comercial) }}">
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
                                    <option value="ativo"
                                        {{ old('status', $vendedor->user?->status) === 'ativo' ? 'selected' : '' }}>
                                        Ativo</option>
                                    <option value="inativo"
                                        {{ old('status', $vendedor->user?->status) === 'inativo' ? 'selected' : '' }}>
                                        Inativo</option>
                                    <option value="banido"
                                        {{ old('status', $vendedor->user?->status) === 'banido' ? 'selected' : '' }}>
                                        Banido</option>
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
                                        {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'aprovado' ? 'selected' : '' }}>
                                        Aprovado (Permite publicar e vender)</option>
                                    <option value="pendente"
                                        {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'pendente' ? 'selected' : '' }}>
                                        Pendente (Aguardando análise)</option>
                                    <option value="rejeitado"
                                        {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'rejeitado' ? 'selected' : '' }}>
                                        Rejeitado (Bloqueado)</option>
                                </select>
                                @error('status_aprovacao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- CONFIRMAÇÃO DE SEGURANÇA (SENHA DO ADMINISTRADOR) -->
                        <div class="p-3 mb-4 rounded-3 border border-warning bg-warning bg-opacity-10">
                            <div class="d-flex align-items-center gap-2 mb-2 text-warning-emphasis">
                                <i class="bi bi-shield-lock-fill fs-5"></i>
                                <h6 class="fw-bold mb-0">Confirmação de Segurança do Administrador</h6>
                            </div>
                            <p class="text-muted small mb-2">
                                Para autorizar esta alteração cadastral ou de status deste vendedor, digite a <strong>sua senha de administrador</strong>:
                            </p>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="password" name="senha_confirmacao_admin" id="senha_confirmacao_admin_vend"
                                            class="form-control @error('senha_confirmacao_admin') is-invalid @enderror"
                                            placeholder="Digite sua senha de administrador" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            data-toggle="password" data-target="#senha_confirmacao_admin_vend" title="Mostrar/Ocultar Senha">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('senha_confirmacao_admin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.vendedores.index') }}" class="btn btn-light rounded-3 px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="bi bi-check-lg me-1"></i> Atualizar Vendedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
