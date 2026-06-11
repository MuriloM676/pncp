# 🚀 Portal de Integração PNCP

Sistema completo de consulta e monitoramento de oportunidades do **PNCP (Portal Nacional de Contratações Públicas)**. Este projeto utiliza uma arquitetura moderna focada em performance, escalabilidade e baixa latência através de cache inteligente.

---

## 🏗️ Arquitetura do Sistema

O sistema é dividido em três camadas principais, orquestradas via **Docker**:

1.  **Frontend (Nginx + Alpine.js)**: Interface reativa e ultraleve que consome nossa API.
2.  **Backend (PHP 8.2 + Apache)**: Camada de inteligência, roteamento e comunicação com a API do PNCP.
3.  **Cache & Rate Limit (Redis 7)**: O "coração" da performance, garantindo respostas rápidas e proteção contra bloqueios da API externa.

---

## ⚙️ Como os Dados são Atualizados?

Uma das maiores dúvidas é: *O sistema atualiza sozinho?* **Sim!** Ele utiliza uma estratégia de atualização em duas frentes:

### 🔄 No Frontend (O que o usuário vê)
O site possui um mecanismo de **auto-refresh** a cada **5 minutos** (300 segundos). Você pode ver o contador no topo da tela. Quando o tempo acaba, o Alpine.js solicita dados novos ao backend automaticamente.

### ⚡ No Backend (Cache Inteligente)
Implementamos uma estratégia de **TTL (Time To Live)** no Redis:
- **Oportunidades:** Atualizadas a cada 5 minutos.
- **Disputas ao Vivo:** Atualizadas a cada 15 segundos (para lances em tempo real).
- **Detalhes do Processo:** Atualizados a cada 10 minutos.

> **Vantagem:** Se 100 usuários acessarem a mesma página, apenas a primeira requisição vai até o PNCP. As outras 99 são entregues instantaneamente pelo Redis em menos de **50ms**.

---

## 🚀 Como Rodar o Projeto

### 1. Preparação
O sistema usa variáveis de ambiente para configuração. Comece criando o seu arquivo `.env`:
```bash
cp .env.example .env
```

### 2. Inicialização
Suba toda a infraestrutura com um único comando:
```bash
docker compose up -d --build
```

### 3. Acesso
- **Aplicação Web:** [http://localhost](http://localhost)
- **Status da API:** [http://localhost:8080/health](http://localhost:8080/health)

---

## 🛠️ Ferramentas de Manutenção

Criamos scripts específicos para facilitar a gestão do sistema:

### 🔥 Warmup (Aquecimento de Cache)
Para garantir que o primeiro usuário do dia não sinta lentidão, você pode "aquecer" o cache com os dados mais procurados (Nacional, SP, RJ, MG, PR):
```bash
docker exec pncp-backend php scripts/warmup.php
```

### 🧹 Limpeza de Cache
Caso precise forçar a atualização de todos os dados imediatamente:
```bash
docker exec pncp-backend php scripts/clear-cache.php
```

---

## 📋 Funcionalidades Implementadas

- [x] Listagem de oportunidades com pouca participação.
- [x] Filtro por Unidade Federativa (UF).
- [x] Modal de detalhes completo (Objeto, Órgão, Valores).
- [x] Visualização de itens em disputa e lances em tempo real.
- [x] Tratamento de erros com Retry e Backoff Exponencial.
- [x] Rate Limiting por IP (Proteção contra abusos).
- [x] Design responsivo com TailwindCSS.

---

## 📁 Estrutura de Arquivos

```text
projeto-pncp/
├── backend/          # Lógica em PHP, Endpoints e Integração API
├── frontend/         # HTML estático, Alpine.js e Nginx
├── docker-compose.yml# Orquestração dos serviços
├── .env              # Configurações do sistema (TTLs, Portas, etc)
└── GEMINI.md         # Este guia top que você está lendo
```

---
**Desenvolvido com foco em eficiência e simplicidade.** 🚀
