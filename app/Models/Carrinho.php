<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrinho extends Model
{

    use HasFactory;

    protected $table = 'carrinhos';

    protected $fillable = [
        'user_id',
        'livro_id',
        'quantidade',
    ];

    // um carrinho pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // um carrinho pode ter muitos livros (relação muitos-para-muitos)
    public function livros()
    {   
        // a tabela intermediária 'carrinho_livro' tem os campos 'carrinho_id', 'livro_id' e
        // 'quantidade'
        // o método withPivot('quantidade') permite acessar a quantidade de cada livro no carrinho
        return $this->belongsToMany(Livro::class, 'carrinho_livro')
            ->withPivot('quantidade');
    }
}
