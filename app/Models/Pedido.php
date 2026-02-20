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
        'user_id',
        'cupom_id',
    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_pedido
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_pedido' => 'date',
    ];


    // um pedido pedido pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // um livro pode estar um muitos pedidos, e um pedido pode ter muitos livros
    public function livros()
    {   
        return $this->belongsToMany(Livro::class, 'pedido_livro', 'pedido_id', 'livro_id')
                    ->withPivot('quantidade', 'preco_unitario'); 
                    // se quiser acessar a quantidade e preço unitário
    }
    
    // um pedido pode ter somente um cupom
    public function cupom()
    {
        return $this->belongsTo(Cupom::class);
    }

    // um peido pode ter pelo menos um pagamento
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    // um pedido pode ter um endereço de entrega
    public function enderecoEntrega()
    {
        return $this->hasOne(PedidoEntrega::class);
    }

    // 
    public function avaliacao()
    {
        return $this->hasOne(Avaliacao::class);
    }
}
