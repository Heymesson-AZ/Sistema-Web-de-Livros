<x-layouts.principal>
    <div class="container py-4">

        <!-- BREADCRUMB & VOLTAR -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Painel</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendedores.index') }}" class="text-decoration-none text-muted">Vendedores</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-primary me-2"></i>
                        Editar Vendedor: {{ $vendedor->nome_fantasia }}
                    </h1>
                    <p class="text-muted small mb-0">Atualize os dados cadastrais, informações de contato e status da loja.</p>
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
                    <form method="POST" action="{{ route('admin.vendedores.update', $vendedor) }}">
                        @csrf
                        @method('PUT')

                        <!-- SEÇÃO 1: CONTA DE ACESSO -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lock text-primary"></i>
                            Dados da Conta de Acesso
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Nome do Responsável <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $vendedor->user?->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">E-mail Profissional <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $vendedor->user?->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Nova Senha <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Deixe em branco para manter a atual">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a nova senha se for alterar">
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
                                <label class="form-label small fw-bold text-secondary">Nome Fantasia da Loja <span class="text-danger">*</span></label>
                                <input type="text" name="nome_fantasia" class="form-control @error('nome_fantasia') is-invalid @enderror" value="{{ old('nome_fantasia', $vendedor->nome_fantasia) }}" required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Razão Social <span class="text-danger">*</span></label>
                                <input type="text" name="razao_social" class="form-control @error('razao_social') is-invalid @enderror" value="{{ old('razao_social', $vendedor->razao_social) }}" required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">CNPJ <span class="text-danger">*</span></label>
                                <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj', $vendedor->cnpj) }}" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Inscrição Estadual <span class="text-danger">*</span></label>
                                <input type="text" name="inscricao_estadual" class="form-control @error('inscricao_estadual') is-invalid @enderror" value="{{ old('inscricao_estadual', $vendedor->inscricao_estadual) }}" required>
                                @error('inscricao_estadual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Telefone Comercial</label>
                                <input type="text" name="telefone_comercial" class="form-control @error('telefone_comercial') is-invalid @enderror" value="{{ old('telefone_comercial', $vendedor->telefone_comercial) }}">
                                @error('telefone_comercial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- SEÇÃO 3: STATUS DE APROVAÇÃO -->
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary"></i>
                            Status de Aprovação
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">Status Atual <span class="text-danger">*</span></label>
                                <select name="status_aprovacao" class="form-select @error('status_aprovacao') is-invalid @enderror" required>
                                    <option value="aprovado" {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'aprovado' ? 'selected' : '' }}>Aprovado (Permite publicar e vender)</option>
                                    <option value="pendente" {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'pendente' ? 'selected' : '' }}>Pendente (Aguardando análise)</option>
                                    <option value="rejeitado" {{ old('status_aprovacao', $vendedor->status_aprovacao) === 'rejeitado' ? 'selected' : '' }}>Rejeitado (Bloqueado)</option>
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
                                <i class="bi bi-check-lg me-1"></i> Atualizar Vendedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
