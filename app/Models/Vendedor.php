<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Vendedor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'vendedores';

    protected $fillable = [
        'user_id',
        'cnpj',
        'telefone_comercial',
        'razao_social',
        'nome_fantasia',
        'inscricao_estadual',
        'status_aprovacao',
    ];

    /**
     * Padroniza Razão Social em Title Case.
     */
    protected function razaoSocial(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    /**
     * Padroniza Nome Fantasia em Title Case.
     */
    protected function nomeFantasia(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    /**
     * Higieniza o CNPJ mantendo apenas números.
     */
    protected function cnpj(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', (string) $value)
        );
    }

    /**
     * Higieniza o telefone comercial mantendo apenas números.
     */
    protected function telefoneComercial(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', (string) $value)
        );
    }


    // o vendedor pertence somente a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // um vendedor pode ter muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }

    // um vendedor pode ter muitas avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_REJEITADO = 'rejeitado';

    // Helpers de status
    public function isAprovado(): bool
    {
        return $this->status_aprovacao === self::STATUS_APROVADO;
    }

    public function isPendente(): bool
    {
        return $this->status_aprovacao === self::STATUS_PENDENTE;
    }

    public function isRejeitado(): bool
    {
        return $this->status_aprovacao === self::STATUS_REJEITADO;
    }

    /**
     * Situação unificada da loja e conta do vendedor.
     */
    public function getSituacaoAttribute(): string
    {
        if ($this->user?->isBanido()) {
            return 'banido';
        }
        if ($this->status_aprovacao === self::STATUS_REJEITADO) {
            return 'rejeitado';
        }
        if ($this->status_aprovacao === self::STATUS_PENDENTE) {
            return 'pendente';
        }
        if ($this->user?->isInativo()) {
            return 'inativo';
        }
        return 'aprovado';
    }

    /**
     * Rótulo descritivo da situação consolidada.
     */
    public function getSituacaoRotuloAttribute(): string
    {
        return match ($this->situacao) {
            'banido' => 'Banido',
            'rejeitado' => 'Rejeitado',
            'pendente' => 'Pendente de Análise',
            'inativo' => 'Inativo / Pausado',
            default => 'Aprovado & Ativo',
        };
    }

    /**
     * Classe CSS de badge para a situação consolidada.
     */
    public function getSituacaoBadgeClassAttribute(): string
    {
        return match ($this->situacao) {
            'banido' => 'bg-danger text-white',
            'rejeitado' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'pendente' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'inativo' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default => 'bg-success-subtle text-success border border-success-subtle',
        };
    }

    /**
     * Ícone Bootstrap correspondente à situação.
     */
    public function getSituacaoIconeAttribute(): string
    {
        return match ($this->situacao) {
            'banido' => 'bi-slash-circle-fill',
            'rejeitado' => 'bi-x-circle-fill',
            'pendente' => 'bi-hourglass-split',
            'inativo' => 'bi-pause-circle-fill',
            default => 'bi-check-circle-fill',
        };
    }

    /**
     * Scope para filtrar vendedores por situação consolidada.
     */
    public function scopeComSituacao($query, string $situacao)
    {
        return match ($situacao) {
            'banido' => $query->whereHas('user', fn ($q) => $q->where('status', 'banido')),
            'inativo' => $query->where('status_aprovacao', self::STATUS_APROVADO)
                ->whereHas('user', fn ($q) => $q->where('status', 'inativo')),
            'pendente' => $query->where('status_aprovacao', self::STATUS_PENDENTE),
            'rejeitado' => $query->where('status_aprovacao', self::STATUS_REJEITADO),
            'aprovado' => $query->where('status_aprovacao', self::STATUS_APROVADO)
                ->whereHas('user', fn ($q) => $q->where('status', 'ativo')),
            default => $query,
        };
    }

    // um vendedor pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
