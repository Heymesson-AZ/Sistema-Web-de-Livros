# Passo 1: Entrar na branch de desenvolvimento

Sempre que for programar ou fazer alterações, você deve estar na sua branch de trabalho (devops-deploy):

git checkout devops-deploy

## Passo 2: Fazer alterações e subir para a branch

Depois de alterar seus arquivos no computador, salve-os e rode os comandos para enviá-los para o GitHub na branch devops-deploy:

## 1. Adiciona os arquivos modificados

git add .

## 2. Cria o commit com uma mensagem explicando o que você mudou

git commit -m " "

## 3. Envia para o GitHub na branch de desenvolvimento

git push origin devops-deploy

Passo 3: Jogar o conteúdo para a main (Para atualizar a produção)
Quando tudo estiver testado e pronto para ir para o ar, você junta o código na branch main diretamente pelo terminal:

## 1. Muda para a branch main

git checkout main

## 2. Garante que a sua main local está atualizada com o GitHub

git pull origin main

## 3. Puxa todo o conteúdo da branch de desenvolvimento para dentro da main

git merge devops-deploy

## 4. Envia a main atualizada para o GitHub

git push origin main

Passo 4: O Deploy Automático 🚀
Assim que você executa o git push origin main do Passo 3:

O GitHub Actions detecta a alteração na main.

Ele roda o script automaticamente na sua VPS Oracle (163.176.81.174).

Para acompanhar se deu tudo certo, basta ir até o seu repositório no GitHub > aba Actions. O fluxo vai rodar sozinho e ficar verde (✅).
