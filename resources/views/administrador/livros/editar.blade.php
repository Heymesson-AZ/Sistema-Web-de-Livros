<x-layouts.principal>
    <div class="container py-4">

        <!-- NAVEGAÇÃO / BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                        class="text-decoration-none text-muted">Painel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.livros.index') }}"
                        class="text-decoration-none text-muted">Livros</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Editar Livro</li>
            </ol>
        </nav>

        <!-- CABEÇALHO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Editar Livro
                </h1>
                <p class="text-muted small mb-0">
                    Modifique as informações da obra <strong>"{{ $livro->titulo }}"</strong>.
                </p>
            </div>
            <a href="{{ route('admin.livros.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <form method="POST" action="{{ route('admin.livros.update', $livro) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- PRÉVIA E UPLOAD DA CAPA -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-12 col-sm-auto text-center mb-3 mb-sm-0">
                                    <div class="bg-light p-2 rounded-3 border d-inline-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 160px;">
                                        <img src="{{ $livro->url_capa }}" id="capaPreviewEdit" alt="Prévia da Capa"
                                            class="rounded shadow-sm"
                                            style="max-width: 100px; max-height: 145px; object-fit: cover;"
                                            onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80'">
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold text-secondary small">Alterar Capa do Livro</label>
                                    <input type="file" name="capa" id="capaInputEdit"
                                        class="form-control @error('capa') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg,image/webp"
                                        onchange="window.previewImage(this, 'capaPreviewEdit')">
                                    <small class="text-muted d-block mt-1">Formatos aceitos: JPG, PNG, WEBP até
                                        2MB.</small>
                                    @error('capa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if ($livro->capa)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remover_capa"
                                                id="remover_capa" value="1">
                                            <label class="form-check-label small text-danger" for="remover_capa">
                                                Remover capa atual e restaurar padrão
                                            </label>
                                        </div>
                                    @endif
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
                                        value="{{ old('titulo', $livro->titulo) }}" required>
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">ISBN <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="isbn"
                                        class="form-control @error('isbn') is-invalid @enderror"
                                        value="{{ old('isbn', $livro->isbn) }}" required>
                                    @error('isbn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Data de Publicação <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="data_publicacao"
                                        class="form-control @error('data_publicacao') is-invalid @enderror"
                                        value="{{ old('data_publicacao', $livro->data_publicacao ? $livro->data_publicacao->format('Y-m-d') : '') }}"
                                        required>
                                    @error('data_publicacao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Preço (R$) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0.01" name="preco"
                                            class="form-control @error('preco') is-invalid @enderror"
                                            value="{{ old('preco', $livro->preco) }}" required>
                                    </div>
                                    @error('preco')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Quantidade em Estoque <span
                                            class="text-danger">*</span></label>
                                    <input type="number" min="0" name="quantidade"
                                        class="form-control @error('quantidade') is-invalid @enderror"
                                        value="{{ old('quantidade', $livro->quantidade) }}" required>
                                    @error('quantidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- RELACIONAMENTOS -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Autor <span
                                            class="text-danger">*</span></label>
                                    <select name="autor_id"
                                        class="form-select @error('autor_id') is-invalid @enderror" required>
                                        <option value="">Selecione o Autor</option>
                                        @foreach ($autores as $autor)
                                            <option value="{{ $autor->id }}"
                                                {{ old('autor_id', $livro->autor_id) == $autor->id ? 'selected' : '' }}>
                                                {{ $autor->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('autor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Gênero Literário <span
                                            class="text-danger">*</span></label>
                                    <select name="genero_id"
                                        class="form-select @error('genero_id') is-invalid @enderror" required>
                                        <option value="">Selecione o Gênero</option>
                                        @foreach ($generos as $genero)
                                            <option value="{{ $genero->id }}"
                                                {{ old('genero_id', $livro->genero_id) == $genero->id ? 'selected' : '' }}>
                                                {{ $genero->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genero_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Editora <span
                                            class="text-danger">*</span></label>
                                    <select name="editora_id"
                                        class="form-select @error('editora_id') is-invalid @enderror" required>
                                        <option value="">Selecione a Editora</option>
                                        @foreach ($editoras as $editora)
                                            <option value="{{ $editora->id }}"
                                                {{ old('editora_id', $livro->editora_id) == $editora->id ? 'selected' : '' }}>
                                                {{ $editora->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editora_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Vendedor Responsável <span
                                            class="text-danger">*</span></label>
                                    <select name="vendedor_id"
                                        class="form-select @error('vendedor_id') is-invalid @enderror" required>
                                        <option value="">Selecione o Vendedor / Loja</option>
                                        @foreach ($vendedores as $vendedor)
                                            <option value="{{ $vendedor->id }}"
                                                {{ old('vendedor_id', $livro->vendedor_id) == $vendedor->id ? 'selected' : '' }}>
                                                {{ $vendedor->nome_fantasia ?? $vendedor->razao_social }}
                                                ({{ $vendedor->user->email ?? 'Sem email' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendedor_id')
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
                                    placeholder="Resumo envolvente sobre o enredo ou conteúdo do livro...">{{ old('sinopse', $livro->sinopse) }}</textarea>
                                @error('sinopse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- BOTÕES DE AÇÃO -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('admin.livros.index') }}"
                                    class="btn btn-light rounded-pill px-4">Cancelar</a>
                                <button type="submit"
                                    class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.principal>
