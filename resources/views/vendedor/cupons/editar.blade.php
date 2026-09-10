<x-layouts.principal>
    <div class="container py-4 py-md-5">

        <!-- NAVEGAÇÃO -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Painel do Vendedor</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.cupons.index') }}"
                        class="text-decoration-none text-muted">Cupons da Loja</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar Cupom</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                style="width: 46px; height: 46px; font-size: 20px;">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Editar Cupom: {{ $cupom->codigo }}</h4>
                                <p class="text-muted small mb-0">Atualize as regras deste cupom exclusivo da sua loja.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Corrija os erros abaixo:</div>
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('vendedor.cupons.update', $cupom) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- CÓDIGO DO CUPOM -->
                            <div class="mb-3">
                                <label for="codigo" class="form-label small fw-semibold">Código do Cupom:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted font-monospace"><i
                                            class="bi bi-tag"></i></span>
                                    <input type="text" name="codigo" id="codigo"
                                        class="form-control text-uppercase font-monospace @error('codigo') is-invalid @enderror"
                                        value="{{ old('codigo', $cupom->codigo) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <!-- TIPO DE DESCONTO -->
                                <div class="col-12 col-md-6">
                                    <label for="tipo_desconto" class="form-label small fw-semibold">Tipo de
                                        Desconto:</label>
                                    <select name="tipo_desconto" id="tipo_desconto" class="form-select" required>
                                        <option value="percentual"
                                            {{ old('tipo_desconto', $cupom->tipo_desconto) === 'percentual' ? 'selected' : '' }}>
                                            Porcentagem (%)</option>
                                        <option value="valor_fixo"
                                            {{ old('tipo_desconto', $cupom->tipo_desconto) === 'valor_fixo' ? 'selected' : '' }}>
                                            Valor Fixo em Reais (R$)</option>
                                    </select>
                                </div>

                                <!-- VALOR DO DESCONTO -->
                                <div class="col-12 col-md-6">
                                    <label for="valor_desconto" class="form-label small fw-semibold">Valor do
                                        Desconto:</label>
                                    <input type="number" step="0.01" min="0.01" name="valor_desconto"
                                        id="valor_desconto"
                                        class="form-control @error('valor_desconto') is-invalid @enderror"
                                        value="{{ old('valor_desconto', $cupom->valor_desconto) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <!-- LIMITE DE USOS -->
                                <div class="col-12 col-md-6">
                                    <label for="limite_uso" class="form-label small fw-semibold">Limite de Usos:</label>
                                    <input type="number" min="1" name="limite_uso" id="limite_uso"
                                        class="form-control" value="{{ old('limite_uso', $cupom->limite_uso) }}">
                                    <div class="form-text" style="font-size: 11px;">Usos atuais:
                                        <strong>{{ $cupom->usos_atuais }}</strong></div>
                                </div>

                                <!-- DATA DE VALIDADE -->
                                <div class="col-12 col-md-6">
                                    <label for="validade_cupom" class="form-label small fw-semibold">Data de
                                        Validade:</label>
                                    <input type="date" name="validade_cupom" id="validade_cupom" class="form-control"
                                        value="{{ old('validade_cupom', $cupom->validade_cupom?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('vendedor.cupons.index') }}"
                                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
                                    Atualizar Cupom
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>
