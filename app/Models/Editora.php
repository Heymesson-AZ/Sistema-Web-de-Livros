<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Editora extends Model
{
    use HasFactory;

    protected $table = 'editoras';
    protected $fillable = ['nome'];

    /**
     * Padroniza o nome da editora em Title Case.
     */
    protected function nome(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    // uma editora tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class); // Relacionamento de uma editora para muitos livros
    }

}
