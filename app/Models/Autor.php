<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    use HasFactory;

    /**
     * Opcional: Por padrão, o Laravel esperaria a tabela "autors".
     * Como nossa tabela se chama "autor", precisamos definir explicitamente.
     */
    protected $table = 'autor';

    /**
     * Atributos que podem ser preenchidos em massa (Mass Assignment).
     * Isso é essencial para que a Factory e o método Autor::create() funcionem.
     */
    protected $fillable = [
        'nome',
        'nacionalidade',
        'data_nascimento',
        'email',
        'biografia',
        'foto_perfil',
    ];

    /**
     * Opcional: Se você quiser que o Laravel trate a data_nascimento 
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_nascimento' => 'date',
    ];
}