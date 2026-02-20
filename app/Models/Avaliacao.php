<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';
    
    protected $fillable = [
        'vendedor_id',
        'cliente_id',
        'pedido_id',
        'avaliacao',
        'comentario',
        'recomenda',
    ];

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    
}
