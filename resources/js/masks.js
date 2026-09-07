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

    // Aplica máscara de CPF
    const cpfInputs = document.querySelectorAll(
        'input[data-mask="cpf"], input#cpf, input[name="cpf"]',
    );
    cpfInputs.forEach((input) => {
        if (input.value) {
            input.value = formatCPF(input.value);
        }

        input.addEventListener("input", () => {
            input.value = formatCPF(input.value);
        });

        input.addEventListener("blur", () => {
            input.value = formatCPF(input.value);
        });
    });

    // Aplica máscara de Telefone/Celular (dinâmica para 10 ou 11 dígitos)
    const phoneInputs = document.querySelectorAll(
        'input[data-mask="telefone"], input#telefone, input[name="telefone"], input[name="celular_contato"]',
    );
    phoneInputs.forEach((input) => {
        if (input.value) {
            input.value = formatPhone(input.value);
        }

        input.addEventListener("input", () => {
            input.value = formatPhone(input.value);
        });

        input.addEventListener("blur", () => {
            input.value = formatPhone(input.value);
        });
    });
}
