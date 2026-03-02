import './bootstrap'; // Importações padrão do Laravel

import $ from 'jquery'; // 1. Trazemos o jQuery para dentro do maestro
window.$ = window.jQuery = $; // 2. Tornamos o '$' famoso globalmente

import './menu'; // 3. AGORA o menu.js pode ser carregado