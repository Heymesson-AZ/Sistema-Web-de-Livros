<x-layouts.app>

    <div class="container py-3 py-md-5">

        <div class="row justify-content-center">

            <div class="col-md-8 col-lg-7">

                <div class="card shadow-sm border-0 rounded-4">

                    <div class="card-body p-3 p-md-4">

                        <h2 class="mb-4">
                            <i class="bi bi-person-circle me-2"></i>
                            Meu Perfil
                        </h2>

                        @if (session('status') === 'perfil-atualizado')
                            <div class="alert alert-success">
                                Perfil atualizado com sucesso!
                            </div>
                        @endif

                        <form method="POST" action="{{ route('cliente.perfil.atualizar') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nome
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
                                    E-mail
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
                                <label for="telefone" class="form-label">
                                    Telefone
                                </label>

                                <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone" name="telefone" data-mask="telefone" maxlength="15"
                                    value="{{ old('telefone', $user->cliente?->celular_contato) }}" required>

                                @error('telefone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
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

</x-layouts.app>
