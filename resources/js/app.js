/**
 * Ponto de Entrada Principal do Front-end (Vanilla JavaScript)
 * 
 * Centraliza a inicialização de:
 * 1. Máscaras de formulários brasileiros (CPF, CNPJ, Telefone, CEP, etc.)
 * 2. Busca dinâmica inteligente com debounce e autocompletar via fetch
 * 3. Validações interativas em tempo real
 * 4. Integração assíncrona com API ViaCEP
 * 5. Alternador universal de visibilidade de senha (ícone de olho)
 * 6. Pré-visualização e validação de tamanho de foto de perfil
 */

import "./bootstrap";
import "./menu";
import "./carrosel";
import { inicializarMascaras } from "./masks";
import { initRealtimeValidation as inicializarValidacaoEmTempoReal } from "./validation";
import { initBuscaDinamica as inicializarBuscaDinamica } from "./busca-dinamica";
import { inicializarBuscaCep } from "./viacep";

/**
 * Configura o alternador de visualização de senha para todos os campos
 * Suporta modais de login, registro, redefinição e formulários do painel.
 */
function configurarAlternadoresDeSenha() {
    document.addEventListener("click", function (evento) {
        const botao = evento.target.closest("[data-toggle='password'], #togglePassword, .toggle-password-btn");
        if (!botao) return;

        evento.preventDefault();

        // Identifica o campo de senha correspondente
        const seletorAlvo = botao.getAttribute("data-target");
        let campoSenha = seletorAlvo ? document.querySelector(seletorAlvo) : null;
        if (!campoSenha) {
            const grupoPai = botao.closest(".input-group");
            campoSenha = grupoPai ? grupoPai.querySelector("input") : null;
        }

        if (campoSenha) {
            const ehTipoSenha = campoSenha.getAttribute("type") === "password";
            campoSenha.setAttribute("type", ehTipoSenha ? "text" : "password");

            // Alterna o ícone entre bi-eye e bi-eye-slash
            const icone = botao.querySelector("i") || botao;
            if (icone) {
                if (ehTipoSenha) {
                    icone.classList.remove("bi-eye");
                    icone.classList.add("bi-eye-slash");
                } else {
                    icone.classList.remove("bi-eye-slash");
                    icone.classList.add("bi-eye");
                }
            }
        }
    });
}

/**
 * Pré-visualização instantânea e validação de tamanho para uploads de imagens
 * Impede envio de arquivos maiores que o limite permitido (ex: 2MB).
 */
window.previsualizarImagem = function (campoInput, idPrevisualizacao, limiteMegabytes = 2) {
    if (campoInput.files && campoInput.files[0]) {
        const arquivo = campoInput.files[0];
        const tamanhoMegabytes = arquivo.size / (1024 * 1024);

        if (tamanhoMegabytes > limiteMegabytes) {
            alert(`A foto selecionada possui ${tamanhoMegabytes.toFixed(1)}MB e ultrapassa o limite recomendado de ${limiteMegabytes}MB. Por favor, selecione uma imagem menor.`);
            campoInput.value = "";
            return;
        }

        const leitor = new FileReader();
        leitor.onload = function (e) {
            const imagemElemento = document.getElementById(idPrevisualizacao);
            if (imagemElemento) {
                imagemElemento.src = e.target.result;
            }
        };
        leitor.readAsDataURL(arquivo);
    }
};

// Alias de retrocompatibilidade
window.previewImage = window.previsualizarImagem;

// Inicialização de todos os módulos quando o DOM estiver pronto
document.addEventListener("DOMContentLoaded", function () {
    // 1. Inicializa máscaras em todos os campos
    inicializarMascaras();

    // 2. Inicializa busca rápida com debounce e autocompletar
    inicializarBuscaDinamica();

    // 3. Inicializa validações em tempo real nos formulários
    inicializarValidacaoEmTempoReal();

    // 4. Inicializa integração automática com a API ViaCEP
    inicializarBuscaCep();

    // 5. Configura botões de mostrar/ocultar senha
    configurarAlternadoresDeSenha();

    // 6. Re-executa as máscaras e ViaCEP ao exibir qualquer modal do Bootstrap
    document.addEventListener("shown.bs.modal", function () {
        inicializarMascaras();
        inicializarBuscaCep();
    });
});
