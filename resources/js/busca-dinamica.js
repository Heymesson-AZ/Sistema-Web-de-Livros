// Busca Dinâmica com Debounce via Fetch em Vanilla JS
export function initBuscaDinamica() {
    const searchInputs = document.querySelectorAll(
        '.search-box input[name="busca"], input[data-dynamic-search]',
    );

    searchInputs.forEach((input) => {
        let debounceTimer = null;
        const container = input.closest(".search-box") || input.parentElement;

        if (!container) return;

        // Garante posicionamento relativo do container
        if (window.getComputedStyle(container).position === "static") {
            container.style.position = "relative";
        }

        // Cria elemento de dropdown de resultados
        let dropdown = container.querySelector(".search-suggestions-dropdown");
        if (!dropdown) {
            dropdown = document.createElement("div");
            dropdown.className = "search-suggestions-dropdown shadow-lg";
            dropdown.style.cssText = `
                display: none;
                position: absolute;
                top: calc(100% + 6px);
                left: 0;
                right: 0;
                background: #ffffff;
                border-radius: 12px;
                border: 1px solid rgba(0,0,0,0.08);
                box-shadow: 0 12px 28px rgba(0,0,0,0.14);
                z-index: 1050;
                max-height: 380px;
                overflow-y: auto;
                padding: 6px 0;
            `;
            container.appendChild(dropdown);
        }

        let activeIndex = -1;

        function closeDropdown() {
            dropdown.style.display = "none";
            dropdown.innerHTML = "";
            activeIndex = -1;
        }

        function renderResults(livros, termo) {
            if (!livros || livros.length === 0) {
                dropdown.innerHTML = `
                    <div class="px-3 py-3 text-center text-muted small">
                        <i class="bi bi-search me-1"></i> Nenhum livro encontrado para "<strong>${escapeHtml(termo)}</strong>"
                    </div>
                `;
                dropdown.style.display = "block";
                return;
            }

            let html = `
                <div class="px-3 py-2 text-uppercase text-secondary fw-bold border-bottom" style="font-size: 10.5px; letter-spacing: 0.5px;">
                    Livros Sugeridos
                </div>
            `;

            livros.forEach((livro, idx) => {
                html += `
                    <a href="${livro.url}" class="suggestion-item d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-dark border-bottom border-light" data-index="${idx}" style="transition: background 0.15s ease;">
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
            activeIndex = -1;

            // Hover styling
            const items = dropdown.querySelectorAll(".suggestion-item");
            items.forEach((item) => {
                item.addEventListener("mouseenter", () => {
                    items.forEach(
                        (it) => (it.style.backgroundColor = "transparent"),
                    );
                    item.style.backgroundColor = "#f1f5f9";
                });
                item.addEventListener("mouseleave", () => {
                    item.style.backgroundColor = "transparent";
                });
            });
        }

        input.addEventListener("input", function () {
            const termo = input.value.trim();

            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            if (termo.length < 2) {
                closeDropdown();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/busca-rapida?q=${encodeURIComponent(termo)}`, {
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
                        renderResults(data, termo);
                    })
                    .catch((err) => {
                        console.error("Erro busca dinâmica:", err);
                        closeDropdown();
                    });
            }, 300); // 300ms debounce
        });

        // Navegação por teclado
        input.addEventListener("keydown", function (e) {
            const items = dropdown.querySelectorAll(".suggestion-item");
            if (
                !items ||
                items.length === 0 ||
                dropdown.style.display === "none"
            ) {
                if (e.key === "Escape") closeDropdown();
                return;
            }

            if (e.key === "ArrowDown") {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveItem(items);
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveItem(items);
            } else if (e.key === "Enter" && activeIndex >= 0) {
                e.preventDefault();
                items[activeIndex].click();
            } else if (e.key === "Escape") {
                closeDropdown();
            }
        });

        function updateActiveItem(items) {
            items.forEach((item, idx) => {
                if (idx === activeIndex) {
                    item.style.backgroundColor = "#e2e8f0";
                    item.scrollIntoView({ block: "nearest" });
                } else {
                    item.style.backgroundColor = "transparent";
                }
            });
        }

        // Fechar ao clicar fora
        document.addEventListener("click", function (e) {
            if (!container.contains(e.target)) {
                closeDropdown();
            }
        });
    });

    function escapeHtml(text) {
        if (!text) return "";
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}
