<!-- resources/views/components/modal-registro.blade.php -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content login-container border-0 shadow-lg">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Lado Esquerdo (Identidade) - Mantemos o padrão -->
                    <div class="col-md-4 login-sidebar d-none d-md-flex text-white p-5 flex-column justify-content-center">
                        <h2 class="fw-bold">Junte-se a nós</h2>
                        <p class="text-white-50 small">Crie sua conta e comece a explorar novos mundos literários.</p>
                        <div class="mt-4">
                            <i data-lucide="user-plus" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                        </div>
                    </div>

                    <!-- Lado Direito (Formulário) -->
                    <div class="col-md-8 p-5 position-relative" style="max-height: 85vh; overflow-y: auto;">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

                        <h3 class="fw-bold mb-1">Criar Conta</h3>
                        <p class="text-muted mb-4 small">Preencha os dados abaixo para se cadastrar</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <!-- Nome -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nome Completo</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                                    @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">E-mail</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <!-- CPF e Data Nascimento (Lado a Lado) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">CPF</label>
                                    <input type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" placeholder="000.000.000-00" required>
                                    @error('cpf') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nascimento</label>
                                    <input type="date" name="data_nascimento" class="form-control @error('data_nascimento') is-invalid @enderror" value="{{ old('data_nascimento') }}" required>
                                    @error('data_nascimento') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Telefone</label>
                                    <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" required>
                                    @error('telefone') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <!-- Senhas -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Senha</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                                    @error('password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Confirmar Senha</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-login shadow-sm">Finalizar Cadastro</button>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal">
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
