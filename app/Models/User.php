<?php

namespace App\Models;
// Bibliotecas necessárias para o modelo User

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Cliente;
use App\Models\Vendedor;
// definição da classe User que estende Authenticatable
// o Authenticatable fornece funcionalidades de autenticação para o modelo User(usuário)

class User extends Authenticatable
{
    // os Traits HAsFactory e Notifiable são usados para adicionar funcionalidades ao modelo User.
    use HasFactory, Notifiable;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var list<string>
     */
    // Atributos que podem ser atribuídos em massa
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
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

    // Dentro da classe User em app/Models/User.php

    public function cliente()
    {
        // Um usuário possui um (hasOne) perfil de cliente
        return $this->hasOne(Cliente::class);
    }

    public function vendedor()
    {
        // Um usuário possui um (hasOne) perfil de vendedor
        return $this->hasOne(Vendedor::class);
    }
}
