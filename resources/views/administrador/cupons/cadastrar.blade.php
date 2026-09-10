<x-layouts.principal title="Novo Cupom - Universo de Papel">
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
                        <i data-lucide="ticket" class="text-primary"></i>
                        <span>Cadastrar Novo Cupom</span>
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

                    <form action="{{ route('admin.cupons.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Código do Cupom:</label>
                            <input type="text" name="codigo" class="form-control text-uppercase font-monospace"
                                value="{{ old('codigo') }}" placeholder="Ex: PROMO20" required maxlength="30">
                            <div class="form-text" style="font-size: 11px;">Será salvo em letras maiúsculas
                                automaticamente.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Tipo de Desconto:</label>
                                <select name="tipo_desconto" class="form-select" required>
                                    <option value="percentual"
                                        {{ old('tipo_desconto') === 'percentual' ? 'selected' : '' }}>Percentual (%)
                                    </option>
                                    <option value="valor_fixo"
                                        {{ old('tipo_desconto') === 'valor_fixo' ? 'selected' : '' }}>Valor Fixo (R$)
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Valor do Desconto:</label>
                                <input type="number" step="0.01" min="0.01" name="valor_desconto"
                                    class="form-control" value="{{ old('valor_desconto') }}" placeholder="Ex: 10.00"
                                    required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Limite de Usos (Opcional):</label>
                                <input type="number" min="1" name="limite_uso" class="form-control"
                                    value="{{ old('limite_uso') }}" placeholder="Ex: 100">
                                <div class="form-text" style="font-size: 11px;">Deixe vazio para uso ilimitado.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Data de Validade (Opcional):</label>
                                <input type="date" name="validade_cupom" class="form-control"
                                    value="{{ old('validade_cupom') }}">
                                <div class="form-text" style="font-size: 11px;">Deixe vazio para não expirar.</div>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4 p-3 bg-light rounded-3">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="requer_concordancia"
                                value="1" id="switchConcordancia"
                                {{ old('requer_concordancia') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="switchConcordancia">
                                <i class="bi bi-hand-thumbs-up me-1 text-primary"></i> Campanha Promocional de Incentivo
                                (Sujeita a concordância dos lojistas)
                            </label>
                            <div class="text-muted small ms-4">
                                Quando ativado, os vendedores parceiros poderão aderir ou recusar a promoção em seus
                                painéis. O desconto incidirá apenas sobre itens dos vendedores participantes.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.cupons.index') }}"
                                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                                Salvar Cupom
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-layouts.principal>
