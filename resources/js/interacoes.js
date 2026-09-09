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
        const limiteMegabytes = parseFloat(input.getAttribute("data-max-size") || "2");

        if (input.files && input.files[0]) {
            const arquivo = input.files[0];
            const tamanhoMegabytes = arquivo.size / (1024 * 1024);

            if (tamanhoMegabytes > limiteMegabytes) {
                alert(
                    `O arquivo selecionado possui ${tamanhoMegabytes.toFixed(1)}MB e excede o limite máximo permitido de ${limiteMegabytes}MB. Por favor, selecione uma imagem menor.`
                );
                input.value = "";
                return;
            }

            const leitor = new FileReader();
            leitor.onload = function (e) {
                const elementoAlvo = document.getElementById(seletorAlvo) || document.querySelector(seletorAlvo);
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
    const nomeModal = document.body ? document.body.getAttribute("data-auth-modal") : null;
    if (!nomeModal) return;

    const elementoModal = document.getElementById(nomeModal);
    if (elementoModal && typeof window.bootstrap !== "undefined" && window.bootstrap.Modal) {
        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(elementoModal);
        modalInstance.show();
    }
}

/**
 * Inicializador principal de todas as interações declarativas
 */
export function inicializarInteracoesDeclarativas() {
    configurarPreviaImagens();
    configurarConfirmacoes();
    configurarNavegacaoSelect();
    configurarDisparoDeAbas();
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

