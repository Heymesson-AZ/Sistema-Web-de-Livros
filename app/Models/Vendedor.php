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
     * Determina se o vendedor está apto para vender e operar comercialmente.
     * Requisitos estritos: aprovação cadastral 'aprovado' E status da conta de usuário 'ativo'.
     */
    public function isAptoParaVender(): bool
    {
        return $this->status_aprovacao === self::STATUS_APROVADO && $this->user?->status === 'ativo';
    }

    /**
     * Rótulo descritivo da aprovação cadastral do vendedor.
     */
    public function getAprovacaoRotuloAttribute(): string
    {
        return match ($this->status_aprovacao) {
            self::STATUS_PENDENTE => 'Em Análise',
            self::STATUS_REJEITADO => 'Rejeitado',
            default => 'Aprovado',
        };
    }

    /**
     * Classe CSS da aprovação cadastral.
     */
    public function getAprovacaoBadgeClassAttribute(): string
    {
        return match ($this->status_aprovacao) {
            self::STATUS_PENDENTE => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::STATUS_REJEITADO => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-success-subtle text-success border border-success-subtle',
        };
    }

    /**
     * Ícone da aprovação cadastral.
     */
    public function getAprovacaoIconeAttribute(): string
    {
        return match ($this->status_aprovacao) {
            self::STATUS_PENDENTE => 'bi-hourglass-split',
            self::STATUS_REJEITADO => 'bi-x-circle-fill',
            default => 'bi-check-circle-fill',
        };
    }

    /**
     * Situação consolidada da loja e aptidão de venda.
     */
    public function getSituacaoAttribute(): string
    {
        if ($this->user?->isBanido() || $this->user?->status === 'banido') {
            return 'banido';
        }
        if ($this->status_aprovacao === self::STATUS_REJEITADO) {
            return 'rejeitado';
        }
        if ($this->status_aprovacao === self::STATUS_PENDENTE) {
            return 'pendente';
        }
        if ($this->user?->isInativo() || $this->user?->status === 'inativo') {
            return 'inativo';
        }
        return 'apto';
    }

    /**
     * Rótulo descritivo da situação consolidada / aptidão.
     */
    public function getSituacaoRotuloAttribute(): string
    {
        return match ($this->situacao) {
            'banido' => 'Banido',
            'rejeitado' => 'Cadastro Rejeitado',
            'pendente' => 'Pendente de Análise',
            'inativo' => 'Inativo / Pausado',
            default => 'Apto para Vender',
        };
    }

    /**
     * Classe CSS de badge para a situação consolidada / aptidão.
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
     * Motivo textual detalhado caso o vendedor não esteja apto para vender.
     */
    public function getMotivoInaptoAttribute(): ?string
    {
        if ($this->isAptoParaVender()) {
            return null;
        }
        if ($this->user?->status === 'banido') {
            return 'Conta de acesso banida por infração disciplinar';
        }
        if ($this->status_aprovacao === self::STATUS_REJEITADO) {
            return 'Cadastro reprovado pela auditoria administrativa';
        }
        if ($this->status_aprovacao === self::STATUS_PENDENTE) {
            return 'Aguardando validação cadastral e fiscal';
        }
        if ($this->user?->status === 'inativo') {
            return 'Conta temporariamente desativada ou pausada';
        }
        return 'Requisitos comerciais não atendidos';
    }

    /**
     * Scope para filtrar vendedores por situação consolidada / aptidão.
     */
    public function scopeComSituacao($query, string $situacao)
    {
        return match ($situacao) {
            'banido' => $query->whereHas('user', fn ($q) => $q->where('status', 'banido')),
            'inativo' => $query->where('status_aprovacao', self::STATUS_APROVADO)
                ->whereHas('user', fn ($q) => $q->where('status', 'inativo')),
            'pendente' => $query->where('status_aprovacao', self::STATUS_PENDENTE),
            'rejeitado' => $query->where('status_aprovacao', self::STATUS_REJEITADO),
            'apto', 'aprovado' => $query->where('status_aprovacao', self::STATUS_APROVADO)
                ->whereHas('user', fn ($q) => $q->where('status', 'ativo')),
            default => $query,
        };
    }

    // um vendedor pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // Cupons exclusivos criados pela loja do vendedor
    public function cupons()
    {
        return $this->hasMany(Cupom::class, 'vendedor_id');
    }

    // Campanhas promocionais de incentivo da plataforma às quais o lojista aderiu
    public function campanhasAderidas()
    {
        return $this->belongsToMany(Cupom::class, 'cupom_vendedor_adesao')
                    ->wherePivot('concordou', true)
                    ->withPivot('concordou', 'data_adesao')
                    ->withTimestamps();
    }
}
