<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cliente extends Model
{
    use HasFactory;
    protected $table = 'clientes';
    
    protected $fillable = [
        'user_id',
        'telefone',
        'data_nascimento',
        'cpf',
    ];


    // o cliente pertence somente a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // o cliente pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // o cliente pode ter muitas avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

}
