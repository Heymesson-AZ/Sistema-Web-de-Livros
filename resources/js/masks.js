// Máscaras de entrada em JavaScript Vanilla
export function initMasks() {
    function formatCPF(value) {
        const digits = value.replace(/\D/g, "").slice(0, 11);
        if (digits.length <= 3) return digits;
        if (digits.length <= 6) return digits.replace(/(\d{3})(\d+)/, "$1.$2");
        if (digits.length <= 9)
            return digits.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
        return digits.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
    }

    function formatCNPJ(value) {
        const digits = value.replace(/\D/g, "").slice(0, 14);
        if (digits.length <= 2) return digits;
        if (digits.length <= 5) return digits.replace(/(\d{2})(\d+)/, "$1.$2");
        if (digits.length <= 8)
            return digits.replace(/(\d{2})(\d{3})(\d+)/, "$1.$2.$3");
        if (digits.length <= 12)
            return digits.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, "$1.$2.$3/$4");
        return digits.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,
            "$1.$2.$3/$4-$5",
        );
    }

    function formatPhone(value) {
        const digits = value.replace(/\D/g, "").slice(0, 11);
        if (digits.length === 0) return "";
        if (digits.length <= 2) return `(${digits}`;
        if (digits.length <= 6)
            return digits.replace(/(\d{2})(\d+)/, "($1) $2");
        if (digits.length <= 10)
            return digits.replace(/(\d{2})(\d{4})(\d+)/, "($1) $2-$3");
        return digits.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
    }

    function formatCEP(value) {
        const digits = value.replace(/\D/g, "").slice(0, 8);
        if (digits.length <= 5) return digits;
        return digits.replace(/(\d{5})(\d{1,3})/, "$1-$2");
    }

    function formatISBN(value) {
        // Suporte a ISBN-10 e ISBN-13 (ex: 978-65-88888-00-1 ou 978-85-359-0277-8)
        let cleaned = value.replace(/[^0-9Xx]/g, "").slice(0, 13);
        if (cleaned.length <= 3) return cleaned;
        if (cleaned.length <= 5)
            return cleaned.replace(/(\d{3})(\d+)/, "$1-$2");
        if (cleaned.length <= 8)
            return cleaned.replace(/(\d{3})(\d{2})(\d+)/, "$1-$2-$3");
        if (cleaned.length <= 12)
            return cleaned.replace(
                /(\d{3})(\d{2})(\d{3,5})(\d+)/,
                "$1-$2-$3-$4",
            );
        return cleaned.replace(
            /(\d{3})(\d{2})(\d{3,5})(\d{1,4})([0-9Xx])/,
            "$1-$2-$3-$4-$5",
        );
    }

    function formatDate(value) {
        const digits = value.replace(/\D/g, "").slice(0, 8);
        if (digits.length <= 2) return digits;
        if (digits.length <= 4) return digits.replace(/(\d{2})(\d+)/, "$1/$2");
        return digits.replace(/(\d{2})(\d{2})(\d{1,4})/, "$1/$2/$3");
    }

    function formatMoney(value) {
        const clean = value.replace(/\D/g, "");
        if (!clean) return "";
        const floatVal = parseFloat(clean) / 100;
        return floatVal.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
    }

    function bindMask(selector, formatter) {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach((input) => {
            if (input.dataset.maskBound) return;
            input.dataset.maskBound = "true";

            if (input.value) {
                input.value = formatter(input.value);
            }

            input.addEventListener("input", () => {
                input.value = formatter(input.value);
            });

            input.addEventListener("blur", () => {
                input.value = formatter(input.value);
            });
        });
    }

    // 1. CPF
    bindMask('input[data-mask="cpf"], input#cpf, input[name="cpf"]', formatCPF);

    // 2. CNPJ
    bindMask(
        'input[data-mask="cnpj"], input#cnpj, input[name="cnpj"]',
        formatCNPJ,
    );

    // 3. Telefone / Celular (fixo 10 dígitos ou celular 11 dígitos)
    bindMask(
        'input[data-mask="telefone"], input#telefone, input[name="telefone"], input[name="celular_contato"], input[name="telefone_comercial"], input[name="telefone_urgencia"], input#telefone_comercial, input#telefone_urgencia',
        formatPhone,
    );

    // 4. CEP
    bindMask('input[data-mask="cep"], input#cep, input[name="cep"]', formatCEP);

    // 5. ISBN
    bindMask(
        'input[data-mask="isbn"], input#isbn, input[name="isbn"]',
        formatISBN,
    );

    // 6. Data (DD/MM/AAAA)
    bindMask('input[data-mask="data"], input[data-mask="date"]', formatDate);

    // 7. Moeda / Preço (R$ 0,00)
    bindMask('input[data-mask="money"], input.mask-money', formatMoney);
}

// Expõe globalmente no window para re-inicialização em modais dinâmicos
if (typeof window !== "undefined") {
    window.initMasks = initMasks;
}
