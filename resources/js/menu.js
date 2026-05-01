import $ from 'jquery';

$(window).on('load', function () {
    // Gerencia a abertura/fechamento das Categorias e Conta
    $('.menu-inner ul li:has(ul)').on('click', function (e) {
        // Evita que o clique feche o menu principal ou dispare outros eventos
        e.stopPropagation();

        // Fecha outros submenus abertos para manter o visual limpo
        $('.menu-inner ul li').not(this).removeClass('open');

        // Abre/Fecha o submenu atual
        $(this).toggleClass('open');
    });

    // Garante que os ícones do Lucide sejam renderizados
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
