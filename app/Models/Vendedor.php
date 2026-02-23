<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendedor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'vendedores';

    protected $fillable = [
        'user_id',
        'cnpj',
        'telefone',
        'razao_social',
        'nome_fantasia',
        'inscricao_estadual',
    ];


    // o vendedor pertence somente a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // um vendedor pode ter muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }

    // um vendedor pode ter muitas avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

    // um vendedor pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
