// Gerenciamento do Menu Lateral & Drawer Mobile (Vanilla JS)
document.addEventListener("DOMContentLoaded", () => {
    const menuWrapper = document.getElementById("menuWrapper");
    const menuToggle = document.getElementById("menuToggle");
    const menuCloseBtn = document.getElementById("menuCloseBtn");
    const menuBackdrop = document.getElementById("menuBackdrop");

    // Funções de controle do Drawer Mobile
    const openMobileMenu = () => {
        if (menuWrapper) menuWrapper.classList.add("is-open");
        if (menuBackdrop) menuBackdrop.classList.add("is-active");
        document.body.style.overflow = "hidden"; // Impede rolagem de fundo no mobile
    };

    const closeMobileMenu = () => {
        if (menuWrapper) menuWrapper.classList.remove("is-open");
        if (menuBackdrop) menuBackdrop.classList.remove("is-active");
        document.body.style.overflow = "";
    };

    if (menuToggle) {
        menuToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            if (menuWrapper && menuWrapper.classList.contains("is-open")) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    if (menuCloseBtn) {
        menuCloseBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            closeMobileMenu();
        });
    }

    if (menuBackdrop) {
        menuBackdrop.addEventListener("click", closeMobileMenu);
    }

    // Fecha o menu móvel com a tecla Escape
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && menuWrapper && menuWrapper.classList.contains("is-open")) {
            closeMobileMenu();
        }
    });

    // Fecha gaveta se a janela for redimensionada para desktop
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 992) {
            closeMobileMenu();
        }
    });

    // Submenus (Acordeão)
    const submenuItems = document.querySelectorAll(
        ".menu-inner ul li.has-submenu"
    );

    submenuItems.forEach((item) => {
        const header = item.querySelector(".menu-item-header");
        if (header) {
            header.addEventListener("click", (e) => {
                e.stopPropagation();

                // Fecha outros submenus abertos para manter a visualização limpa
                submenuItems.forEach((other) => {
                    if (other !== item) {
                        other.classList.remove("open");
                    }
                });

                // Alterna o submenu atual
                item.classList.toggle("open");
            });
        }
    });

    // Ao clicar em links ou gatilhos de modal dentro do menu no mobile, fecha a gaveta
    const modalTriggers = document.querySelectorAll(
        "#menu [data-bs-toggle='modal']"
    );
    modalTriggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
            closeMobileMenu();
        });
    });

    // Renderiza ícones Lucide
    if (typeof window.lucide !== "undefined") {
        window.lucide.createIcons();
    }
});

// Garante renderização dos ícones Lucide após carregamento dos scripts externos
window.addEventListener("load", () => {
    if (typeof window.lucide !== "undefined") {
        window.lucide.createIcons();
    }
});
