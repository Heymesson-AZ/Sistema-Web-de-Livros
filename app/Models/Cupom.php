<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'vendedor_id',
        'tipo_origem',
        'codigo',
        'tipo_desconto',
        'valor_desconto',
        'limite_uso',
        'validade_cupom',
        'requer_concordancia',
    ];

    protected $casts = [
        'validade_cupom' => 'date',
        'valor_desconto' => 'decimal:2',
        'limite_uso' => 'integer',
        'requer_concordancia' => 'boolean',
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

    // Vendedor dono do cupom (quando for cupom de loja)
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class, 'vendedor_id');
    }

    // Adesões/concordâncias de vendedores para cupons de incentivo do admin
    public function vendedoresAderidos()
    {
        return $this->belongsToMany(Vendedor::class, 'cupom_vendedor_adesao')
                    ->wherePivot('concordou', true)
                    ->withPivot('concordou', 'data_adesao')
                    ->withTimestamps();
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
     * Identifica se é um cupom específico de loja de vendedor.
     */
    public function isDeLoja(): bool
    {
        return $this->tipo_origem === 'loja' || !empty($this->vendedor_id);
    }

    /**
     * Identifica se é um cupom geral da plataforma emitido por administradores.
     */
    public function isDaPlataforma(): bool
    {
        return !$this->isDeLoja();
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
     * Verifica se o cupom é elegível para uma coleção de itens (carrinho ou pedido)
     * e calcula o desconto incidente exclusivamente sobre os itens participantes.
     */
    public function calcularDescontoParaItens($itens): array
    {
        if (!$this->isValido() || empty($itens) || count($itens) === 0) {
            return ['desconto' => 0.0, 'subtotal_elegivel' => 0.0, 'aplicavel' => false, 'motivo' => 'Cupom inválido ou expirado.'];
        }

        $subtotalElegivel = 0.0;
        $extrairDadosItem = function ($item) {
            $vendedorId = $item->vendedor_id ?? $item->livro?->vendedor_id ?? null;
            $preco = (float) ($item->preco ?? $item->preco_unitario ?? $item->livro?->preco ?? 0.0);
            $qtd = (int) ($item->quantidade_carrinho ?? $item->quantidade_itens ?? $item->quantidade ?? 1);
            return [(int) $vendedorId, $preco, $qtd];
        };

        // Se for cupom de loja do vendedor
        if ($this->isDeLoja()) {
            foreach ($itens as $item) {
                [$vendedorId, $preco, $qtd] = $extrairDadosItem($item);
                if ($vendedorId === (int) $this->vendedor_id) {
                    $subtotalElegivel += $preco * $qtd;
                }
            }

            if ($subtotalElegivel <= 0) {
                $nomeLoja = $this->vendedor?->nome_fantasia ?: 'da loja participante';
                return [
                    'desconto' => 0.0,
                    'subtotal_elegivel' => 0.0,
                    'aplicavel' => false,
                    'motivo' => "Este cupom é exclusivo para livros vendidos por {$nomeLoja} e não há itens dessa loja no seu carrinho.",
                ];
            }
        } elseif ($this->requer_concordancia) {
            // Cupom promocional de incentivo do admin com concordância de vendedores
            $idsVendedoresAderidos = $this->vendedoresAderidos()->pluck('vendedores.id')->toArray();

            foreach ($itens as $item) {
                [$vendedorId, $preco, $qtd] = $extrairDadosItem($item);
                if (in_array($vendedorId, $idsVendedoresAderidos, true)) {
                    $subtotalElegivel += $preco * $qtd;
                }
            }

            if ($subtotalElegivel <= 0) {
                return [
                    'desconto' => 0.0,
                    'subtotal_elegivel' => 0.0,
                    'aplicavel' => false,
                    'motivo' => 'Este cupom promocional aplica-se a lojas participantes e os vendedores dos itens do seu carrinho ainda não aderiram à campanha.',
                ];
            }
        } else {
            // Cupom global geral da plataforma: aplica a todos os itens
            foreach ($itens as $item) {
                [, $preco, $qtd] = $extrairDadosItem($item);
                $subtotalElegivel += $preco * $qtd;
            }
        }

        $desconto = 0.0;
        if ($this->tipo_desconto === 'percentual') {
            $desconto = ($subtotalElegivel * (float) $this->valor_desconto) / 100;
        } else {
            $desconto = (float) $this->valor_desconto;
        }

        $descontoFinal = (float) min($desconto, $subtotalElegivel);

        return [
            'desconto' => $descontoFinal,
            'subtotal_elegivel' => $subtotalElegivel,
            'aplicavel' => true,
            'motivo' => null,
        ];
    }

    /**
     * Calcula o valor em Reais do desconto aplicado a um subtotal simples.
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
