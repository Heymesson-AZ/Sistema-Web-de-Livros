@php
    $livroExistente = isset($livro);
@endphp

<!-- PRÉVIA E UPLOAD DA CAPA -->
<div class="row mb-4 align-items-center">
    <div class="col-12 col-sm-auto text-center mb-3 mb-sm-0">
        <div class="bg-light p-2 rounded-3 border d-inline-flex align-items-center justify-content-center"
            style="width: 120px; height: 160px;">
            <img src="{{ $livroExistente && $livro->capa ? asset('storage/' . $livro->capa) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80' }}"
                id="capaPreview" alt="Prévia da Capa" class="rounded shadow-sm"
                style="max-width: 100px; max-height: 145px; object-fit: cover;">
        </div>
    </div>
    <div class="col">
        <label class="form-label fw-bold text-secondary small">Capa da Obra</label>
        <input type="file" name="capa" id="capaInput"
            class="form-control @error('capa') is-invalid @enderror"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            data-preview-target="capaPreview">
        <small class="text-muted d-block mt-1">Formatos aceitos: JPG, PNG, WEBP até 2MB.</small>
        @error('capa')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">

<!-- DADOS BÁSICOS -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-8">
        <label class="form-label fw-bold text-secondary small">Título da Obra <span class="text-danger">*</span></label>
        <input type="text" name="titulo"
            class="form-control @error('titulo') is-invalid @enderror"
            value="{{ old('titulo', $livro->titulo ?? '') }}" placeholder="Ex: Dom Casmurro" required>
        @error('titulo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">ISBN <span class="text-danger">*</span></label>
        <input type="text" name="isbn"
            class="form-control @error('isbn') is-invalid @enderror"
            value="{{ old('isbn', $livro->isbn ?? '') }}" placeholder="Ex: 978-85-359-0277-5" required>
        @error('isbn')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Data de Publicação <span class="text-danger">*</span></label>
        <input type="date" name="data_publicacao"
            class="form-control @error('data_publicacao') is-invalid @enderror"
            value="{{ old('data_publicacao', isset($livro) && $livro->data_publicacao ? $livro->data_publicacao->format('Y-m-d') : '') }}" required>
        @error('data_publicacao')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Preço de Venda (R$) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted">R$</span>
            <input type="text" name="preco"
                class="form-control @error('preco') is-invalid @enderror"
                value="{{ old('preco', isset($livro) ? number_format($livro->preco, 2, ',', '.') : '') }}"
                placeholder="0,00" required>
        </div>
        @error('preco')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Estoque Disponível <span class="text-danger">*</span></label>
        <input type="number" name="quantidade"
            class="form-control @error('quantidade') is-invalid @enderror"
            value="{{ old('quantidade', $livro->quantidade ?? 0) }}" min="0" required>
        @error('quantidade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- RELACIONAMENTOS -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Autor <span class="text-danger">*</span></label>
        <select name="autor_id" class="form-select @error('autor_id') is-invalid @enderror" required>
            <option value="">Selecione o Autor...</option>
            @foreach ($autores as $autor)
                <option value="{{ $autor->id }}"
                    {{ old('autor_id', $livro->autor_id ?? '') == $autor->id ? 'selected' : '' }}>
                    {{ $autor->nome }}
                </option>
            @endforeach
        </select>
        @error('autor_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Gênero Literário <span class="text-danger">*</span></label>
        <select name="genero_id" class="form-select @error('genero_id') is-invalid @enderror" required>
            <option value="">Selecione o Gênero...</option>
            @foreach ($generos as $gen)
                <option value="{{ $gen->id }}"
                    {{ old('genero_id', $livro->genero_id ?? '') == $gen->id ? 'selected' : '' }}>
                    {{ $gen->nome }}
                </option>
            @endforeach
        </select>
        @error('genero_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label fw-bold text-secondary small">Editora <span class="text-danger">*</span></label>
        <select name="editora_id" class="form-select @error('editora_id') is-invalid @enderror" required>
            <option value="">Selecione a Editora...</option>
            @foreach ($editoras as $edit)
                <option value="{{ $edit->id }}"
                    {{ old('editora_id', $livro->editora_id ?? '') == $edit->id ? 'selected' : '' }}>
                    {{ $edit->nome }}
                </option>
            @endforeach
        </select>
        @error('editora_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- VENDEDOR / LOJA RESPONSÁVEL -->
<div class="row g-3 mb-4">
    @if ($ehAdmin)
        <div class="col-12 col-md-6">
            <label class="form-label fw-bold text-secondary small">Vendedor Responsável <span class="text-danger">*</span></label>
            <select name="vendedor_id" class="form-select @error('vendedor_id') is-invalid @enderror" required>
                <option value="">Selecione o Vendedor...</option>
                @foreach ($vendedores as $v)
                    <option value="{{ $v->id }}"
                        {{ old('vendedor_id', $livro->vendedor_id ?? '') == $v->id ? 'selected' : '' }}>
                        {{ $v->nome_fantasia }} ({{ $v->user->name ?? 'Sem usuário' }})
                    </option>
                @endforeach
            </select>
            @error('vendedor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @else
        <div class="col-12 col-md-6">
            <label class="form-label fw-bold text-secondary small">Loja Vendedora</label>
            <div class="form-control bg-light text-muted d-flex align-items-center gap-2">
                <i class="bi bi-shop text-success"></i>
                <span class="fw-semibold text-dark">{{ $vendedor->nome_fantasia }}</span>
                <span class="badge bg-success-subtle text-success small ms-auto">Minha Loja</span>
            </div>
        </div>
    @endif
</div>

<!-- SINOPSE -->
<div class="mb-4">
    <label class="form-label fw-bold text-secondary small">Sinopse da Obra</label>
    <textarea name="sinopse" rows="5" class="form-control @error('sinopse') is-invalid @enderror"
        placeholder="Escreva uma breve descrição ou resumo sobre a história...">{{ old('sinopse', $livro->sinopse ?? '') }}</textarea>
    @error('sinopse')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

