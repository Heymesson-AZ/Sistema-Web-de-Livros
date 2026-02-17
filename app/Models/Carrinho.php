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
}
