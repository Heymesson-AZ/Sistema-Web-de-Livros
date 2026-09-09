<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedidos_itens';

    protected $fillable = [
        'pedido_id',
        'livro_id',
        'quantidade_itens',
        'valor_unitario',
    ];

    protected $casts = [
        'quantidade_itens' => 'integer',
        'valor_unitario' => 'decimal:2',
    ];

    public function getSubtotalAttribute(): float
    {
        return (float) $this->valor_unitario * (int) $this->quantidade_itens;
    }

    public function getSubtotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->valor_unitario, 2, ',', '.');
    }

    // um item do pedido pertence a um pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, "pedido_id");
    }

    public function livro()
    {
        // O item do pedido pertence a um livro
        return $this->belongsTo(Livro::class, 'livro_id');
    }
}
