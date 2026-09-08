<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; background-color: #eff6ff; color: #2563eb;">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Recuperar Senha</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                @if (session('status'))
                    <!-- Card Interativo de Sucesso -->
                    <div class="p-3 mb-3 border-0 shadow-sm"
                        style="border-radius: 14px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #22c55e !important;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fs-4">📬</span>
                            <h6 class="fw-bold text-success mb-0">Tudo certo! Verifique seu e-mail</h6>
                        </div>
                        <p class="small text-secondary mb-2">
                            Enviamos as instruções de redefinição com um link seguro para:
                            <br>
                            <strong class="text-dark fs-6">{{ session('email_enviado') ?? old('email') }}</strong>
                        </p>
                        <hr class="my-2 border-success opacity-25">
                        <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-1">
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-clock-history text-success"></i>
                                <span>O link expira em <strong>60 minutos</strong>.</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-inbox text-success"></i>
                                <span>Não encontrou? Verifique a pasta de <strong>Spam / Lixo
                                        Eletrônico</strong>.</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>Se não solicitou, ignore. Sua conta permanece 100% segura.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-3">
                        <button type="button" class="btn btn-primary btn-login py-2" data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Ir para o Login
                        </button>
                        <button type="button" class="btn btn-outline-secondary py-2 small" data-bs-dismiss="modal">
                            Fechar
                        </button>
                    </div>
                @else
                    <p class="text-muted small mb-4">
                        Esqueceu sua senha? Sem problemas! Informe seu e-mail cadastrado e enviaremos um link rápido e
                        seguro para você criar uma nova senha.
                    </p>

                    <form method="POST" action="{{ route('senha.email') }}">
                        @csrf
                        <input type="hidden" name="formulario" value="recuperar_senha">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">E-mail de Cadastro</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email"
                                    class="form-control border-start-0 ps-0 @error('email') is-invalid border-danger @enderror"
                                    value="{{ old('email') }}" placeholder="seuemail@exemplo.com" required autofocus>
                            </div>
                            @error('email')
                                <div class="invalid-feedback fw-semibold mt-1 text-danger small d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-login py-2">
                                <i class="bi bi-send me-1"></i> Enviar Link de Recuperação
                            </button>
                            <button type="button" class="btn btn-link text-decoration-none text-muted small"
                                data-bs-toggle="modal" data-bs-target="#loginModal">
                                ← Lembra da senha? Voltar ao Login
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
