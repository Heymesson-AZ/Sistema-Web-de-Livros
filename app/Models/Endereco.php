<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Endereco extends Model
{
    use HasFactory;

    protected $table = 'enderecos';

    protected $fillable = [
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'pais',
        'complemento',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
