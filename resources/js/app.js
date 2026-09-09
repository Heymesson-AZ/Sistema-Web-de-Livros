import "./bootstrap"; // Importações padrão do Laravel

import "./menu";
import "./carrosel";
import { initMasks } from "./masks";
import { initRealtimeValidation } from "./validation";
import { initBuscaDinamica } from "./busca-dinamica";

document.addEventListener("DOMContentLoaded", function () {
    // Inicializa máscaras completas
    initMasks();
    // Inicializa busca dinâmica com debounce via fetch
    initBuscaDinamica();
    // Inicializa validações em tempo real
    initRealtimeValidation();

    // Re-aplica máscaras ao abrir modais Bootstrap
    document.addEventListener("shown.bs.modal", function () {
        initMasks();
    });
    const togglePassword = document.querySelector("#togglePassword");
    const passwordInput = document.querySelector("#password");
    const toggleIcon = document.querySelector("#toggleIcon");

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            // Alterna o tipo do input entre 'password' e 'text'
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);

            // Alterna o ícone de olho aberto / olho cortado
            toggleIcon.classList.toggle("bi-eye");
            toggleIcon.classList.toggle("bi-eye-slash");
        });
    }
});

// Pré-visualização instantânea de upload de imagens (avatar/foto)
window.previewImage = function (input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const el = document.getElementById(previewId);
            if (el) {
                el.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
};
