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
    // CONSTANTES DOS DEPARTAMENTOS
    // =========================================================================
    public const DEPARTAMENTO_TECNOLOGIA = 'Tecnologia';
    public const DEPARTAMENTO_EDITORIAL = 'Editorial';
    public const DEPARTAMENTO_COMERCIAL = 'Comercial';
    public const DEPARTAMENTO_OPERACOES = 'Operações';
    public const DEPARTAMENTO_ATENDIMENTO = 'Atendimento';

    // =========================================================================
    // CONSTANTES DOS CARGOS
    // =========================================================================
    public const CARGO_SUPER_ADMIN = 'Super Admin';
    public const CARGO_GERENTE_CATALOGO = 'Gerente de Catálogo';
    public const CARGO_GERENTE_COMERCIAL = 'Gerente Comercial';
    public const CARGO_ANALISTA_OPERACOES = 'Analista de Operações';
    public const CARGO_ATENDENTE_SUPORTE = 'Atendente de Suporte';

    /**
     * Retorna a lista completa de departamentos (Útil para formulários e validações)
     */
    public static function getDepartamentos(): array
    {
        return [
            self::DEPARTAMENTO_TECNOLOGIA,
            self::DEPARTAMENTO_EDITORIAL,
            self::DEPARTAMENTO_COMERCIAL,
            self::DEPARTAMENTO_OPERACOES,
            self::DEPARTAMENTO_ATENDIMENTO,
        ];
    }

    /**
     * Retorna a lista completa de cargos (Útil para formulários e validações)
     */
    public static function getCargos(): array
    {
        return [
            self::CARGO_SUPER_ADMIN,
            self::CARGO_GERENTE_CATALOGO,
            self::CARGO_GERENTE_COMERCIAL,
            self::CARGO_ANALISTA_OPERACOES,
            self::CARGO_ATENDENTE_SUPORTE,
        ];
    }

    /**
     * Relacionamento: Um Admin pertence a um Usuário base (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
