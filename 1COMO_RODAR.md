# 🚀 Guia Prático: Como Rodar e Testar o Projeto Localmente (Laravel Sail)

Este guia contém o passo a passo exato e testado para inicializar, configurar e testar tanto o **Back-end** quanto o **Front-end** da aplicação usando o **Laravel Sail** com banco de dados **MySQL**.

---

## 📋 Pré-requisito Único

Ter o Docker rodando no seu computador ou estar utilizando o GitHub Codespaces.

> **💡 Dica de Atalho:** Se você configurou o alias do Sail em seu terminal, pode usar `sail` diretamente. Caso contrário, use `./vendor/bin/sail`.

---

## 🛠️ Passo 1: Configuração Inicial e Permissões (Apenas na 1ª vez)

Caso seja a primeira vez clonando ou após mudanças nas pastas de cache, garanta o arquivo `.env` e permissões de escrita:

```bash
# Copia o arquivo de ambiente caso não exista
cp -n .env.example .env

# Garante permissões para o Laravel gravar logs, sessões e views compiladas
chmod -R 777 storage bootstrap/cache
```

---

## 🐳 Passo 2: Subir os Containers do Sail (Aplicação + MySQL)

Inicie os containers em segundo plano:

```bash
./vendor/bin/sail up -d
```

_(Opcional)_ Para verificar se os dois containers estão ativos e saudáveis (`healthy`):

```bash
./vendor/bin/sail ps
```

---

## 🧹 Passo 3: Limpar Caches da Aplicação

Sempre que puxar novas alterações da branch, limpe os caches de rotas, views e configurações:

```bash
./vendor/bin/sail artisan optimize:clear
```

---

## 🗄️ Passo 4: Rodar Migrações e Popular o Banco de Dados (MySQL)

Recrie as tabelas do MySQL do zero e popule com dados de teste reais (usuários, livros, categorias, pedidos e avaliações):

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## 🎨 Passo 5: Compilar os Assets do Front-end (Vite, CSS e JS)

Você tem duas opções para o front-end:

### Opção A: Compilação Rápida de Produção (Recomendado para Testes)

Gera os arquivos CSS, JS (máscaras e validações em tempo real) e o manifesto do Vite:

```bash
./vendor/bin/sail npm run build
```

### Opção B: Servidor de Desenvolvimento em Tempo Real (Hot Reload)

Se você estiver editando layouts Blade, estilos ou scripts JS e quiser recarregamento automático no navegador, abra uma **nova aba no terminal** e execute:

```bash
./vendor/bin/sail npm run dev
```

---

## 🧪 Passo 6: Executar a Suíte de Testes Automatizados

Rode a suíte de testes completa para garantir que todas as regras de negócio e rotas estão 100% funcionais:

```bash
./vendor/bin/sail test
```

### Rodar Testes Específicos:

- **Testar validações de cadastro** (regras de tamanho mínimo/máximo de nome, maioridade de 18 anos, CPF único, etc.):
    ```bash
    ./vendor/bin/sail test tests/Feature/Auth/RegistrationValidationTest.php
    ```
- **Testar modais de autenticação e menu**:
    ```bash
    ./vendor/bin/sail test tests/Feature/ModalAndMenuTest.php
    ```
- **Testar fluxo de autenticação (Login / Logout)**:
    ```bash
    ./vendor/bin/sail test tests/Feature/Auth/AuthenticationTest.php
    ```

---

## 🌐 Passo 7: Testar Manualmente no Navegador

Abra o navegador no endereço: **`http://localhost`** (ou acerte a porta `80` na aba _Ports_ se estiver no Codespaces).

### O que você pode testar na interface:

1. **Menu de Navegação:**
    - Visual moderno, responsivo (teste redimensionando a janela para mobile).
    - Botão rápido de acesso e links com ícones Lucide.
2. **Modal de Login:**
    - Clique em **"Entrar"** no topo da página. O modal abre sobre a página inicial sem recarregar a tela.
3. **Modal de Cadastro e Validações em Tempo Real:**
    - Clique em **"Cadastre-se"** (no menu ou pelo link dentro do modal de login).
    - **Nome:** Digite menos de 3 caracteres ou mais de 100 caracteres e veja o aviso.
    - **Máscaras Dinâmicas:** Digite no campo **CPF** e **Telefone** para verificar a formatação automática.
    - **Nascimento:** Selecione uma data; o sistema valida e bloqueia menores de 18 anos.
    - **Senha e Confirmação:** Digite a senha para ver os requisitos de segurança e confirme no campo ao lado.
4. **Modal de Recuperação de Senha:**
    - No modal de login, clique em **"Esqueceu sua senha?"**. O modal de envio de link de recuperação será exibido.

---

## 🛑 Comandos Úteis de Apoio

| Objetivo                                   | Comando                         |
| :----------------------------------------- | :------------------------------ |
| **Acompanhar logs em tempo real**          | `./vendor/bin/sail logs -f app` |
| **Acessar o terminal dentro do container** | `./vendor/bin/sail shell`       |
| **Acessar o console do MySQL**             | `./vendor/bin/sail mysql`       |
| **Abrir o Laravel Tinker**                 | `./vendor/bin/sail tinker`      |
| **Desligar os containers**                 | `./vendor/bin/sail down`        |
| **Desligar e resetar volumes do Docker**   | `./vendor/bin/sail down -v`     |
