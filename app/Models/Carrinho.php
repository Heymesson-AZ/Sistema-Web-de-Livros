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
            ->withPivot('quantidade')
            ->withTimestamps();
    }

    /**
     * Calcula a soma dos subtotais de todos os itens do carrinho.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->livros->sum(function ($livro) {
            return (float) $livro->preco * (int) ($livro->pivot->quantidade ?? 1);
        });
    }

    /**
     * Soma de unidades de todos os exemplares adicionados ao carrinho.
     */
    public function getQuantidadeTotalAttribute(): int
    {
        return (int) $this->livros->sum('pivot.quantidade');
    }

    /**
     * Verifica se o carrinho está vazio.
     */
    public function isEmpty(): bool
    {
        return $this->livros->isEmpty();
    }
}
