<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Autor extends Model
{
    use HasFactory;

    /**
     * Opcional: Por padrão, o Laravel esperaria a tabela "autors".
     * Como nossa tabela se chama "autor", precisamos definir explicitamente.
     */
    protected $table = 'autores';

    /**
     * Atributos que podem ser preenchidos em massa (Mass Assignment).
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
     * Padroniza o nome do autor em Title Case.
     */
    protected function nome(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    /**
     * Opcional: Se você quiser que o Laravel trate a data_nascimento 
     * como um objeto Carbon (data) automaticamente.
     */
    protected $casts = [
        'data_nascimento' => 'date',
    ];

    // relacinamemto com livros, um autor tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}