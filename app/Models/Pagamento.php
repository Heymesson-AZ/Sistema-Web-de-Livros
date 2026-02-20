<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Testing\Fluent\Concerns\Has;

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
        'status_pagamento',
    ];


    // Cast para data_confirmacao_pagamento
    
    protected $casts = [
        'data_confirmacao_pagamento' => 'datetime',
    ];

    // um pagamento pertence a um pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    
}
