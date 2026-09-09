<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    /**
     * Define explicitamente a tabela no singular para bater com a migration create_admin_table
     */
    protected $table = 'admin';

    /**
     * Campos liberados para preenchimento em massa (Mass Assignment)
     */
    protected $fillable = [
        'user_id',
        'telefone_urgencia',
        'cargo',
        'departamento',
    ];

    // =========================================================================
    // CONSTANTES DOS DEPARTAMENTOS (Contextualizados e simplificados)
    // =========================================================================
    public const DEPARTAMENTO_DIRETORIA = 'Diretoria Executiva';
    public const DEPARTAMENTO_MODERACAO = 'Moderação e Catálogo';
    public const DEPARTAMENTO_COMERCIAL = 'Comercial e Parcerias';

    // Aliases para retrocompatibilidade
    public const DEPARTAMENTO_TECNOLOGIA = 'Diretoria Executiva';
    public const DEPARTAMENTO_EDITORIAL = 'Moderação e Catálogo';
    public const DEPARTAMENTO_OPERACOES = 'Moderação e Catálogo';
    public const DEPARTAMENTO_OPERACIONAL = 'Moderação e Catálogo';
    public const DEPARTAMENTO_ATENDIMENTO = 'Comercial e Parcerias';

    // =========================================================================
    // CONSTANTES DOS CARGOS (Contexto Universo de Papel)
    // =========================================================================
    public const CARGO_SUPER_ADMIN = 'Super Admin';
    public const CARGO_MODERADOR = 'Moderador de Catálogo';
    public const CARGO_GESTOR_COMERCIAL = 'Gestor Comercial';

    // Aliases para retrocompatibilidade
    public const CARGO_ADMINISTRADOR = 'Super Admin';
    public const CARGO_GERENTE_CATALOGO = 'Moderador de Catálogo';
    public const CARGO_GERENTE_COMERCIAL = 'Gestor Comercial';
    public const CARGO_ANALISTA_OPERACOES = 'Moderador de Catálogo';
    public const CARGO_ATENDENTE_SUPORTE = 'Gestor Comercial';

    /**
     * Retorna a lista completa de departamentos simplificados
     */
    public static function getDepartamentos(): array
    {
        return [
            self::DEPARTAMENTO_DIRETORIA,
            self::DEPARTAMENTO_MODERACAO,
            self::DEPARTAMENTO_COMERCIAL,
        ];
    }

    /**
     * Retorna a lista de cargos simplificados para a plataforma
     */
    public static function getCargos(): array
    {
        return [
            self::CARGO_SUPER_ADMIN,
            self::CARGO_MODERADOR,
            self::CARGO_GESTOR_COMERCIAL,
        ];
    }

    // =========================================================================
    // REGRAS DE NEGÓCIO E PERMISSÕES POR CARGO
    // =========================================================================

    /**
     * Verifica se o administrador é Super Admin (acesso total irrestrito)
     */
    public function isSuperAdmin(): bool
    {
        return in_array($this->cargo, [self::CARGO_SUPER_ADMIN, 'Super Admin', 'Administrador'], true);
    }

    /**
     * Verifica se o administrador atua na moderação e catálogo
     */
    public function isModerador(): bool
    {
        return in_array($this->cargo, [self::CARGO_MODERADOR, 'Moderador de Catálogo', 'Gerente de Catálogo', 'Analista de Operações'], true);
    }

    /**
     * Verifica se o administrador atua na gestão comercial e de parceiros
     */
    public function isGestorComercial(): bool
    {
        return in_array($this->cargo, [self::CARGO_GESTOR_COMERCIAL, 'Gestor Comercial', 'Gerente Comercial', 'Atendente de Suporte'], true);
    }

    /**
     * Regra: Somente o Super Admin pode gerenciar outros administradores
     */
    public function podeGerenciarAdministradores(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Regra: Super Admin e Moderador de Catálogo podem moderar obras
     */
    public function podeModerarLivros(): bool
    {
        return $this->isSuperAdmin() || $this->isModerador();
    }

    /**
     * Regra: Super Admin e Gestor Comercial podem aprovar/gerenciar vendedores parceiros
     */
    public function podeGerenciarVendedores(): bool
    {
        return $this->isSuperAdmin() || $this->isGestorComercial();
    }

    /**
     * Regra: Super Admin e Gestor Comercial podem gerenciar cupons de desconto
     */
    public function podeGerenciarCupons(): bool
    {
        return $this->isSuperAdmin() || $this->isGestorComercial();
    }

    /**
     * Relacionamento: Um Admin pertence a um Usuário base (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
