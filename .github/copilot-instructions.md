# 🎯 Contexto do Projeto

Este projeto é um sistema web para gestão de um **bolão da Mega-Sena da Virada**, reutilizável anualmente, desenvolvido em **Laravel + MySQL**, sem login para participantes e com painel administrativo protegido por senha.

O sistema deve priorizar **transparência**, **automação dos cálculos** e **simplicidade de uso**.

---

## 🧩 Conceitos Fundamentais

### Participação
- Valor fixo: R$ 50,00
- Quantidade ilimitada por participante
- Percentual de participação = (participações do usuário / total de participações)
- O rateio de qualquer prêmio é sempre proporcional às participações

---

## 🔐 Acesso ao Sistema

### Participantes
- Não possuem login e senha
- Acessam via link único no formato:

  `/p/{hash1}/{hash2}`

- Os hashes são gerados da seguinte forma:
  - `hash1 = md5(APP_ANO) + sha1(telefone)`
  - `hash2 = md5(APP_ANO) + sha1(telefone) + sha1(APP_ANO)`
- `APP_ANO` e telefone do admin ficam no `.env`
- Ao trocar o ano no `.env`, todos os links antigos se tornam inválidos

### Admin
- Acesso protegido por senha
- Telefone do admin definido no `.env`
- O admin acessa um painel completo de gestão

---

## ⏱️ Congelamento do Bolão

- Existe um **contador regressivo** visível para todos
- Data fixa: `30/12/{ANO_ATUAL} às 23:59:59`
- O bolão só é oficialmente encerrado quando o admin clicar em **"Fechar Bolão"**
- Após o fechamento:
  - Não é possível adicionar participações
  - Os jogos são considerados definitivos

---

## 🎯 Estratégia de Geração de Jogos (Automática)

- O sistema deve apostar **todo o montante arrecadado**
- Estratégia obrigatória:
  1. Tentar criar o maior jogo possível (15 → 14 → 13 → ... → 6)
  2. Se o valor restante não permitir repetir o mesmo tamanho, tentar o próximo menor
  3. Continuar até não ser possível criar nem um jogo de 6 números
- A tabela oficial de preços por quantidade de números deve ser parametrizada no banco

### Sobra de Dinheiro
- Caso sobre valor insuficiente para um jogo de 6 números:
  - O **admin complementa manualmente**
  - Esse valor gera uma **fração adicional de participação** exclusivamente para o admin

---

## 📊 Probabilidades

- As probabilidades são exibidas **por tipo de jogo**
  - Sena
  - Quina
  - Quadra
- As probabilidades são baseadas na tabela oficial da Mega-Sena
- Não calcular probabilidade combinada entre jogos

---

## 🧾 Comprovantes

- Para cada conjunto de jogos realizados, o admin deve:
  - Registrar os números apostados
  - Anexar PDF ou imagem do comprovante da lotérica
- Todos os participantes podem:
  - Ver os jogos realizados
  - Ver o comprovante anexado

---

## 👤 Painel do Participante

Cada participante deve visualizar:
- Quantidade de participações adquiridas
- Percentual de participação no bolão
- Total arrecadado
- Jogos possíveis com o montante atual
- Probabilidades por tipo de jogo
- Simulação de prêmio:
  - Valor estimado que receberia em caso de Sena, Quina ou Quadra
- Jogos realizados + comprovantes

---

## 🧑‍💼 Painel do Administrador

O admin deve poder:
- Criar, editar e visualizar participantes
- Ajustar participações manualmente
- Ver total arrecadado
- Simular jogos antes do fechamento
- Fechar o bolão
- Registrar jogos efetivamente realizados
- Anexar comprovantes
- Registrar resultado do sorteio
- Visualizar automaticamente o rateio final

---

## 🧱 Arquitetura Técnica

- Framework: Laravel
- Banco: MySQL
- Backend estruturado em:
  - Models
  - Services (para regras de negócio)
  - Controllers enxutos
- Não usar lógica pesada em Controllers
- Priorizar código limpo, legível e reutilizável
- Sistema responsivo (mobile-friendly)

---

## 📦 Estrutura Esperada de Entidades (alto nível)

- User (Admin)
- Participant
- Participation
- Game
- GameNumber
- Receipt
- ProbabilityTable
- Settings (ano, valor da participação, datas, etc.)

---

## 🧪 Qualidade

- Validar valores monetários com precisão (usar decimal)
- Evitar valores mágicos no código
- Centralizar regras de negócio
- Código preparado para reutilização anual

---

## 🎯 Objetivo Final

Gerar um sistema confiável, transparente e reutilizável, que permita a gestão completa de um bolão da Mega-Sena da Virada com o mínimo de intervenção manual e máxima clareza para os participantes.
