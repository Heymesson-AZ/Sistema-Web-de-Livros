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
        'tipo',
        'principal',
        'user_id',
    ];

    protected $attributes = [
        'pais' => 'Brasil',
        'principal' => false,
        'tipo' => 'residencial',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function getEnderecoFormatadoAttribute(): string
    {
        $partes = ["{$this->rua}, {$this->numero}"];
        if ($this->complemento) {
            $partes[] = $this->complemento;
        }
        $partes[] = $this->bairro;
        $partes[] = "{$this->cidade} - {$this->estado}";
        $partes[] = "CEP {$this->cep}";

        return implode(', ', $partes);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
