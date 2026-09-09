/**
 * Módulo de Integração com a API ViaCEP (Vanilla JavaScript)
 * 
 * Funcionalidade:
 * - Realiza consulta assíncrona automática ao digitar o CEP (8 dígitos).
 * - Preenche automaticamente os campos de Rua, Bairro, Cidade e Estado.
 * - Direciona o cursor (foco) diretamente para o campo "Número" após o preenchimento.
 * - Trata de forma amigável cenários de erro (CEP inexistente ou falha de conexão).
 */

export function inicializarBuscaCep() {
    // Localiza todos os formulários ou contêineres que possuem integração com o ViaCEP
    const conteineres = document.querySelectorAll("[data-viacep-container]");

    conteineres.forEach((conteiner) => {
        const campoCep = conteiner.querySelector("[data-viacep='cep']");
        const campoRua = conteiner.querySelector("[data-viacep='rua']");
        const campoBairro = conteiner.querySelector("[data-viacep='bairro']");
        const campoCidade = conteiner.querySelector("[data-viacep='cidade']");
        const campoEstado = conteiner.querySelector("[data-viacep='estado']");
        const campoNumero = conteiner.querySelector("[data-viacep='numero']");
        const elementoMensagem = conteiner.querySelector("[data-viacep='mensagem']");

        if (!campoCep) return;

        // Função para exibir mensagem de status ou erro
        const exibirMensagem = (texto, tipo = "danger") => {
            if (elementoMensagem) {
                elementoMensagem.textContent = texto;
                elementoMensagem.className = `small text-${tipo} mt-1 d-block fw-semibold`;
            }
        };

        // Função para limpar mensagens
        const limparMensagem = () => {
            if (elementoMensagem) {
                elementoMensagem.textContent = "";
                elementoMensagem.className = "d-none";
            }
        };

        // Variável de controle para abortar requisições pendentes se o usuário alterar o CEP rapidamente
        let controladorRequisicao = null;

        campoCep.addEventListener("input", async () => {
            limparMensagem();

            // Remove qualquer caractere que não seja número
            const cepLimpo = campoCep.value.replace(/\D/g, "");

            // Se o CEP atingir exatamente 8 dígitos, dispara a consulta automática
            if (cepLimpo.length === 8) {
                exibirMensagem("Buscando endereço pelo CEP...", "info");

                // Cancela requisição anterior se houver
                if (controladorRequisicao) {
                    controladorRequisicao.abort();
                }
                controladorRequisicao = new AbortController();

                try {
                    const resposta = await fetch(
                        `https://viacep.com.br/ws/${cepLimpo}/json/`,
                        { signal: controladorRequisicao.signal }
                    );

                    if (!resposta.ok) {
                        throw new Error("Erro na comunicação com o serviço ViaCEP");
                    }

                    const dados = await resposta.json();

                    // O serviço ViaCEP retorna { erro: "true" } quando o CEP não é localizado
                    if (dados.erro) {
                        exibirMensagem("CEP não encontrado. Por favor, digite os dados manualmente.", "warning");
                        return;
                    }

                    // Preenche os campos com os dados retornados
                    if (campoRua && dados.logradouro) campoRua.value = dados.logradouro;
                    if (campoBairro && dados.bairro) campoBairro.value = dados.bairro;
                    if (campoCidade && dados.localidade) campoCidade.value = dados.localidade;
                    if (campoEstado && dados.uf) campoEstado.value = dados.uf.toUpperCase();

                    limparMensagem();

                    // Foca imediatamente no campo de número para agilizar a digitação
                    if (campoNumero) {
                        campoNumero.focus();
                    }
                } catch (erro) {
                    if (erro.name === "AbortError") return;
                    exibirMensagem("Não foi possível consultar o CEP automaticamente. Digite os dados manualmente.", "secondary");
                }
            }
        });
    });
}
