@php
    $layout = Auth::check() ? 'layouts.principal' : 'layouts.visitante';
@endphp

<x-dynamic-component :component="$layout">
    <div class="container py-4 py-md-5">

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <!-- CABEÇALHO DA PÁGINA -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3 shadow-sm"
                        style="width: 72px; height: 72px;">
                        <i class="bi bi-shop" style="font-size: 32px;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Venda na Universo de Papel</h2>
                    <p class="text-muted mx-auto" style="max-width: 540px;">
                        Abra sua livraria ou sebo online e alcance leitores em todo o país. Preencha os dados abaixo e envie sua solicitação para avaliação.
                    </p>
                </div>

                <!-- CARD DE CADASTRO -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                Solicitação de Abertura de Loja
                            </h5>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-monospace" style="font-size: 11px;">
                                <i class="bi bi-clock-history me-1"></i> Sujeito à Análise
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                    <strong>Atenção! Verifique os erros no formulário:</strong>
                                </div>
                                <ul class="mb-0 ps-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('vendedor.solicitar.salvar') }}">
                            @csrf

                            <!-- CASO CLIENTE AUTENTICADO -->
                            @auth
                                <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 p-3 mb-4 d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-weight: bold;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                    <div class="small">
                                        <div class="fw-bold text-dark">Conectado como {{ Auth::user()->name }}</div>
                                        <div class="text-muted">{{ Auth::user()->email }} — Sua conta atual será vinculada a este perfil de vendedor comercial.</div>
                                    </div>
                                </div>
                            @else
                                <!-- DADOS DE ACESSO (VISITANTE) -->
                                <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: 12px; letter-spacing: 0.5px;">
                                    1. Dados de Acesso do Responsável
                                </h6>

                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6">
                                        <label for="name" class="form-label small fw-bold text-secondary">Nome Completo do Responsável *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" placeholder="Ex: Maria Silva" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label small fw-bold text-secondary">E-mail de Acesso *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}" placeholder="seu.email@exemplo.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="password" class="form-label small fw-bold text-secondary">Senha de Acesso *</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="password_confirmation" class="form-label small fw-bold text-secondary">Confirmar Senha *</label>
                                        <input type="password" class="form-control"
                                            id="password_confirmation" name="password_confirmation" placeholder="Repita sua senha" required>
                                    </div>
                                </div>
                            @endauth

                            <!-- DADOS COMERCIAIS DA LOJA -->
                            <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: 12px; letter-spacing: 0.5px;">
                                {{ Auth::check() ? 'Dados Comerciais da Loja' : '2. Dados Comerciais da Loja' }}
                            </h6>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label for="nome_fantasia" class="form-label small fw-bold text-secondary">Nome Fantasia da Loja *</label>
                                    <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror"
                                        id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia') }}"
                                        placeholder="Ex: Livraria Dom Quixote" required>
                                    @error('nome_fantasia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="razao_social" class="form-label small fw-bold text-secondary">Razão Social *</label>
                                    <input type="text" class="form-control @error('razao_social') is-invalid @enderror"
                                        id="razao_social" name="razao_social" value="{{ old('razao_social') }}"
                                        placeholder="Ex: Dom Quixote Livros LTDA" required>
                                    @error('razao_social')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="cnpj" class="form-label small fw-bold text-secondary">CNPJ *</label>
                                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror"
                                        id="cnpj" name="cnpj" value="{{ old('cnpj') }}"
                                        placeholder="00.000.000/0000-00" maxlength="20" required>
                                    @error('cnpj')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="telefone_comercial" class="form-label small fw-bold text-secondary">Telefone / WhatsApp Comercial *</label>
                                    <input type="text" class="form-control @error('telefone_comercial') is-invalid @enderror"
                                        id="telefone_comercial" name="telefone_comercial" value="{{ old('telefone_comercial') }}"
                                        placeholder="(00) 00000-0000" maxlength="20" required>
                                    @error('telefone_comercial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="inscricao_estadual" class="form-label small fw-bold text-secondary">Inscrição Estadual (Opcional)</label>
                                    <input type="text" class="form-control @error('inscricao_estadual') is-invalid @enderror"
                                        id="inscricao_estadual" name="inscricao_estadual" value="{{ old('inscricao_estadual') }}"
                                        placeholder="Informe caso possua ou deixe em branco">
                                    @error('inscricao_estadual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- INFORMAÇÃO SOBRE AVALIAÇÃO -->
                            <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-shield-check text-success fs-4 mt-1"></i>
                                    <div class="small text-muted">
                                        <strong class="text-dark d-block mb-1">Como funciona o processo de aprovação?</strong>
                                        Após enviar sua solicitação, nossa equipe administrativa revisará as informações comerciais em até <strong>24 a 48 horas úteis</strong>. Enquanto sua conta estiver com o status <em>Pendente</em>, você poderá acessar o painel para acompanhar o status e atualizar seus dados.
                                    </div>
                                </div>
                            </div>

                            <!-- BOTÕES DE SUBMISSÃO -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 border-top">
                                <a href="{{ url('/') }}" class="btn btn-light rounded-3 px-4 text-muted w-100 w-sm-auto">
                                    <i class="bi bi-arrow-left me-1"></i> Voltar ao Início
                                </a>
                                <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold w-100 w-sm-auto shadow-sm">
                                    <i class="bi bi-send me-1"></i> Enviar Solicitação para Aprovação
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-dynamic-component>
