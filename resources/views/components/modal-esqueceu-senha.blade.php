<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold">Recuperar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <p class="text-muted small mb-4">
                    Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para você escolher uma
                    nova.
                </p>

                <!-- Status de Sucesso (Link enviado) -->
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 small border-0 shadow-sm"
                        style="border-radius: 10px; background-color: #dcfce7; color: #15803d;">
                        <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="formulario" value="recuperar_senha">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">E-mail de Cadastro</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid border-danger @enderror"
                            value="{{ old('email') }}" placeholder="seuemail@exemplo.com" required autofocus>
                        <div class="invalid-feedback fw-semibold mt-1 text-danger small">
                            @error('email')
                                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            Enviar Link de Recuperação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
