import './bootstrap'; // Importações padrão do Laravel

import $ from 'jquery'; // 1. Trazemos o jQuery para dentro do maestro
window.$ = window.jQuery = $; // 2. Tornamos o '$' famoso globalmente

import './menu'; // 3. AGORA o menu.js pode ser carregado


document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const toggleIcon = document.querySelector('#toggleIcon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Alterna o tipo do input entre 'password' e 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alterna o ícone de olho aberto / olho cortado
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    }
});