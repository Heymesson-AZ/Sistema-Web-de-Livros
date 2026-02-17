<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CartoesSalvos extends Model
{
    use HasFactory;

    protected $table = 'cartoes_salvos';

    protected $fillable = [
        'user_id',
        'token_cartao',
        'ultimos_digitos',
        'bandeira_cartao',
        'cartao_padrao',
    ];
}
