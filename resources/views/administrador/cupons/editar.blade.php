<x-layouts.principal title="Editar Cupom - Universo de Papel">
    <div class="container py-4">

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('admin.cupons.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h1 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i data-lucide="edit-3" class="text-primary"></i>
                        <span>Editar Cupom {{ $cupom->codigo }}</span>
                    </h1>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.cupons.update', $cupom) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Código do Cupom:</label>
                            <input type="text" name="codigo" class="form-control text-uppercase font-monospace"
                                value="{{ old('codigo', $cupom->codigo) }}" required maxlength="30">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Tipo de Desconto:</label>
                                <select name="tipo_desconto" class="form-select" required>
                                    <option value="percentual" {{ old('tipo_desconto', $cupom->tipo_desconto) === 'percentual' ? 'selected' : '' }}>Percentual (%)</option>
                                    <option value="valor_fixo" {{ old('tipo_desconto', $cupom->tipo_desconto) === 'valor_fixo' ? 'selected' : '' }}>Valor Fixo (R$)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Valor do Desconto:</label>
                                <input type="number" step="0.01" min="0.01" name="valor_desconto" class="form-control"
                                    value="{{ old('valor_desconto', $cupom->valor_desconto) }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Limite de Usos:</label>
                                <input type="number" min="1" name="limite_uso" class="form-control"
                                    value="{{ old('limite_uso', $cupom->limite_uso) }}">
                                <div class="form-text" style="font-size: 11px;">Usos atuais: <strong>{{ $cupom->usos_atuais }}</strong></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Data de Validade:</label>
                                <input type="date" name="validade_cupom" class="form-control"
                                    value="{{ old('validade_cupom', $cupom->validade_cupom?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.cupons.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                                Atualizar Cupom
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>

