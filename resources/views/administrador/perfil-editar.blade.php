<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Meu Perfil de
                    Administrador</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">

                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <img src="{{ $user->foto }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm"
                                style="width: 56px; height: 56px; object-fit: cover; border: 2px solid #3b82f6;">
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Meu Perfil de Administrador</h4>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        {{ $admin->cargo ?? 'Administrador' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border rounded-pill">
                                        {{ $admin->departamento ?? 'Geral' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if (session('status') === 'perfil-atualizado')
                            <div
                                class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>Perfil atualizado com sucesso!</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.perfil.atualizar') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- FOTO DE PERFIL -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Foto de Perfil</label>
                                <div class="avatar-upload-box">
                                    <img src="{{ $user->foto }}" id="avatarPreviewAdmin" alt="Foto atual"
                                        class="avatar-preview-img">
                                    <div class="avatar-upload-meta">
                                        <input type="file" name="foto_perfil" id="foto_perfil_input"
                                            class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                            accept="image/png, image/jpeg, image/jpg, image/webp"
                                            onchange="window.previewImage(this, 'avatarPreviewAdmin')">
                                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                            Formatos aceitos: JPG, PNG, WEBP até 2MB.
                                        </small>
                                        @if ($user->foto_perfil)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remover_foto"
                                                    value="1" id="removerFotoAdmin">
                                                <label class="form-check-label small text-danger"
                                                    for="removerFotoAdmin">
                                                    <i class="bi bi-trash me-1"></i> Remover foto atual (usar avatar
                                                    padrão)
                                                </label>
                                            </div>
                                        @endif
                                        @error('foto_perfil')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold text-secondary">Nome
                                    Completo</label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" minlength="3" maxlength="100" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-secondary">E-mail</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="telefone_urgencia" class="form-label small fw-bold text-secondary">Telefone
                                    de Urgência / Plantão</label>
                                <input type="text" id="telefone_urgencia" name="telefone_urgencia"
                                    class="form-control @error('telefone_urgencia') is-invalid @enderror"
                                    value="{{ old('telefone_urgencia', $admin->telefone_urgencia ?? '') }}"
                                    placeholder="(00) 00000-0000">
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

                        <hr class="my-4">

                        <div>
                            <h5 class="text-danger fw-bold">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Excluir Conta de Administrador
                            </h5>

                            <p class="text-muted small">
                                Esta ação excluirá permanentemente o seu acesso administrativo. O sistema requer que
                                exista ao menos outro administrador ativo cadastrado.
                            </p>

                            <form method="POST" action="{{ route('admin.perfil.deletar') }}">
                                @csrf
                                @method('DELETE')

                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-bold text-secondary">
                                        Confirme sua senha para continuar
                                    </label>

                                    <input type="password"
                                        class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                        id="password" name="password" required>

                                    @error('password', 'userDeletion')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    @error('DeleteUsuario')
                                        <div class="text-danger small mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-grid d-sm-flex justify-content-sm-end">
                                    <button type="submit" class="btn btn-outline-danger px-4 py-2"
                                        onclick="return confirm('Tem certeza de que deseja excluir sua conta de administrador? Esta ação é irreversível.')">
                                        <i class="bi bi-trash me-1"></i>
                                        Excluir minha conta
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>
