<?php

namespace App\Models;
// Bibliotecas necessárias para o modelo User

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\Admin;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

// definição da classe User que estende Authenticatable
// o Authenticatable fornece funcionalidades de autenticação para o modelo User(usuário)

class User extends Authenticatable implements MustVerifyEmail
{

    // os Traits HAsFactory e Notifiable são usados para adicionar funcionalidades ao modelo User.
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
        'status',
        'foto_perfil'
    ];

    /**
     * Os atributos que devem ser ocultados para arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @return array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Padroniza o nome do usuário com iniciais maiúsculas (Title Case) e remove espaços excessivos.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_convert_case(preg_replace('/\s+/', ' ', (string) $value), MB_CASE_TITLE, 'UTF-8'))
        );
    }

    /**
     * Padroniza o e-mail sempre em minúsculas e sem espaços ao redor.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim(mb_strtolower((string) $value, 'UTF-8'))
        );
    }

    /**
     * Retorna apenas o primeiro nome do usuário.
     */
    public function getPrimeiroNomeAttribute(): string
    {
        $partes = explode(' ', trim((string) $this->name));
        return $partes[0] ?: (string) $this->name;
    }

    // Definição dos Perfis ( Verificar se o usuário é cliente, vendedor ou admin)


    /**
     * Retorna um avatar vetorial SVG embutido em Data URI com as iniciais do usuário.
     * Funciona 100% offline, com zero requisições externas, sem risco de timeout ou bloqueios.
     */
    public function getFotoPadraoAttribute(): string
    {
        $nome = trim((string) ($this->name ?: 'U'));
        $partes = preg_split('/\s+/', $nome);
        $iniciais = mb_strtoupper(mb_substr($partes[0] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8');
        if (isset($partes[1]) && !empty($partes[1])) {
            $iniciais .= mb_strtoupper(mb_substr($partes[1], 0, 1, 'UTF-8'), 'UTF-8');
        }

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 128 128' width='128' height='128'>"
            . "<defs>"
            . "<linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'>"
            . "<stop offset='0%' stop-color='#2563eb'/>"
            . "<stop offset='100%' stop-color='#1d4ed8'/>"
            . "</linearGradient>"
            . "</defs>"
            . "<circle cx='64' cy='64' r='64' fill='url(%23grad)'/>"
            . "<text x='50%' y='54%' dominant-baseline='middle' text-anchor='middle' fill='#ffffff' font-family='sans-serif' font-size='48' font-weight='bold'>"
            . htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8')
            . "</text>"
            . "</svg>";

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    /**
     * Retorna a URL da foto de perfil ou o avatar vetorial seguro padrão.
     */
    public function getFotoAttribute(): string
    {
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }

        return $this->foto_padrao;
    }

    public function isCliente()
    {
        return $this->tipo === 'cliente';
    }

    public function isVendedor()
    {
        return $this->tipo === 'vendedor';
    }


    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function isAdmin()
    {
        return $this->tipo === 'admin';
    }

    /**
     * Regras de permissão por cargo administrativo
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && ($this->admin ? $this->admin->isSuperAdmin() : true);
    }

    public function podeGerenciarAdministradores(): bool
    {
        return $this->isAdmin() && ($this->admin ? $this->admin->podeGerenciarAdministradores() : true);
    }

    public function podeModerarLivros(): bool
    {
        return $this->isAdmin() && ($this->admin ? $this->admin->podeModerarLivros() : true);
    }

    public function podeGerenciarVendedores(): bool
    {
        return $this->isAdmin() && ($this->admin ? $this->admin->podeGerenciarVendedores() : true);
    }

    public function podeGerenciarCupons(): bool
    {
        return $this->isAdmin() && ($this->admin ? $this->admin->podeGerenciarCupons() : true);
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isInativo(): bool
    {
        return $this->status === 'inativo';
    }

    public function isBanido(): bool
    {
        return $this->status === 'banido';
    }

    /////////////////////////////////////////
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'user_id');
    }

    public function vendedor()
    {
        // Um usuário possui um (hasOne) perfil de vendedor
        return $this->hasOne(Vendedor::class);
    }

    // um usuario pode er um carrinho
    public function carrinho()
    {
        return $this->hasOne(Carrinho::class);
    }

    // Avaliações feitas pelo usuário através do perfil de cliente
    public function avaliacoes()
    {
        return $this->hasManyThrough(Avaliacao::class, Cliente::class, 'user_id', 'cliente_id');
    }

    // um usuario pode ter varios favoritos
    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }

    // um usuario pode ter varios endereços
    public function enderecos()
    {
        return $this->hasMany(Endereco::class);
    }

    // um usuario pode ter varios cartoes salvos
    public function cartoesSalvos()
    {
        return $this->hasMany(CartaoSalvo::class);
    }


    /**
     * Verifica se o usuário possui pedidos ativos, pendências financeiras ou problemas em aberto
     * que impedem a exclusão da própria conta.
     */
    public function temPedidosAtivos(): bool
    {
        // Status que representam pedidos em andamento, problemas, disputas ou pendências
        $statusBloqueantes = [
            'pendente',
            'processando',
            'enviado',
            'em_disputa',
            'reclamacao',
            'devolucao',
            'aguardando_pagamento',
        ];

        // 1. Como cliente (comprador)
        if ($this->cliente) {
            $temComoCliente = $this->cliente->pedidos()
                ->whereIn('status', $statusBloqueantes)
                ->exists();

            if ($temComoCliente) {
                return true;
            }
        }

        // 2. Como vendedor (lojista)
        if ($this->vendedor) {
            // Pedidos atribuídos diretamente à loja
            $temComoVendedor = $this->vendedor->pedidos()
                ->whereIn('status', $statusBloqueantes)
                ->exists();

            if ($temComoVendedor) {
                return true;
            }

            // Pedidos que contenham itens vinculados aos livros deste vendedor
            $temItensEmPedidosAtivos = PedidoItem::whereHas('livro', function ($q) {
                $q->where('vendedor_id', $this->vendedor->id);
            })->whereHas('pedido', function ($q) use ($statusBloqueantes) {
                $q->whereIn('status', $statusBloqueantes);
            })->exists();

            if ($temItensEmPedidosAtivos) {
                return true;
            }
        }

        return false;
    }
}
