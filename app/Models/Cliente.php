<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cliente';

    protected $fillable = [
        'user_id',
        'celular_contato',
        'data_nascimento',
        'cpf',
    ];

    /**
     * Higieniza o CPF para conter apenas dígitos numéricos.
     */
    protected function cpf(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', (string) $value)
        );
    }

    /**
     * Higieniza o telefone de contato para conter apenas dígitos numéricos.
     */
    protected function celularContato(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', (string) $value)
        );
    }

    // o cliente pertence somente a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // o cliente pode ter muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // o cliente pode ter muitas avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

}
