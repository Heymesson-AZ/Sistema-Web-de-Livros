<!-- resources/views/components/modal-registro.blade.php -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content login-container border-0 shadow-lg">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Lado Esquerdo (Identidade) - Mantemos o padrão -->
                    <div
                        class="col-md-4 login-sidebar d-none d-md-flex text-white p-5 flex-column justify-content-center">
                        <h2 class="fw-bold">Junte-se a nós</h2>
                        <p class="text-white-50 small">Crie sua conta e comece a explorar novos mundos literários.</p>
                        <div class="mt-4">
                            <i data-lucide="user-plus" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                        </div>
                    </div>

                    <!-- Lado Direito (Formulário) -->
                    <div class="col-md-8 p-3 p-sm-4 p-md-5 position-relative"
                        style="max-height: 85vh; overflow-y: auto;">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>

                        <h3 class="fw-bold mb-1">Criar Conta</h3>
                        <p class="text-muted mb-4 small">Preencha os dados abaixo para se cadastrar</p>

                        <!-- Banner de Erro Geral de Cadastro -->
                        @if (
                            $errors->any() &&
                                (old('formulario') === 'registro' ||
                                    $errors->hasAny(['name', 'cpf', 'data_nascimento', 'telefone', 'password_confirmation'])))
                            <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 small border-0 shadow-sm"
                                style="border-radius: 10px; background-color: #fee2e2; color: #b91c1c;">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-6 flex-shrink-0"></i>
                                <div>Não foi possível concluir o cadastro. Verifique os campos destacados abaixo.</div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('cadastrar') }}">
                            @csrf
                            <input type="hidden" name="formulario" value="registro">

                            <div class="row">
                                <!-- Nome -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nome Completo</label>
                                    <input type="text" name="name" minlength="3" maxlength="100"
                                        class="form-control @error('name') is-invalid border-danger @enderror"
                                        value="{{ old('name') }}" placeholder="Seu nome completo (3 a 100 caracteres)"
                                        required autofocus>
                                    <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                                        @error('name')
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">E-mail</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid border-danger @enderror"
                                        value="{{ old('email') }}" placeholder="seuemail@exemplo.com" required>
                                    <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                                        @error('email')
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <!-- CPF e Data Nascimento (Lado a Lado) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">CPF</label>
                                    <input type="text" name="cpf" id="cpf" data-mask="cpf" maxlength="14"
                                        class="form-control @error('cpf') is-invalid border-danger @enderror"
                                        value="{{ old('cpf') }}" placeholder="000.000.000-00" required>
                                    @error('cpf')
                                        <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nascimento</label>
                                    <input type="date" name="data_nascimento" id="data_nascimento"
                                        max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                        class="form-control @error('data_nascimento') is-invalid border-danger @enderror"
                                        value="{{ old('data_nascimento') }}" required>
                                    @error('data_nascimento')
                                        <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Telefone / Celular</label>
                                    <input type="text" name="telefone" id="telefone" data-mask="telefone"
                                        maxlength="15"
                                        class="form-control @error('telefone') is-invalid border-danger @enderror"
                                        value="{{ old('telefone') }}" placeholder="(00) 00000-0000" required>
                                    @error('telefone')
                                        <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Senhas -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Senha</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="register_password" minlength="8"
                                            class="form-control @error('password') is-invalid border-danger @enderror"
                                            placeholder="Mínimo 8 caracteres" required autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" data-toggle="password"
                                            data-target="#register_password" title="Mostrar/Ocultar Senha"
                                            aria-label="Mostrar/Ocultar Senha">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                                        @error('password')
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Confirmar Senha</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation"
                                            id="register_password_confirmation" minlength="8"
                                            class="form-control @error('password_confirmation') is-invalid border-danger @enderror"
                                            placeholder="Repita a senha" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            data-toggle="password" data-target="#register_password_confirmation"
                                            title="Mostrar/Ocultar Senha" aria-label="Mostrar/Ocultar Senha">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                                        @error('password_confirmation')
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-login shadow-sm">Finalizar
                                    Cadastro</button>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none"
                                    data-bs-toggle="modal" data-bs-target="#loginModal">
                                    Já possui conta? Faça login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
