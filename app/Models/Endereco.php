<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Endereco extends Model
{
    use HasFactory;

    protected $tables = 'enderecos';

    protected $fillable = [
        'rua',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'pais',
        'complemento',
        'user_id',
    ];

}
