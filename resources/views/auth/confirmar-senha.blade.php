<x-layouts.autenticacao>
    <x-slot:title>Área Segura - Confirmar Senha</x-slot:title>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 login-container">
                <div class="text-center mb-4">
                    <i data-lucide="shield-check" class="text-primary mb-2" style="width: 36px; height: 36px;"></i>
                    <h4 class="fw-bold">Área Segura</h4>
                    <p class="text-muted small">Confirme sua senha para continuar.</p>
                </div>

                <form method="POST" action="{{ route('senha.confirmar') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sua Senha</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback fw-bold d-flex align-items-center gap-1 mt-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">Confirmar Acesso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.autenticacao>
