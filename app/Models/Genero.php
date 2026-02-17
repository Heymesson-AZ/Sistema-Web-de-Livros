<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;
    
    protected $table = 'generos'; // Especifica o nome da tabela no banco de dados
    protected $fillable = ['nome'];

}
