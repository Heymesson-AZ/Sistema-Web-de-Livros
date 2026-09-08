<x-layouts.principal>

    <div class="container py-3 py-md-5">

        <div class="row justify-content-center">

            <div class="col-md-8 col-lg-7">

                <div class="card shadow-sm border-0 rounded-4">

                    <div class="card-body p-3 p-md-4">

                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <img src="{{ $user->foto }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm"
                                style="width: 56px; height: 56px; object-fit: cover; border: 2px solid #3b82f6;">
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Meu Perfil de Cliente</h4>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mt-1">
                                    <i class="bi bi-person-check-fill me-1"></i> Cliente Leitor
                                </span>
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

                        <form method="POST" action="{{ route('cliente.perfil.atualizar') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- FOTO DE PERFIL -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Foto de Perfil</label>
                                <div class="avatar-upload-box">
                                    <img src="{{ $user->foto }}" id="avatarPreviewCliente" alt="Foto atual"
                                        class="avatar-preview-img">
                                    <div class="avatar-upload-meta">
                                        <input type="file" name="foto_perfil" id="foto_perfil_cliente"
                                            class="form-control form-control-sm @error('foto_perfil') is-invalid @enderror"
                                            accept="image/png, image/jpeg, image/jpg, image/webp"
                                            onchange="window.previewImage(this, 'avatarPreviewCliente')">
                                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                                            Formatos aceitos: JPG, PNG, WEBP até 2MB.
                                        </small>
                                        @if ($user->foto_perfil)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remover_foto"
                                                    value="1" id="removerFotoCliente">
                                                <label class="form-check-label small text-danger"
                                                    for="removerFotoCliente">
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
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" minlength="3"
                                    maxlength="100" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-secondary">E-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="telefone" class="form-label small fw-bold text-secondary">Celular / WhatsApp
                                    de Contato</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone" name="telefone" data-mask="telefone" maxlength="15"
                                    value="{{ old('telefone', $user->cliente?->celular_contato) }}" required>
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- DADOS CADASTRADOS PROTEGIDOS -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-bold text-secondary">CPF Registrado</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->cliente?->cpf ?? 'Não informado' }}" readonly disabled>
                                    <small class="text-muted" style="font-size: 11px;">O CPF é vinculado à conta e
                                        intransferível.</small>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-bold text-secondary">Data de Nascimento</label>
                                    <input type="text" class="form-control bg-light text-muted"
                                        value="{{ $user->cliente?->data_nascimento ? \Carbon\Carbon::parse($user->cliente->data_nascimento)->format('d/m/Y') : 'Não informada' }}"
                                        readonly disabled>
                                </div>
                            </div>

                            <div class="d-grid d-sm-flex justify-content-sm-end">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Salvar alterações
                                </button>
                            </div>

                        </form>

                        <hr class="my-4">

                        <div>
                            <h5 class="text-danger">
                                Excluir conta
                            </h5>

                            <p class="text-muted">
                                Esta ação excluirá sua conta permanentemente.
                            </p>

                            <form method="POST" action="{{ route('cliente.perfil.deletar') }}">
                                @csrf
                                @method('DELETE')

                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Confirme sua senha
                                    </label>

                                    <input type="password"
                                        class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                        id="password" name="password" required>

                                    @error('password', 'userDeletion')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-grid d-sm-block">
                                    <button type="submit" class="btn btn-outline-danger">
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
