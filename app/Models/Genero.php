<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Genero extends Model
{
    use HasFactory;
    
    protected $table = 'generos'; // Especifica o nome da tabela no banco de dados
    protected $fillable = ['nome'];

    /**
     * Padroniza o nome do gênero em Title Case.
     */
    protected function nome(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    // um gênero tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class); // Relacionamento de um gênero para muitos livros
    }

}
