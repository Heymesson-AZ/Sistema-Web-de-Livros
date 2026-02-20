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
        return $this->belongsToMany(Livro::class, 'carrinho_livro')
            ->withPivot('quantidade');
    }
}
