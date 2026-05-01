<!-- resources/views/auth/verify-email.blade.php -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>

<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-5" style="border-radius: 20px;">
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

                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-login">Reenviar E-mail</button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="btn btn-link text-decoration-none text-muted small">Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
