<x-layouts.autenticacao>
    <x-slot:title>Nova Senha - Universo de Papel</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 login-container">
                <div class="text-center mb-4">
                    <i data-lucide="key-round" class="text-primary mb-2" style="width: 36px; height: 36px;"></i>
                    <h4 class="fw-bold">Redefinir Senha</h4>
                    <p class="text-muted small">Crie uma nova senha segura para acessar sua conta.</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">E-mail</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid border-danger @enderror"
                            value="{{ old('email', $request->email) }}" required readonly>
                        @error('email')
                            <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nova Senha</label>
                        <input type="password" name="password" minlength="8"
                            class="form-control @error('password') is-invalid border-danger @enderror"
                            placeholder="Mínimo 8 caracteres" required autofocus>
                        @error('password')
                            <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation" minlength="8"
                            class="form-control @error('password_confirmation') is-invalid border-danger @enderror"
                            placeholder="Repita a nova senha" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block fw-semibold mt-1 text-danger small">
                                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">Redefinir Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.autenticacao>
