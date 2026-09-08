<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.painel') }}"
                        class="text-decoration-none text-muted">Minha Loja</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendedor.livros.index') }}"
                        class="text-decoration-none text-muted">Meus Livros</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Publicar Livro</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-journal-plus text-success me-2"></i>
                    Publicar Novo Livro
                </h1>
                <p class="text-muted small mb-0">
                    Cadastre uma nova obra para venda pela sua loja <strong>{{ $vendedor->nome_fantasia }}</strong>.
                </p>
            </div>
            <a href="{{ route('vendedor.livros.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <form method="POST" action="{{ route('vendedor.livros.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- PRÉVIA E UPLOAD DA CAPA -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-12 col-sm-auto text-center mb-3 mb-sm-0">
                                    <div class="bg-light p-2 rounded-3 border d-inline-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 160px;">
                                        <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80"
                                            id="capaPreviewVendedor" alt="Prévia da Capa" class="rounded shadow-sm"
                                            style="max-width: 100px; max-height: 145px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold text-secondary small">Capa do Livro</label>
                                    <input type="file" name="capa" id="capaInput"
                                        class="form-control @error('capa') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg,image/webp"
                                        onchange="window.previewImage(this, 'capaPreviewVendedor')">
                                    <small class="text-muted d-block mt-1">Formatos aceitos: JPG, PNG, WEBP até
                                        2MB.</small>
                                    @error('capa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- DADOS BÁSICOS -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-bold text-secondary small">Título da Obra <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="titulo"
                                        class="form-control @error('titulo') is-invalid @enderror"
                                        value="{{ old('titulo') }}" placeholder="Ex: O Pequeno Príncipe" required>
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">ISBN <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="isbn"
                                        class="form-control @error('isbn') is-invalid @enderror"
                                        value="{{ old('isbn') }}" placeholder="Ex: 978-85-359-0277-5" required>
                                    @error('isbn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Data de Publicação <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="data_publicacao"
                                        class="form-control @error('data_publicacao') is-invalid @enderror"
                                        value="{{ old('data_publicacao') }}" required>
                                    @error('data_publicacao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Preço de Venda (R$) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0.01" name="preco"
                                            class="form-control @error('preco') is-invalid @enderror"
                                            value="{{ old('preco') }}" placeholder="0,00" required>
                                    </div>
                                    @error('preco')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Quantidade Disponível <span
                                            class="text-danger">*</span></label>
                                    <input type="number" min="0" name="quantidade"
                                        class="form-control @error('quantidade') is-invalid @enderror"
                                        value="{{ old('quantidade', 1) }}" required>
                                    @error('quantidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- RELACIONAMENTOS -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Autor <span
                                            class="text-danger">*</span></label>
                                    <select name="autor_id"
                                        class="form-select @error('autor_id') is-invalid @enderror" required>
                                        <option value="">Selecione o Autor</option>
                                        @foreach ($autores as $autor)
                                            <option value="{{ $autor->id }}"
                                                {{ old('autor_id') == $autor->id ? 'selected' : '' }}>
                                                {{ $autor->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('autor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Gênero Literário <span
                                            class="text-danger">*</span></label>
                                    <select name="genero_id"
                                        class="form-select @error('genero_id') is-invalid @enderror" required>
                                        <option value="">Selecione o Gênero</option>
                                        @foreach ($generos as $genero)
                                            <option value="{{ $genero->id }}"
                                                {{ old('genero_id') == $genero->id ? 'selected' : '' }}>
                                                {{ $genero->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genero_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Editora <span
                                            class="text-danger">*</span></label>
                                    <select name="editora_id"
                                        class="form-select @error('editora_id') is-invalid @enderror" required>
                                        <option value="">Selecione a Editora</option>
                                        @foreach ($editoras as $editora)
                                            <option value="{{ $editora->id }}"
                                                {{ old('editora_id') == $editora->id ? 'selected' : '' }}>
                                                {{ $editora->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editora_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- SINOPSE -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small">Sinopse / Descrição da
                                    Obra</label>
                                <textarea name="sinopse" rows="5" class="form-control @error('sinopse') is-invalid @enderror"
                                    placeholder="Apresente aos leitores os detalhes, resumo e diferenciais deste exemplar...">{{ old('sinopse') }}</textarea>
                                @error('sinopse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- BOTÕES DE AÇÃO -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('vendedor.livros.index') }}"
                                    class="btn btn-light rounded-pill px-4">Cancelar</a>
                                <button type="submit"
                                    class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i> Publicar Livro
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
