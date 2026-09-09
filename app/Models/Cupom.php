<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'codigo',
        'tipo_desconto',
        'valor_desconto',
        'limite_uso',
        'validade_cupom',
    ];

    protected $casts = [
        'validade_cupom' => 'date',
        'valor_desconto' => 'decimal:2',
        'limite_uso' => 'integer',
    ];

    /**
     * Padroniza o código do cupom sempre em letras maiúsculas e sem espaços.
     */
    protected function codigo(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: fn ($value) => strtoupper(trim((string) $value))
        );
    }

    // Relacionamento com a tabela de pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cupom_id');
    }

    /**
     * Quantidade de vezes que o cupom já foi utilizado em pedidos.
     */
    public function getUsosAtuaisAttribute(): int
    {
        return $this->pedidos()->count();
    }

    /**
     * Verifica se o cupom está dentro do período de validade e do limite de usos.
     */
    public function isValido(): bool
    {
        if ($this->validade_cupom && $this->validade_cupom->endOfDay()->isPast()) {
            return false;
        }

        if ($this->limite_uso !== null && $this->usos_atuais >= $this->limite_uso) {
            return false;
        }

        return true;
    }

    /**
     * Calcula o valor em Reais do desconto aplicado a um subtotal.
     */
    public function calcularDesconto(float $subtotal): float
    {
        if (!$this->isValido() || $subtotal <= 0) {
            return 0.0;
        }

        if ($this->tipo_desconto === 'percentual') {
            $desconto = ($subtotal * (float) $this->valor_desconto) / 100;
        } else {
            $desconto = (float) $this->valor_desconto;
        }

        return (float) min($desconto, $subtotal);
    }

    /**
     * Descrição amigável do benefício do cupom.
     */
    public function getDescricaoDescontoAttribute(): string
    {
        if ($this->tipo_desconto === 'percentual') {
            return number_format((float) $this->valor_desconto, 0) . '% de desconto';
        }

        return 'R$ ' . number_format((float) $this->valor_desconto, 2, ',', '.') . ' de desconto';
    }
}
