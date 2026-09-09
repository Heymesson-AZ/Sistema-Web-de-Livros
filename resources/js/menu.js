/**
 * Gerenciamento do Menu Lateral & Gaveta Móvel (Drawer Mobile)
 * Desenvolvido em Vanilla JavaScript puro (sem frameworks pesados).
 */

document.addEventListener("DOMContentLoaded", () => {
    // Elementos da interface do menu
    const envoltorioMenu = document.getElementById("menuWrapper");
    const botaoAbrirMenu = document.getElementById("menuToggle");
    const botaoFecharMenu = document.getElementById("menuCloseBtn");
    const cortinaFundo = document.getElementById("menuBackdrop");

    /**
     * Abre a gaveta móvel de navegação e bloqueia a rolagem de fundo
     */
    const abrirMenuMovel = () => {
        if (envoltorioMenu) envoltorioMenu.classList.add("is-open");
        if (cortinaFundo) cortinaFundo.classList.add("is-active");
        document.body.style.overflow = "hidden"; // Impede rolagem indesejada do conteúdo
    };

    /**
     * Fecha a gaveta móvel de navegação e restaura a rolagem padrão
     */
    const fecharMenuMovel = () => {
        if (envoltorioMenu) envoltorioMenu.classList.remove("is-open");
        if (cortinaFundo) cortinaFundo.classList.remove("is-active");
        document.body.style.overflow = "";
    };

    // Evento de clique no botão hambúrguer do topo
    if (botaoAbrirMenu) {
        botaoAbrirMenu.addEventListener("click", (evento) => {
            evento.stopPropagation();
            if (
                envoltorioMenu &&
                envoltorioMenu.classList.contains("is-open")
            ) {
                fecharMenuMovel();
            } else {
                abrirMenuMovel();
            }
        });
    }

    // Evento de clique no botão fechar ('X') da gaveta
    if (botaoFecharMenu) {
        botaoFecharMenu.addEventListener("click", (evento) => {
            evento.stopPropagation();
            fecharMenuMovel();
        });
    }

    // Evento de clique no fundo escurecido para fechar a gaveta
    if (cortinaFundo) {
        cortinaFundo.addEventListener("click", fecharMenuMovel);
    }

    // Fecha a gaveta com a tecla Escape
    document.addEventListener("keydown", (evento) => {
        if (
            evento.key === "Escape" &&
            envoltorioMenu &&
            envoltorioMenu.classList.contains("is-open")
        ) {
            fecharMenuMovel();
        }
    });

    // Se o usuário redimensionar para tela de computador (desktop), fecha a gaveta móvel
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 992) {
            fecharMenuMovel();
        }
    });

    // Controle de Submenus sanfonados (Acordeão)
    const itensComSubmenu = document.querySelectorAll(
        ".menu-inner ul li.has-submenu",
    );

    itensComSubmenu.forEach((item) => {
        const cabecalhoItem = item.querySelector(".menu-item-header");
        if (cabecalhoItem) {
            cabecalhoItem.addEventListener("click", (evento) => {
                evento.stopPropagation();

                // Fecha outros submenus abertos para manter visual limpo
                itensComSubmenu.forEach((outroItem) => {
                    if (outroItem !== item) {
                        outroItem.classList.remove("open");
                    }
                });

                // Alterna o submenu clicado
                item.classList.toggle("open");
            });
        }
    });

    // Fecha a gaveta móvel ao clicar em links ou abrir modais
    const gatilhosModais = document.querySelectorAll(
        "#menu [data-bs-toggle='modal'], #menu a.menu-item-link",
    );
    gatilhosModais.forEach((gatilho) => {
        gatilho.addEventListener("click", () => {
            fecharMenuMovel();
        });
    });

    // Inicializa ícones Lucide caso a biblioteca externa esteja carregada
    if (typeof window.lucide !== "undefined") {
        window.lucide.createIcons();
    }
});

// Garante renderização dos ícones Lucide após o carregamento completo da página
window.addEventListener("load", () => {
    if (typeof window.lucide !== "undefined") {
        window.lucide.createIcons();
    }
});
