<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Livro\LivroGestaoController;

/**
 * Controlador de Gestão de Livros para Vendedores
 *
 * Os Vendedores aprovados publicam, editam, precificam e gerenciam
 * o estoque exclusivo de sua própria loja parceira.
 */
class LivroVendedorController extends LivroGestaoController
{
    /**
     * Define o contexto do vendedor (isolado em sua loja)
     */
    protected bool $isAdmin = false;
}
