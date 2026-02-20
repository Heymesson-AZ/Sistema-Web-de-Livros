<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Editora extends Model
{
    use HasFactory;

    protected $table = 'editoras';  
    protected $fillable = ['nome'];

    // uma editora tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class); // Relacionamento de uma editora para muitos livros
    }
    
}
