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

    // um vendedor pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
