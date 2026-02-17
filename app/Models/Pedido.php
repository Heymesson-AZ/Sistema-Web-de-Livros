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
}
