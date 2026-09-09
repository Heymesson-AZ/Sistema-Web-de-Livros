<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'pedido_id',
        'metodo_pagamento',
        'status_pagamento',
        'id_transacao',
        'valor_pago',
        'data_confirmacao_pagamento',
    ];


    protected $casts = [
        'data_confirmacao_pagamento' => 'datetime',
        'valor_pago' => 'decimal:2',
    ];

    public function getMetodoFormatadoAttribute(): string
    {
        return match ($this->metodo_pagamento) {
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'pix' => 'PIX Instantâneo',
            'boleto' => 'Boleto Bancário',
            default => ucfirst((string) $this->metodo_pagamento),
        };
    }

    // um pagamento pertence a um pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }


}
