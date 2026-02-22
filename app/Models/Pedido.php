<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    //
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'numero_pedido',
        'status',
        'total',
        'data_pedido',
        'vendedor_id',
        'cupom_id',
        'cliente_id',

    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_pedido
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_pedido' => 'date',
    ];


    // um pedido pertence a um vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    // um pedido pedido pertence a um cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // um livro pode estar um muitos pedidos, e um pedido pode ter muitos livros
    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }

    // um pedido pode ter somente um cupom
    public function cupom()
    {
        return $this->belongsTo(Cupom::class);
    }

    // um pedido pode ter pelo menos um pagamento
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    public function entrega()
    {
        // O Pedido "tem uma" entrega
        return $this->hasOne(PedidoEntrega::class);
    }

    // um pedido pode ter uma avalação
    public function avaliacao()
    {
        return $this->hasOne(Avaliacao::class);
    }
}
