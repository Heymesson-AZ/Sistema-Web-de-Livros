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
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-pencil-square text-primary me-2"></i>
                        Editar Cliente: {{ $cliente->user?->name }}
                    </h1>
                    <p class="text-muted small mb-0">Atualize os dados pessoais e informações de contato do cliente.</p>
                </div>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- FORMULÁRIO DE EDIÇÃO -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- FOTO DE PERFIL -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Foto de Perfil</label>
                            <div class="avatar-upload-box">
                                <img src="{{ $cliente->user?->foto }}" id="avatarPreviewClienteEdit"
                                    alt="{{ $cliente->user?->name }}" class="avatar-preview-img">
                                <div class="avatar-upload-meta">
                                    <input type="file" name="foto_perfil" id="foto_perfil"
                                        class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="window.previewImage(this, 'avatarPreviewClienteEdit')">
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                        JPG, PNG ou WEBP até 2MB. Selecione um novo arquivo para substituir a foto
                                        atual.
                                    </small>
                                    @if ($cliente->user?->foto_perfil)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remover_foto_perfil"
                                                value="1" id="remover_foto_perfil">
                                            <label class="form-check-label small text-danger" for="remover_foto_perfil">
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
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $cliente->user?->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary">E-mail <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $cliente->user?->email) }}" required>
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
                                    class="form-control @error('cpf') is-invalid @enderror"
                                    value="{{ old('cpf', $cliente->cpf) }}" required>
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Celular de Contato</label>
                                <input type="text" name="celular_contato"
                                    class="form-control @error('celular_contato') is-invalid @enderror"
                                    value="{{ old('celular_contato', $cliente->celular_contato) }}">
                                @error('celular_contato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Data de Nascimento <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="data_nascimento"
                                    class="form-control @error('data_nascimento') is-invalid @enderror"
                                    value="{{ old('data_nascimento', $cliente->data_nascimento ? \Carbon\Carbon::parse($cliente->data_nascimento)->format('Y-m-d') : '') }}"
                                    required>
                                @error('data_nascimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-secondary">Status da Conta <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror"
                                    required>
                                    <option value="ativo"
                                        {{ old('status', $cliente->user?->status) === 'ativo' ? 'selected' : '' }}>
                                        Ativo</option>
                                    <option value="inativo"
                                        {{ old('status', $cliente->user?->status) === 'inativo' ? 'selected' : '' }}>
                                        Inativo</option>
                                    <option value="banido"
                                        {{ old('status', $cliente->user?->status) === 'banido' ? 'selected' : '' }}>
                                        Banido</option>
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
                                <i class="bi bi-check-lg me-1"></i> Atualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
