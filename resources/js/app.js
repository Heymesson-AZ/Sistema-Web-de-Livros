import "./bootstrap"; // Importações padrão do Laravel

import "./menu";
import "./carrosel";
import { initMasks } from "./masks";
import { initRealtimeValidation } from "./validation";

document.addEventListener("DOMContentLoaded", function () {
    // Inicializa máscaras de CPF e Telefone
    initMasks();
    // Inicializa validações em tempo real
    initRealtimeValidation();
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
