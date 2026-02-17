<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vendedor extends Model
{
    use HasFactory;
    protected $table = 'vendedores';

    protected $fillable = [
        'user_id',
        'cnpj',
        'telefone',
        'razao_social',
        'nome_fantasia',
        'inscricao_estadual',
    ];
}
