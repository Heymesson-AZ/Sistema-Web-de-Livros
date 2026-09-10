/**
 * Módulo de Interações e Comportamentos Declarativos (Vanilla JavaScript)
 *
 * Elimina completamente scripts e estilos embutidos em arquivos Blade,
 * fornecendo manipuladores globais baseados em atributos HTML (data-*):
 *
 * 1. data-preview-target: Pré-visualização de imagem com validação de limite (2MB)
 * 2. data-confirm: Exibição de confirmação antes de submeter ou clicar em ação crítica
 * 3. data-navigate-on-change: Redirecionamento dinâmico ao alterar valor de <select>
 * 4. data-trigger-tab: Ativação programática de abas Bootstrap
 * 5. data-auth-modal no <body>: Abertura automática de modais em caso de erro ou sessão
 * 6. Inicialização segura de ícones Lucide
 */

/**
 * Inicializa a renderização de ícones Lucide se a biblioteca estiver presente
 */
export function inicializarIconesLucide() {
    if (typeof window.lucide !== "undefined" && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
}

/**
 * Manipulador global de pré-visualização de arquivos de imagem
 */
function configurarPreviaImagens() {
    document.addEventListener("change", function (evento) {
        const input = evento.target;
        if (!input || !input.matches("[data-preview-target]")) return;

        const seletorAlvo = input.getAttribute("data-preview-target");
        const limiteMegabytes = parseFloat(
            input.getAttribute("data-max-size") || "2",
        );

        if (input.files && input.files[0]) {
            const arquivo = input.files[0];
            const tamanhoMegabytes = arquivo.size / (1024 * 1024);

            if (tamanhoMegabytes > limiteMegabytes) {
                alert(
                    `O arquivo selecionado possui ${tamanhoMegabytes.toFixed(1)}MB e excede o limite máximo permitido de ${limiteMegabytes}MB. Por favor, selecione uma imagem menor.`,
                );
                input.value = "";
                return;
            }

            const leitor = new FileReader();
            leitor.onload = function (e) {
                const elementoAlvo =
                    document.getElementById(seletorAlvo) ||
                    document.querySelector(seletorAlvo);
                if (elementoAlvo) {
                    elementoAlvo.src = e.target.result;
                }
            };
            leitor.readAsDataURL(arquivo);
        }
    });
}

/**
 * Manipulador global de confirmação de ações críticas (data-confirm)
 */
function configurarConfirmacoes() {
    // Para cliques em botões ou links
    document.addEventListener("click", function (evento) {
        const elemento = evento.target.closest("[data-confirm]:not(form)");
        if (!elemento) return;

        const mensagem = elemento.getAttribute("data-confirm");
        if (mensagem && !window.confirm(mensagem)) {
            evento.preventDefault();
            evento.stopImmediatePropagation();
        }
    });

    // Para submissões de formulário
    document.addEventListener("submit", function (evento) {
        const formulario = evento.target;
        if (!formulario || !formulario.matches("[data-confirm]")) return;

        const mensagem = formulario.getAttribute("data-confirm");
        if (mensagem && !window.confirm(mensagem)) {
            evento.preventDefault();
            evento.stopImmediatePropagation();
        }
    });
}

/**
 * Manipulador global para selects que realizam navegação instantânea
 */
function configurarNavegacaoSelect() {
    document.addEventListener("change", function (evento) {
        const select = evento.target;
        if (!select || !select.matches("[data-navigate-on-change]")) return;

        if (select.value) {
            window.location.href = select.value;
        }
    });
}

/**
 * Manipulador global para disparo de abas Bootstrap via data-trigger-tab
 */
function configurarDisparoDeAbas() {
    document.addEventListener("click", function (evento) {
        const gatilho = evento.target.closest("[data-trigger-tab]");
        if (!gatilho) return;

        evento.preventDefault();
        const seletorAba = gatilho.getAttribute("data-trigger-tab");
        const elementoAba = document.querySelector(seletorAba);
        if (elementoAba) {
            elementoAba.click();
        }
    });
}

/**
 * Verifica se há solicitação declarativa no <body> para abertura de modal de autenticação
 */
function verificarAberturaAutomaticaDeModais() {
    const nomeModal = document.body
        ? document.body.getAttribute("data-auth-modal")
        : null;
    if (!nomeModal) return;

    const elementoModal = document.getElementById(nomeModal);
    if (
        elementoModal &&
        typeof window.bootstrap !== "undefined" &&
        window.bootstrap.Modal
    ) {
        const modalInstance =
            window.bootstrap.Modal.getOrCreateInstance(elementoModal);
        modalInstance.show();
    }
}

/**
 * Copiar dados para a área de transferência (PIX, código de barras, etc)
 */
function configurarClipboard() {
    document.addEventListener("click", async function (evento) {
        const botao = evento.target.closest(
            "[data-clipboard-target], [data-clipboard-text]",
        );
        if (!botao) return;

        let textoCopiar = botao.getAttribute("data-clipboard-text");
        if (!textoCopiar) {
            const seletor = botao.getAttribute("data-clipboard-target");
            const elementoAlvo = document.querySelector(seletor);
            if (elementoAlvo) {
                textoCopiar =
                    elementoAlvo.value || elementoAlvo.textContent || "";
            }
        }

        if (!textoCopiar) return;

        try {
            await navigator.clipboard.writeText(textoCopiar.trim());
            const conteudoOriginal = botao.innerHTML;
            botao.innerHTML = '<i class="bi bi-check-lg me-1"></i> Copiado!';
            botao.classList.add("btn-success");
            setTimeout(() => {
                botao.innerHTML = conteudoOriginal;
                botao.classList.remove("btn-success");
            }, 2000);
        } catch (erro) {
            console.error(
                "Falha ao copiar para a área de transferência:",
                erro,
            );
        }
    });
}

/**
 * Preenche o modal de edição de endereço dinamicamente
 */
function configurarEdicaoEndereco() {
    document.addEventListener("click", function (evento) {
        const botao = evento.target.closest("[data-editar-endereco]");
        if (!botao) return;

        const form = document.getElementById("formEditarEndereco");
        if (!form) return;

        const action = botao.getAttribute("data-endereco-action");
        if (action) {
            form.action = action;
        }

        const campos = {
            tipo: botao.getAttribute("data-endereco-tipo"),
            cep: botao.getAttribute("data-endereco-cep"),
            rua: botao.getAttribute("data-endereco-rua"),
            numero: botao.getAttribute("data-endereco-numero"),
            bairro: botao.getAttribute("data-endereco-bairro"),
            cidade: botao.getAttribute("data-endereco-cidade"),
            estado: botao.getAttribute("data-endereco-estado"),
            complemento: botao.getAttribute("data-endereco-complemento"),
        };

        for (const [chave, valor] of Object.entries(campos)) {
            const input = form.querySelector(`[name="${chave}"]`);
            if (input) {
                input.value = valor || "";
            }
        }

        const inputPrincipal = form.querySelector('[name="principal"]');
        if (inputPrincipal) {
            inputPrincipal.checked =
                botao.getAttribute("data-endereco-principal") === "1";
        }
    });
}

/**
 * Alterna status de favorito assincronamente via fetch
 */
function configurarFavoritosToggle() {
    document.addEventListener("click", async function (evento) {
        const botao = evento.target.closest("[data-favorito-toggle]");
        if (!botao) return;

        evento.preventDefault();
        evento.stopPropagation();

        const url =
            botao.getAttribute("data-favorito-url") || botao.form?.action;
        if (!url) return;

        const tokenCsrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        // Desabilita temporariamente para evitar cliques duplos
        botao.style.pointerEvents = "none";
        const icone = botao.querySelector("i");
        if (icone) {
            icone.style.transition =
                "transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)";
            icone.style.transform = "scale(1.3)";
        }

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);

            const resposta = await fetch(url, {
                method: "POST",
                signal: controller.signal,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": tokenCsrf || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            clearTimeout(timeoutId);

            if (resposta.status === 401) {
                // Usuário não autenticado: abre modal de login se existir na página
                const modalLoginEl = document.getElementById("loginModal");
                if (modalLoginEl && window.bootstrap?.Modal) {
                    const modalInstance =
                        window.bootstrap.Modal.getOrCreateInstance(
                            modalLoginEl,
                        );
                    modalInstance.show();
                    return;
                }
                window.location.href = "/entrar";
                return;
            }

            if (resposta.status === 419) {
                alert("Sua sessão expirou. Recarregando a página...");
                window.location.reload();
                return;
            }

            if (!resposta.ok)
                throw new Error("Erro na requisição ao favoritar");

            const dados = await resposta.json();

            if (dados.favoritado) {
                if (icone) {
                    icone.className = "bi bi-heart-fill text-danger fs-6";
                }
                botao.title = "Remover dos favoritos";
            } else {
                if (icone) {
                    icone.className = "bi bi-heart text-secondary fs-6";
                }
                botao.title = "Adicionar aos favoritos";

                // Se estiver na aba de favoritos do painel, remove o card com transição suave
                const cardFavorito = botao.closest("[data-favorito-item]");
                if (cardFavorito) {
                    cardFavorito.style.transition = "all 0.3s ease";
                    cardFavorito.style.opacity = "0";
                    cardFavorito.style.transform = "scale(0.95)";
                    setTimeout(() => cardFavorito.remove(), 300);
                }
            }

            // Atualiza contadores de favoritos na navbar e menu se existirem
            const badges = document.querySelectorAll(
                "[data-contador-favoritos], .badge-count-favoritos",
            );
            badges.forEach((badge) => {
                badge.textContent = dados.total_favoritos;
            });
        } catch (erro) {
            console.error("Erro ao favoritar:", erro);
        } finally {
            botao.style.pointerEvents = "auto";
            if (icone) {
                setTimeout(() => {
                    icone.style.transform = "scale(1)";
                }, 200);
            }
        }
    });
}

/**
 * Inicializador principal de todas as interações declarativas
 */
export function inicializarInteracoesDeclarativas() {
    configurarPreviaImagens();
    configurarConfirmacoes();
    configurarNavegacaoSelect();
    configurarDisparoDeAbas();
    configurarClipboard();
    configurarEdicaoEndereco();
    configurarFavoritosToggle();
    inicializarIconesLucide();
    verificarAberturaAutomaticaDeModais();

    // Re-executa inicialização de ícones quando modais forem exibidos
    document.addEventListener("shown.bs.modal", function () {
        inicializarIconesLucide();
    });

    // Re-executa inicialização de ícones quando dropdowns forem abertos
    document.addEventListener("shown.bs.dropdown", function () {
        inicializarIconesLucide();
    });
}
