<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoEntrega extends Model
{
    use HasFactory;

    protected $table = 'pedido_entregas';

    protected $fillable = [
        'pedido_id',
        'status',
        'codigo_rastreamento',
        'url_nota_fiscal',
        'data_previsao_entrega',
        'data_entrega',
        'nome_recebedor',
        'cpf_recebedor',
        'valor_frete',
        'metodo_envio',
        'rua',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'pais',
        'complemento',
    ];


    // casts das datas para Carbon
    protected $casts = [
        'data_previsao_entrega' => 'date',
        'data_entrega' => 'date',
    ];

    public function pedido()
    {
        // A Entrega "pertence a" um pedido
        return $this->belongsTo(Pedido::class);
    }
 
}
