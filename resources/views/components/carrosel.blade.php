@props(['livros' => collect()])

<div class="livros-carousel-container shadow-lg">
    <!-- Cabeçalho do Carrossel com Título e Botões -->
    <div class="carousel-top-bar">
        <div class="carousel-title-group">
            <h2>
                <i class="bi bi-stars text-warning"></i>
                <span>Livros em Destaque</span>
            </h2>
            <p>Obras selecionadas, novidades e os títulos mais procurados pelos leitores.</p>
        </div>

        <div class="carousel-nav-buttons">
            <button type="button" class="carousel-nav-btn carousel-btn-prev" aria-label="Voltar slide">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="carousel-nav-btn carousel-btn-next" aria-label="Avançar slide">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Viewport e Track de Slides -->
    <div class="carousel-viewport">
        <div class="carousel-track">
            @if ($livros && count($livros) > 0)
                @foreach ($livros as $livro)
                    <div class="carousel-slide">
                        <a href="{{ route('livros.show', $livro) }}" class="carousel-book-card">
                            <div class="carousel-book-cover-wrapper">
                                <img src="{{ $livro->url_capa }}" alt="{{ $livro->titulo }}"
                                    class="carousel-book-cover-img" loading="lazy">
                                <span class="carousel-book-badge">{{ $livro->genero?->nome ?? 'Livro' }}</span>
                            </div>
                            <div class="carousel-book-info">
                                <h3 class="carousel-book-title" title="{{ $livro->titulo }}">{{ $livro->titulo }}</h3>
                                <span class="carousel-book-author">por
                                    {{ $livro->autor?->nome ?? 'Autor Independente' }}</span>
                                <div class="carousel-book-meta">
                                    <span class="carousel-book-price">{{ $livro->preco_formatado }}</span>
                                    <span class="carousel-book-cta">
                                        Ver Livro <i class="bi bi-arrow-right ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                {{-- Fallback elegante caso ainda não haja livros cadastrados --}}
                @php
                    $exemplos = [
                        [
                            'titulo' => 'Dom Casmurro',
                            'autor' => 'Machado de Assis',
                            'genero' => 'Clássicos',
                            'preco' => 'R$ 34,90',
                            'capa' =>
                                'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80',
                        ],
                        [
                            'titulo' => 'O Pequeno Príncipe',
                            'autor' => 'Antoine de Saint-Exupéry',
                            'genero' => 'Infantil',
                            'preco' => 'R$ 29,90',
                            'capa' =>
                                'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=450&q=80',
                        ],
                        [
                            'titulo' => 'A Metamorfose',
                            'autor' => 'Franz Kafka',
                            'genero' => 'Ficção',
                            'preco' => 'R$ 27,50',
                            'capa' =>
                                'https://images.unsplash.com/photo-1495640388908-05fa85288e61?auto=format&fit=crop&w=450&q=80',
                        ],
                        [
                            'titulo' => '1984',
                            'autor' => 'George Orwell',
                            'genero' => 'Distopia',
                            'preco' => 'R$ 39,90',
                            'capa' =>
                                'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?auto=format&fit=crop&w=450&q=80',
                        ],
                    ];
                @endphp
                @foreach ($exemplos as $ex)
                    <div class="carousel-slide">
                        <div class="carousel-book-card">
                            <div class="carousel-book-cover-wrapper">
                                <img src="{{ $ex['capa'] }}" alt="{{ $ex['titulo'] }}" class="carousel-book-cover-img"
                                    loading="lazy">
                                <span class="carousel-book-badge">{{ $ex['genero'] }}</span>
                            </div>
                            <div class="carousel-book-info">
                                <h3 class="carousel-book-title">{{ $ex['titulo'] }}</h3>
                                <span class="carousel-book-author">por {{ $ex['autor'] }}</span>
                                <div class="carousel-book-meta">
                                    <span class="carousel-book-price">{{ $ex['preco'] }}</span>
                                    <span class="carousel-book-cta">
                                        Destaque <i class="bi bi-star-fill ms-1 text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Indicadores de Slide (Dots) -->
    <div class="carousel-dots"></div>
</div>
