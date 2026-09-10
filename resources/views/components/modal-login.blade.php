<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content login-container border-0 shadow-lg">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Lado Esquerdo (Identidade) -->
                    <div
                        class="col-md-5 login-sidebar d-none d-md-flex text-white p-5 flex-column justify-content-center">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Universo de Papel" class="img-fluid mb-3"
                            style="max-height: 80px; width: auto; object-fit: contain;">
                        <p class="text-white-50 mt-2">Sua próxima grande história começa com um simples login.</p>
                    </div>

                    <!-- Lado Direito (Formulário) -->
                    <div class="col-md-7 p-4 p-md-5 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Fechar modal"></button>

                        <h3 class="fw-bold mb-1" id="loginModalLabel">Entrar</h3>
                        <p class="text-muted mb-4 small">Acesse sua conta para continuar</p>

                        @if (session('status') && session('form_sucesso') !== 'recuperar_senha')
                            <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 small border-0 shadow-sm"
                                style="border-radius: 10px;">
                                <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                                <div>{{ session('status') }}</div>
                            </div>
                        @endif

                        <!-- Banner de Erro Geral de Login -->
                        @if ($errors->any() && old('formulario') === 'login')
                            <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 small border-0 shadow-sm"
                                style="border-radius: 10px;">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                                <div>E-mail ou senha incorretos. Verifique os dados informados.</div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('entrar') }}">
                            @csrf
                            <input type="hidden" name="formulario" value="login">

                            <!-- E-mail -->
                            <div class="mb-3">
                                <label for="login_email" class="form-label small fw-bold">E-mail</label>
                                <input type="email" name="email" id="login_email"
                                    class="form-control @error('email') is-invalid border-danger @enderror"
                                    value="{{ old('email') }}" placeholder="Digite seu e-mail" required autofocus>
                                <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                                    @error('email')
                                        <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                    @enderror
                                </div>
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label for="login_password" class="form-label small fw-bold">Senha</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="login_password"
                                        class="form-control @error('password') is-invalid border-danger @enderror"
                                        placeholder="Digite sua senha" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword"
                                        data-toggle="password" data-target="#login_password" title="Mostrar ou Ocultar Senha"
                                        aria-label="Mostrar ou Ocultar Senha">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                        <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_modal"
                                        value="1" checked>
                                    <label class="form-check-label small text-muted" for="remember_modal">
                                        Manter conectado por 15 dias
                                    </label>
                                </div>
                                <!-- Ajustado para abrir a Modal de Esqueci Senha -->
                                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
                                    class="small text-decoration-none">Esqueceu a senha?</a>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-login shadow-sm">Acessar
                                    Conta</button>

                                <hr class="my-3 text-muted">

                                <!-- Novo botão: Criar Conta -->
                                <div class="text-center">
                                    <p class="small text-muted mb-2">Novo por aqui?</p>
                                    <button type="button" class="btn btn-outline-primary w-100 btn-login"
                                        data-bs-toggle="modal" data-bs-target="#registerModal">
                                        Criar minha conta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
