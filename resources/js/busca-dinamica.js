/**
 * Busca Dinâmica com Delegação de Eventos e Debounce Delay
 *
 * Características:
 * - Delegação de eventos no document (input, keydown, click) sem necessidade de rebind
 * - Debounce delay calibrado (350ms)
 * - Cancelamento automático de requisições anteriores em voo via AbortController
 * - Navegação via teclado acessível (Setas Cima/Baixo, Enter, Escape)
 * - Suporte a múltiplos campos de busca (header, página inicial, formulários de gestão)
 */

let debounceTimer = null;
let currentAbortController = null;
let activeIndex = -1;

const SEARCH_SELECTOR =
    '.search-box input[name="busca"], input[data-dynamic-search]';

/**
 * Obtém ou cria o container de dropdown de sugestões associado a um input
 */
function getOrCreateDropdown(input) {
    const container =
        input.closest(".search-box") ||
        input.closest(".input-group") ||
        input.parentElement;
    if (!container) return null;

    if (window.getComputedStyle(container).position === "static") {
        container.style.position = "relative";
    }

    let dropdown = container.querySelector(".search-suggestions-dropdown");
    if (!dropdown) {
        dropdown = document.createElement("div");
        dropdown.className = "search-suggestions-dropdown shadow-lg";
        dropdown.setAttribute("role", "listbox");
        dropdown.style.cssText = `
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.16);
            z-index: 1060;
            max-height: 380px;
            overflow-y: auto;
            padding: 6px 0;
        `;
        container.appendChild(dropdown);
    }

    return dropdown;
}

/**
 * Fecha e limpa o dropdown ativo
 */
function closeDropdown(dropdown, input) {
    if (dropdown) {
        dropdown.style.display = "none";
        dropdown.innerHTML = "";
    }
    if (input) {
        input.setAttribute("aria-expanded", "false");
    }
    activeIndex = -1;
}

/**
 * Fecha todos os dropdowns abertos no documento
 */
function closeAllDropdowns() {
    document.querySelectorAll(".search-suggestions-dropdown").forEach((dd) => {
        dd.style.display = "none";
        dd.innerHTML = "";
    });
    document.querySelectorAll(SEARCH_SELECTOR).forEach((input) => {
        input.setAttribute("aria-expanded", "false");
    });
    activeIndex = -1;
}

/**
 * Renderiza os resultados da busca no dropdown
 */
function renderResults(dropdown, input, livros, termo) {
    if (!dropdown) return;

    if (!livros || livros.length === 0) {
        dropdown.innerHTML = `
            <div class="px-3 py-3 text-center text-muted small">
                <i class="bi bi-search me-1"></i> Nenhum livro encontrado para "<strong>${escapeHtml(termo)}</strong>"
            </div>
        `;
        dropdown.style.display = "block";
        input.setAttribute("aria-expanded", "true");
        return;
    }

    let html = `
        <div class="px-3 py-2 text-uppercase text-secondary fw-bold border-bottom" style="font-size: 10.5px; letter-spacing: 0.5px;">
            Livros Sugeridos
        </div>
    `;

    livros.forEach((livro, idx) => {
        html += `
            <a href="${livro.url}" class="suggestion-item d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-dark border-bottom border-light" data-index="${idx}" role="option" style="transition: background 0.15s ease;">
                <img src="${livro.capa_url}" alt="${escapeHtml(livro.titulo)}"
                     style="width: 38px; height: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex-shrink: 0;"
                     onerror="this.src='https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=150&q=80'">
                <div class="overflow-hidden flex-grow-1" style="min-width: 0;">
                    <div class="fw-semibold text-truncate small text-dark mb-0" title="${escapeHtml(livro.titulo)}">${escapeHtml(livro.titulo)}</div>
                    <div class="text-muted small text-truncate" style="font-size: 11.5px;">${escapeHtml(livro.autor)}</div>
                </div>
                <div class="text-end flex-shrink-0">
                    <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 11.5px;">${livro.preco_formatado}</span>
                </div>
            </a>
        `;
    });

    html += `
        <div class="p-2 text-center bg-light">
            <a href="/?busca=${encodeURIComponent(termo)}#catalogo" class="small text-primary text-decoration-none fw-semibold d-block">
                Ver todos os resultados no catálogo &rarr;
            </a>
        </div>
    `;

    dropdown.innerHTML = html;
    dropdown.style.display = "block";
    input.setAttribute("aria-expanded", "true");
    activeIndex = -1;
}

/**
 * Atualiza o item visualmente ativo na lista de sugestões
 */
function updateActiveItem(dropdown) {
    const items = dropdown.querySelectorAll(".suggestion-item");
    items.forEach((item, idx) => {
        if (idx === activeIndex) {
            item.style.backgroundColor = "#e2e8f0";
            item.setAttribute("aria-selected", "true");
            item.scrollIntoView({ block: "nearest" });
        } else {
            item.style.backgroundColor = "transparent";
            item.removeAttribute("aria-selected");
        }
    });
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Inicializador da Busca Dinâmica com Delegação Global no document
 */
export function initBuscaDinamica() {
    // 1. Delegação do Evento INPUT com Debounce Delay de 350ms e AbortController
    document.addEventListener("input", function (event) {
        const input = event.target.closest(SEARCH_SELECTOR);
        if (!input) return;

        const dropdown = getOrCreateDropdown(input);
        if (!dropdown) return;

        const termo = input.value.trim();

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        if (currentAbortController) {
            currentAbortController.abort();
            currentAbortController = null;
        }

        if (termo.length < 2) {
            closeDropdown(dropdown, input);
            return;
        }

        debounceTimer = setTimeout(() => {
            currentAbortController = new AbortController();
            const { signal } = currentAbortController;

            fetch(`/busca-rapida?q=${encodeURIComponent(termo)}`, {
                signal,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => {
                    if (!response.ok) throw new Error("Erro na busca");
                    return response.json();
                })
                .then((data) => {
                    renderResults(dropdown, input, data, termo);
                })
                .catch((err) => {
                    if (err.name === "AbortError") return;
                    console.error("Erro na busca dinâmica:", err);
                    closeDropdown(dropdown, input);
                });
        }, 350); // Debounce delay 350ms
    });

    // 2. Delegação do Evento KEYDOWN para navegação por teclado acessível
    document.addEventListener("keydown", function (event) {
        const input = event.target.closest(SEARCH_SELECTOR);
        if (!input) return;

        const dropdown = getOrCreateDropdown(input);
        if (!dropdown || dropdown.style.display === "none") return;

        const items = dropdown.querySelectorAll(".suggestion-item");
        if (!items || items.length === 0) {
            if (event.key === "Escape") closeDropdown(dropdown, input);
            return;
        }

        if (event.key === "ArrowDown") {
            event.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            updateActiveItem(dropdown);
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            updateActiveItem(dropdown);
        } else if (event.key === "Enter" && activeIndex >= 0) {
            event.preventDefault();
            items[activeIndex].click();
        } else if (event.key === "Escape") {
            closeDropdown(dropdown, input);
        }
    });

    // 3. Delegação de Hover sobre itens de sugestão
    document.addEventListener("mouseover", function (event) {
        const item = event.target.closest(".suggestion-item");
        if (!item) return;

        const dropdown = item.closest(".search-suggestions-dropdown");
        if (!dropdown) return;

        dropdown.querySelectorAll(".suggestion-item").forEach((it) => {
            it.style.backgroundColor = "transparent";
        });
        item.style.backgroundColor = "#f1f5f9";
    });

    // 4. Delegação de Clique Fora para fechar os dropdowns abertos
    document.addEventListener("click", function (event) {
        const isInsideSearch =
            event.target.closest(SEARCH_SELECTOR) ||
            event.target.closest(".search-suggestions-dropdown");
        if (!isInsideSearch) {
            closeAllDropdowns();
        }
    });
}
