<x-layouts.autenticacao>
    <x-slot:title>Verifique seu E-mail - Universo de Papel</x-slot:title>

    <div class="row justify-content-center text-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-5 login-container">
                <div class="mb-3">
                    <i data-lucide="mail-check" class="text-primary" style="width: 48px; height: 48px;"></i>
                </div>

                <h3 class="fw-bold mb-3">Verifique seu E-mail</h3>
                <p class="text-muted">
                    Obrigado por se cadastrar na <strong>Universo de Papel</strong>! Antes de começar, clique no
                    link que enviamos para o seu e-mail. Não recebeu? Podemos enviar outro.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success small my-3">
                        Um novo link de verificação foi enviado para o seu e-mail.
                    </div>
                @endif

                @if (app()->isLocal() && Auth::check() && !Auth::user()->hasVerifiedEmail())
                    <div class="alert alert-info small my-3 text-start border-0 shadow-sm"
                        style="border-radius: 12px; background-color: #eff6ff; color: #1e40af;">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Modo de Desenvolvimento Local:
                        </div>
                        <div class="mb-2">Como o sistema está em ambiente local, os e-mails são gravados no log
                            (<code>storage/logs/laravel.log</code>).</div>
                        <div>
                            <a href="{{ URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => Auth::id(), 'hash' => sha1(Auth::user()->getEmailForVerification())]) }}"
                                class="btn btn-sm btn-primary fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Confirmar este e-mail agora
                            </a>
                        </div>
                    </div>
                @endif

                <div class="mt-4 d-flex justify-content-center gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-login">Reenviar E-mail</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none text-muted small">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.autenticacao>
