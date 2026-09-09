/**
 * Módulo de Máscaras de Entrada em Vanilla JavaScript Puro
 * Zero dependências externas (sem jQuery, sem plugins pesados).
 * Aplica formatação automática em tempo real para campos comuns brasileiros.
 */

export function inicializarMascaras() {
    /**
     * Formata CPF no padrão: 000.000.000-00
     */
    function formatarCPF(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 11);
        if (digitos.length <= 3) return digitos;
        if (digitos.length <= 6)
            return digitos.replace(/(\d{3})(\d+)/, "$1.$2");
        if (digitos.length <= 9)
            return digitos.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
        return digitos.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
    }

    /**
     * Formata CNPJ no padrão: 00.000.000/0000-00
     */
    function formatarCNPJ(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 14);
        if (digitos.length <= 2) return digitos;
        if (digitos.length <= 5)
            return digitos.replace(/(\d{2})(\d+)/, "$1.$2");
        if (digitos.length <= 8)
            return digitos.replace(/(\d{2})(\d{3})(\d+)/, "$1.$2.$3");
        if (digitos.length <= 12)
            return digitos.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, "$1.$2.$3/$4");
        return digitos.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,
            "$1.$2.$3/$4-$5",
        );
    }

    /**
     * Formata Telefone fixo ou Celular (10 ou 11 dígitos)
     * Padrões: (00) 0000-0000 ou (00) 00000-0000
     */
    function formatarTelefone(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 11);
        if (digitos.length === 0) return "";
        if (digitos.length <= 2) return `(${digitos}`;
        if (digitos.length <= 6)
            return digitos.replace(/(\d{2})(\d+)/, "($1) $2");
        if (digitos.length <= 10)
            return digitos.replace(/(\d{2})(\d{4})(\d+)/, "($1) $2-$3");
        return digitos.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
    }

    /**
     * Formata CEP no padrão: 00000-000
     */
    function formatarCEP(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 8);
        if (digitos.length <= 5) return digitos;
        return digitos.replace(/(\d{5})(\d{1,3})/, "$1-$2");
    }

    /**
     * Formata código ISBN no padrão: 978-XX-XXXX-XXX-X
     */
    function formatarISBN(valor) {
        let limpo = valor.replace(/[^0-9Xx]/g, "").slice(0, 13);
        if (limpo.length <= 3) return limpo;
        if (limpo.length <= 5) return limpo.replace(/(\d{3})(\d+)/, "$1-$2");
        if (limpo.length <= 8)
            return limpo.replace(/(\d{3})(\d{2})(\d+)/, "$1-$2-$3");
        if (limpo.length <= 12)
            return limpo.replace(/(\d{3})(\d{2})(\d{3,5})(\d+)/, "$1-$2-$3-$4");
        return limpo.replace(
            /(\d{3})(\d{2})(\d{3,5})(\d{1,4})([0-9Xx])/,
            "$1-$2-$3-$4-$5",
        );
    }

    /**
     * Formata Data no padrão brasileiro: DD/MM/AAAA
     */
    function formatarData(valor) {
        const digitos = valor.replace(/\D/g, "").slice(0, 8);
        if (digitos.length <= 2) return digitos;
        if (digitos.length <= 4)
            return digitos.replace(/(\d{2})(\d+)/, "$1/$2");
        return digitos.replace(/(\d{2})(\d{2})(\d{1,4})/, "$1/$2/$3");
    }

    /**
     * Formata Moeda em Reais no padrão: R$ 0,00
     */
    function formatarMoeda(valor) {
        const limpo = valor.replace(/\D/g, "");
        if (!limpo) return "";
        const valorFloat = parseFloat(limpo) / 100;
        return valorFloat.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
    }

    /**
     * Vincula a máscara ao input monitorando eventos de digitação e desfoque
     */
    function vincularMascara(seletor, formatador) {
        const inputs = document.querySelectorAll(seletor);
        inputs.forEach((input) => {
            if (input.dataset.mascaraVinculada) return;
            input.dataset.mascaraVinculada = "true";

            if (input.value) {
                input.value = formatador(input.value);
            }

            input.addEventListener("input", () => {
                input.value = formatador(input.value);
            });

            input.addEventListener("blur", () => {
                input.value = formatador(input.value);
            });
        });
    }

    // Vinculação de máscaras aos respectivos campos identificados
    vincularMascara(
        'input[data-mask="cpf"], input#cpf, input[name="cpf"]',
        formatarCPF,
    );
    vincularMascara(
        'input[data-mask="cnpj"], input#cnpj, input[name="cnpj"]',
        formatarCNPJ,
    );
    vincularMascara(
        'input[data-mask="telefone"], input#telefone, input[name="telefone"], input[name="celular_contato"], input[name="telefone_comercial"], input[name="telefone_urgencia"], input#telefone_comercial, input#telefone_urgencia',
        formatarTelefone,
    );
    vincularMascara(
        'input[data-mask="cep"], input#cep, input[name="cep"], [data-viacep="cep"]',
        formatarCEP,
    );
    vincularMascara(
        'input[data-mask="isbn"], input#isbn, input[name="isbn"]',
        formatarISBN,
    );
    vincularMascara(
        'input[data-mask="data"], input[data-mask="date"]',
        formatarData,
    );
    vincularMascara(
        'input[data-mask="money"], input.mask-money',
        formatarMoeda,
    );
}

// Alias em inglês para retrocompatibilidade
export const initMasks = inicializarMascaras;

// Disponibiliza globalmente para reexecução quando modais forem abertos
if (typeof window !== "undefined") {
    window.inicializarMascaras = inicializarMascaras;
    window.initMasks = inicializarMascaras;
}
