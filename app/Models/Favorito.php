<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Favorito extends Model
{
    //
    use HasFactory;

    protected $table = 'favoritos';
    protected $fillable = [
        'user_id',
        'livro_id',
    ];
}
