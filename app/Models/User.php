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
     * Retorna a URL da foto de perfil ou um avatar padrão.
     */
    public function getFotoAttribute(): string
    {
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }

        // Retorna um avatar gerado automaticamente com as iniciais do nome do usuário
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
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


    public function temPedidosAtivos(): bool
    {
        // Definimos quais status bloqueiam a exclusão
        $statusAtivos = ['pendente', 'processando', 'enviado'];

        if ($this->tipo === 'cliente') {
            // Verifica se o cliente tem algum pedido nesses status
            return $this->cliente->pedidos()
                ->whereIn('status', $statusAtivos)
                ->exists();
        }

        if ($this->tipo === 'vendedor') {
            // Verifica se o vendedor tem algum pedido nesses status
            return $this->vendedor->pedidos()
                ->whereIn('status', $statusAtivos)
                ->exists();
        }

        return false;
    }
}
