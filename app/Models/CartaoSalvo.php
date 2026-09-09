<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaoSalvo extends Model
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

    protected $casts = [
        'cartao_padrao' => 'boolean',
    ];

    /**
     * Retorna descrição amigável do cartão (ex: Mastercard final •••• 1234).
     */
    public function getDescricaoFormatadaAttribute(): string
    {
        $bandeira = ucfirst($this->bandeira_cartao);
        return "{$bandeira} final •••• {$this->ultimos_digitos}";
    }

    public function getPadraoAttribute(): bool
    {
        return (bool) $this->cartao_padrao;
    }

    public function getIsPadraoAttribute(): bool
    {
        return (bool) $this->cartao_padrao;
    }

    // um cartao pertece a um usuario
    public function user () {
        return $this->belongsTo(User::class, 'user_id');
    }
}
