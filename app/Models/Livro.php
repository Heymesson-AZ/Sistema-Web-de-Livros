<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Livro extends Model
{
    use HasFactory;
    use SoftDeletes;

    // =========================================================================
    // CONSTANTES DE MODERAÇÃO DE LIVROS
    // =========================================================================
    public const STATUS_MODERACAO_ATIVO = 'ativo';
    public const STATUS_MODERACAO_SOB_ANALISE = 'sob_analise';
    public const STATUS_MODERACAO_BLOQUEADO = 'bloqueado_temporariamente';
    public const STATUS_MODERACAO_BANIDO = 'banido';

    protected $fillable = [
        'titulo',
        'data_publicacao',
        'isbn',
        'sinopse',
        'capa',
        'preco',
        'quantidade',
        'status_moderacao',
        'motivo_moderacao',
        'moderado_em',
        'moderado_por',
        'autor_id',
        'genero_id',
        'editora_id',
        'vendedor_id',
    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_publicacao
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_publicacao' => 'date',
        'moderado_em' => 'datetime',
    ];

    /**
     * Padroniza o título do livro removendo espaços extras.
     */
    protected function titulo(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(preg_replace('/\s+/', ' ', (string) $value))
        );
    }

    /**
     * Higieniza o ISBN removendo hífens e espaços.
     */
    protected function isbn(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim(preg_replace('/[^0-9Xx]/', '', (string) $value)))
        );
    }

    /**
     * Converte preços formatados (ex: 'R$ 49,90' ou '49,90') para float padronizado.
     */
    protected function preco(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value)) {
                    $limpo = str_replace(['R$', ' '], '', $value);
                    if (str_contains($limpo, ',') && str_contains($limpo, '.')) {
                        $limpo = str_replace('.', '', $limpo);
                        $limpo = str_replace(',', '.', $limpo);
                    } elseif (str_contains($limpo, ',')) {
                        $limpo = str_replace(',', '.', $limpo);
                    }
                    return (float) $limpo;
                }
                return (float) $value;
            }
        );
    }


    // um livro pertence a um autor
    public function autor()
    {
        return $this->belongsTo(Autor::class); // Relacionamento de um livro para um autor
    }

    // um livro pertence a um gênero
    public function genero()
    {
        return $this->belongsTo(Genero::class); // Relacionamento de um livro para um gênero
    }

    // um livro pertence a uma editora
    public function editora()
    {
        return $this->belongsTo(Editora::class); // Relacionamento de um livro para uma editora
    }

    // um livro pertence a um vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class); // Relacionamento de um livro para um vendedor
    }

    // um livro pode ser muitos favoritado (uma relação muitos-para-muitos)
    public function favoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos');  // Relacionamento de muitos para muitos entre livros e usuários (favoritos)
    }

    /**
     * Verifica se o livro foi favoritado pelo usuário informado.
     */
    public function isFavoritadoPor(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favoritos()->where('users.id', $user->id)->exists();
    }


    // Um livro pode estar em vários carrinhos (relação muitos-para-muitos via carrinho_livro)
    public function carrinhos()
    {
        return $this->belongsToMany(Carrinho::class, 'carrinho_livro')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }

    // Alias para retrocompatibilidade
    public function itensCarrinho()
    {
        return $this->carrinhos();
    }

    // um livro pode estar em muitos itens do pedido (uma relação muitos-para-muitos)
    public function itensPedido()
    {
        return $this->hasMany(PedidoItem::class); // Relacionamento de um livro para muitos itens do pedido (pedido_itens)
    }

    /**
     * URL completa da capa com fallback seguro.
     */
    public function getUrlCapaAttribute(): string
    {
        if ($this->capa && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->capa)) {
            return asset('storage/' . $this->capa);
        }

        if ($this->capa && (str_starts_with($this->capa, 'http://') || str_starts_with($this->capa, 'https://'))) {
            return $this->capa;
        }

        // Capa ilustrativa padrão de alta qualidade para livros
        return 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=450&q=80';
    }

    /**
     * Preço formatado em Reais (BRL).
     */
    public function getPrecoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->preco, 2, ',', '.');
    }

    /**
     * Administrador responsável pela moderação da obra.
     */
    public function moderador()
    {
        return $this->belongsTo(User::class, 'moderado_por');
    }

    /**
     * Helpers para verificação do status de moderação
     */
    public function isAtivo(): bool
    {
        return ($this->status_moderacao ?? self::STATUS_MODERACAO_ATIVO) === self::STATUS_MODERACAO_ATIVO;
    }

    public function isSobAnalise(): bool
    {
        return $this->status_moderacao === self::STATUS_MODERACAO_SOB_ANALISE;
    }

    public function isBloqueado(): bool
    {
        return $this->status_moderacao === self::STATUS_MODERACAO_BLOQUEADO;
    }

    public function isBanido(): bool
    {
        return $this->status_moderacao === self::STATUS_MODERACAO_BANIDO;
    }

    public function getStatusModeracaoRotuloAttribute(): string
    {
        return match ($this->status_moderacao) {
            self::STATUS_MODERACAO_SOB_ANALISE => 'Sob Análise',
            self::STATUS_MODERACAO_BLOQUEADO   => 'Bloqueado Temporariamente',
            self::STATUS_MODERACAO_BANIDO      => 'Banido',
            default                            => 'Ativo',
        };
    }

    public function getStatusModeracaoBadgeClassAttribute(): string
    {
        return match ($this->status_moderacao) {
            self::STATUS_MODERACAO_SOB_ANALISE => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::STATUS_MODERACAO_BLOQUEADO   => 'bg-danger-subtle text-danger border border-danger-subtle',
            self::STATUS_MODERACAO_BANIDO      => 'bg-dark text-white border border-dark',
            default                            => 'bg-success-subtle text-success border border-success-subtle',
        };
    }

    /**
     * Verifica se o livro possui estoque disponível e está ativo no catálogo.
     */
    public function isDisponivel(): bool
    {
        return (int) $this->quantidade > 0 && $this->isAtivo();
    }

    /**
     * Scope para buscar apenas livros aprovados/ativos para exibição pública.
     */
    public function scopeAtivos($query)
    {
        return $query->where('status_moderacao', self::STATUS_MODERACAO_ATIVO);
    }

    /**
     * Scope para buscar livros com estoque e ativos no catálogo de vendedores aprovados e ativos.
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('quantidade', '>', 0)
                     ->where('status_moderacao', self::STATUS_MODERACAO_ATIVO)
                     ->whereHas('vendedor', function ($sub) {
                         $sub->where('status_aprovacao', 'aprovado')
                             ->whereHas('user', function ($u) {
                                 $u->where('status', 'ativo');
                             });
                     });
    }

    /**
     * Scope para filtros combinados estilo Amazon.
     */
    public function scopeFiltrar($query, array $filtros)
    {
        // 1. Busca textual (título, sinopse, ISBN ou nome do autor)
        $termo = trim($filtros['busca'] ?? $filtros['q'] ?? '');
        if (!empty($termo)) {
            $query->where(function ($q) use ($termo) {
                $q->where('titulo', 'like', "%{$termo}%")
                  ->orWhere('isbn', 'like', "%{$termo}%")
                  ->orWhere('sinopse', 'like', "%{$termo}%")
                  ->orWhereHas('autor', function ($sub) use ($termo) {
                      $sub->where('nome', 'like', "%{$termo}%");
                  });
            });
        }

        // 2. Filtro por Gênero (por ID ou Nome)
        $filtroGenero = $filtros['genero'] ?? $filtros['categoria'] ?? null;
        if (!empty($filtroGenero)) {
            if (is_array($filtroGenero)) {
                $query->whereIn('genero_id', $filtroGenero);
            } elseif (is_numeric($filtroGenero)) {
                $query->where('genero_id', $filtroGenero);
            } else {
                $query->whereHas('genero', function ($sub) use ($filtroGenero) {
                    $sub->where('nome', 'like', "%{$filtroGenero}%");
                });
            }
        }

        // 3. Filtro por Editora
        if (!empty($filtros['editora'])) {
            $query->where('editora_id', $filtros['editora']);
        }

        // 4. Filtro por Vendedor
        if (!empty($filtros['vendedor'])) {
            $query->where('vendedor_id', $filtros['vendedor']);
        }

        // 5. Filtro por Faixas de Preço pré-definidas
        if (!empty($filtros['faixa_preco'])) {
            match ($filtros['faixa_preco']) {
                'ate-30'   => $query->where('preco', '<=', 30),
                '30-60'    => $query->whereBetween('preco', [30, 60]),
                '60-100'   => $query->whereBetween('preco', [60, 100]),
                'acima-100'=> $query->where('preco', '>', 100),
                default    => null,
            };
        }

        // 6. Faixa de Preço personalizada
        if (isset($filtros['preco_min']) && is_numeric($filtros['preco_min'])) {
            $query->where('preco', '>=', (float) $filtros['preco_min']);
        }
        if (isset($filtros['preco_max']) && is_numeric($filtros['preco_max'])) {
            $query->where('preco', '<=', (float) $filtros['preco_max']);
        }

        // 7. Filtro de Estoque
        if (!empty($filtros['em_estoque'])) {
            $query->where('quantidade', '>', 0);
        }

        // 8. Filtro por Status de Moderação
        if (!empty($filtros['status_moderacao'])) {
            $query->where('status_moderacao', $filtros['status_moderacao']);
        } elseif (!isset($filtros['admin_gestao']) || !$filtros['admin_gestao']) {
            // No catálogo público, garante exibição apenas de livros com status 'ativo'
            $query->where('status_moderacao', self::STATUS_MODERACAO_ATIVO);
        }

        // 9. Ordenação
        $ordem = $filtros['ordem'] ?? 'novidades';
        match ($ordem) {
            'preco_menor' => $query->orderBy('preco', 'asc'),
            'preco_maior' => $query->orderBy('preco', 'desc'),
            'titulo_az'   => $query->orderBy('titulo', 'asc'),
            default       => $query->latest(),
        };

        return $query;
    }

}
