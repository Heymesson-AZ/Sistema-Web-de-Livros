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
    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_publicacao
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_publicacao' => 'date',
    ];

}
