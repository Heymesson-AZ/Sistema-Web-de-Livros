<x-layouts.principal>

    <div class="container py-3 py-md-5">

        <div class="row justify-content-center">

            <div class="col-md-8 col-lg-7">

                <div class="card shadow-sm border-0 rounded-4">

                    <div class="card-body p-3 p-md-4">

                        <h2 class="mb-4">
                            <i class="bi bi-shop me-2"></i>
                            Meu Perfil de Vendedor
                        </h2>

                        @if (session('status') === 'perfil-atualizado')
                            <div class="alert alert-success">
                                Perfil atualizado com sucesso!
                            </div>
                        @endif

                        <form method="POST" action="{{ route('vendedor.perfil.atualizar') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nome do Responsável *
                                </label>

                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" minlength="3"
                                    maxlength="100" required>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    E-mail *
                                </label>

                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nome_fantasia" class="form-label">
                                    Nome Fantasia da Loja *
                                </label>

                                <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror"
                                    id="nome_fantasia" name="nome_fantasia"
                                    value="{{ old('nome_fantasia', $user->vendedor?->nome_fantasia) }}" required>

                                @error('nome_fantasia')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="razao_social" class="form-label">
                                    Razão Social *
                                </label>

                                <input type="text" class="form-control @error('razao_social') is-invalid @enderror"
                                    id="razao_social" name="razao_social"
                                    value="{{ old('razao_social', $user->vendedor?->razao_social) }}" required>

                                @error('razao_social')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label for="telefone" class="form-label">
                                        Telefone / WhatsApp Comercial *
                                    </label>

                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                        id="telefone" name="telefone"
                                        value="{{ old('telefone', $user->vendedor?->telefone_comercial) }}" required>

                                    @error('telefone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label for="inscricao_estadual" class="form-label">
                                        Inscrição Estadual
                                    </label>

                                    <input type="text" class="form-control @error('inscricao_estadual') is-invalid @enderror"
                                        id="inscricao_estadual" name="inscricao_estadual"
                                        value="{{ old('inscricao_estadual', $user->vendedor?->inscricao_estadual) }}">

                                    @error('inscricao_estadual')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">
                                    CNPJ (Registrado)
                                </label>
                                <input type="text" class="form-control bg-light text-muted"
                                    value="{{ $user->vendedor?->cnpj ?? 'Não informado' }}" readonly disabled>
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
                                Excluir conta de vendedor
                            </h5>

                            <p class="text-muted small">
                                Esta ação excluirá sua conta e desativará os produtos vinculados à loja permanentemente.
                            </p>

                            <form method="POST" action="{{ route('vendedor.perfil.deletar') }}">
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
