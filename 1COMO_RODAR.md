# 🚀 Guia Rápido: Como Rodar o Projeto (Codespaces)

## 1. Ligar o Ambiente (Backend / Banco de dados)

sail up -d

## 2. Limpar Caches do Laravel

docker compose exec app php artisan optimize:clear

## 3. Rodar Migrações do Banco

docker compose exec app php artisan migrate

## 4. Ligar o Front-end (Vite)

*(Abra uma NOVA aba no terminal e execute:)*
npm run dev

## 🛑 Para Desligar Tudo

sail down
