Preciso desenvolver um sistema completo de integração com a API do PNCP (Portal de Compras Públicas) usando Docker, PHP backend e frontend com Alpine.js.

Contexto da API:
- Base URL: https://apipcp.portaldecompraspublicas.com.br/publico/
- Endpoints principais que vamos usar:
  * obterProcessosPoucaParticipacao - processos com poucas propostas (oportunidades de negócio)
  * obterItensEmDisputa - itens em fase de lance ao vivo
  * obterProcesso - informações detalhadas de um processo
  * processosFornecedor - processos por CNPJ
  * obterStatusProcesso - status atual
  * obterChat - chat do pregão
  * obterAnexosLicitação - editais e documentos

Arquitetura definida:
1. Backend PHP 8.2 com Apache (porta 8080)
2. Redis 7 para cache (porta 6379)
3. Frontend Nginx servindo HTML/Alpine.js (porta 80)
4. Docker Compose para orquestração

Funcionalidades obrigatórias:
1. Listar oportunidades (processos com pouca participação)
2. Cache no Redis com TTL diferente por endpoint (5min para oportunidades, 15s para disputas, 10min para detalhes)
3. Rate limiting via Redis (60 req/minuto)
4. Frontend responsivo com Alpine.js e TailwindCSS
5. Modal para detalhes do processo
6. Auto-refresh a cada 5 minutos
7. Filtro por UF
8. Tratamento de erros com retry

Requisitos técnicos detalhados:

Backend PHP:
- Roteador em index.php (sem framework)
- Cliente cURL para chamar API do PNCP
- Classe RedisCache com get/set/delete/increment
- Classe RateLimiter usando Redis para contar requisições
- Classe PNCPClient com tratamento de erros e retry
- Endpoints específicos: /oportunidades, /disputa, /processo, /fornecedor, /status, /chat, /anexos, /health
- Logs de erro e acesso
- CORS configurado para desenvolvimento
- Healthcheck endpoint

Redis Cache Strategy:
- Chaves: md5(endpoint + params)
- TTLs: oportunidades=300s, disputa=15s, processo=600s, fornecedor=1800s, status=60s, chat=30s
- Cache warming no startup (pré-carregar processos populares)
- Estatísticas de cache (hits/misses)

Frontend Alpine.js:
- App com loading state, error handling
- Lista de cards de oportunidades
- Modal para detalhes do processo
- Auto-refresh configurável
- Filtro por UF funcionando no frontend
- Formatação de valores monetários
- Exibição de lances em tempo real (via polling)
- Responsivo (TailwindCSS)

Docker:
- docker-compose.yml com 3 serviços (redis, backend, frontend)
- Dockerfile para PHP com extensões: redis, curl, zip
- Dockerfile para frontend (ou apenas Nginx servindo HTML estático)
- Variáveis de ambiente via .env
- Volumes: redis-data para persistência, backend-logs
- Network bridge
- Healthcheck em todos containers

Arquivos necessários (estrutura completa):
projeto-pncp/
├── docker-compose.yml
├── .env.example
├── backend/
│   ├── Dockerfile
│   ├── docker-entrypoint.sh
│   ├── index.php (roteador)
│   ├── .htaccess
│   ├── endpoints/
│   │   ├── oportunidades.php
│   │   ├── disputa.php
│   │   ├── processo.php
│   │   ├── fornecedor.php
│   │   ├── status.php
│   │   ├── chat.php
│   │   ├── anexos.php
│   │   └── health.php
│   ├── lib/
│   │   ├── config.php
│   │   ├── pncp-client.php
│   │   ├── cache-redis.php
│   │   ├── rate-limit.php
│   │   └── logger.php
│   ├── middleware/
│   │   └── cors.php
│   └── scripts/
│       ├── warmup.php
│       └── clear-cache.php
└── frontend/
    ├── Dockerfile
    ├── nginx.conf
    └── public/
        ├── index.html
        └── favicon.ico

Código de exemplo para cada arquivo deve ser gerado completo e funcional, com comentários explicativos.

Variáveis de ambiente a serem usadas:
PNCP_BASE_URL=https://apipcp.portaldecompraspublicas.com.br
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_TTL_OPORTUNIDADES=300
CACHE_TTL_DISPUTA=15
CACHE_TTL_PROCESSO=600
RATE_LIMIT_MAX=60
RATE_LIMIT_WINDOW=60
APP_ENV=development
CORS_ALLOWED_ORIGINS=http://localhost

Tratamento de erros específicos:
- API do PNCP retornando 429 (rate limit): implementar backoff exponential
- Redis offline: continuar funcionando sem cache (fallback)
- Timeout da API PNCP: retry com 3 tentativas, 1s de intervalo
- Erro de autenticação (chave inválida): log e retornar mensagem amigável

Performance esperada:
- Respostas cacheadas em <50ms (Redis local)
- Respostas da API PNCP em <2s (com cache miss)
- Frontend renderizando lista em <200ms

Segurança:
- Sanitizar inputs (especialmente idLicitacao, documentoFornecedor)
- Não expor chave da API no frontend (tudo via backend)
- Rate limiting por IP
- Headers de segurança básicos

Comandos para execução após gerar os arquivos:
1. Copiar .env.example para .env e preencher PNCP_PUBLIC_KEY
2. docker-compose build
3. docker-compose up -d
4. Acessar http://localhost

O sistema final deve ser funcional, pronto para rodar com docker-compose up, e capaz de consultar a API real do PNCP. Gerar todos os arquivos necessários com o código completo.