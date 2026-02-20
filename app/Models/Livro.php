<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livro extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'data_publicacao',
        'isbn',
        'sinopse',
        'capa',
        'preco',
        'quantidade',
        'autor_id',
        'genero_id',
        'editora_id',
        'vendedor_id',
    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_publicacao
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_publicacao' => 'date',
    ];


    // um livro pertence a um autor
    public function autor()
    {
        return $this->belongsTo(Autor::class); // Relacionamento de um livro para um autor
    }

    // um livro pertence a um gênero
    public function genero()
    {
        return $this->belongsTo(Genero::class); // Relacionamento de um livro para um gênero
    }

    // um livro pertence a uma editora
    public function editora()
    {
        return $this->belongsTo(Editora::class); // Relacionamento de um livro para uma editora
    }

    // um livro pertence a um vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class); // Relacionamento de um livro para um vendedor
    }

    // um livro pode ser muitos favoritado (uma relação muitos-para-muitos)
    public function favoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos');  // Relacionamento de muitos para muitos entre livros e usuários (favoritos)
    }


    // Um livro pode estar em vários carrinhos (itens do carrinho)
    public function itensCarrinho()
    {
        return $this->hasMany(Carrinho::class); // Relacionamento de um livro para muitos itens do carrinho
    }

    // um livro pode estar em muitos itens do pedido (uma relação muitos-para-muitos)
    public function itensPedido()
    {
        return $this->hasMany(PedidoItem::class); // Relacionamento de um livro para muitos itens do pedido (pedido_itens)
    }

}
