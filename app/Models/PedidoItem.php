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
    
}
