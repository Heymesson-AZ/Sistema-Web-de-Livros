<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Livro\LivroGestaoController;

/**
 * Controlador de Gestão de Livros para Administradores
 *
 * O Administrador gerencia o sistema e seus usuários:
 * Audita o catálogo geral, modera informações e exclui conteúdos irregulares.
 * A publicação de novos livros é função exclusiva de vendedores parceiros.
 */
class LivroAdminController extends LivroGestaoController
{
    /**
     * Define o contexto administrativo
     */
    protected bool $isAdmin = true;
}
