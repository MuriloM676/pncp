# PNCP Integration

Sistema de consulta de oportunidades do PNCP utilizando Docker, PHP e Alpine.js.

## 🚀 Como Rodar

1. **Configuração**:
   ```bash
   cp .env.example .env
   ```
2. **Subir Containers**:
   ```bash
   docker compose up -d --build
   ```
3. **Acesso**:
   - Frontend: [http://localhost](http://localhost)
   - Healthcheck: [http://localhost:8080/health](http://localhost:8080/health)

## 🛠️ Comandos Úteis

- **Logs**: `docker compose logs -f backend`
- **Limpar Cache**: `docker exec pncp-backend php scripts/clear-cache.php`
- **Warmup**: `docker exec pncp-backend php scripts/warmup.php`

## 📝 Notas
- O sistema está em **Modo Mock** devido a instabilidades na API oficial do governo.
- Documentação completa disponível em [GEMINI.md](./GEMINI.md).