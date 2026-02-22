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
