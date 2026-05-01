<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content login-container border-0 shadow-lg">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Lado Esquerdo (Identidade) -->
                    <div
                        class="col-md-5 login-sidebar d-none d-md-flex text-white p-5 flex-column justify-content-center">
                        <h2 class="fw-bold">Universo de Papel</h2>
                        <p class="text-white-50">Sua próxima grande história começa com um simples login.</p>
                        <div class="mt-4">
                            <i data-lucide="book-open" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                        </div>
                    </div>

                    <!-- Lado Direito (Formulário) -->
                    <div class="col-md-7 p-5 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>

                        <h3 class="fw-bold mb-1">Entrar</h3>
                        <p class="text-muted mb-4 small">Acesse sua conta para continuar</p>

                        <!-- Status da Sessão (Ex: Senha redefinida) -->
                        @if (session('status'))
                            <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- E-mail -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">E-mail</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="Digite seu e-mail" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Senha</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Digite sua senha" required>
                                @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_modal">
                                    <label class="form-check-label small text-muted" for="remember_modal">Lembrar de
                                        mim</label>
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
