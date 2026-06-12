# FRONTEND GAP ANALYSIS: PORTAL PNCP

Este relatório identifica as discrepâncias entre as capacidades do backend e a interface visual atual, destacando oportunidades de melhoria e funcionalidades ocultas.

## 1. Endpoints Existentes Sem Interface Visual
| Endpoint | Funcionalidade | Esforço | Impacto | Prioridade |
| :--- | :--- | :--- | :--- | :--- |
| `/chat` | Histórico de mensagens do processo | Médio | Alto | Alta |
| `/anexos` | Listagem e download de documentos | Baixo | Alto | Alta |
| `/status` | Histórico de mudanças de estado do processo | Baixo | Médio | Média |
| `/fornecedor` | Consulta de dados cadastrais de empresas | Baixo | Médio | Baixa |
| `/materiais` | Busca de materiais por descrição (PDM) | Baixo | Médio | Média |

## 2. Funcionalidades Implementadas mas não Expostas
- **Paginado de Oportunidades:** O backend suporta o parâmetro `pagina`, mas o frontend não possui controles de navegação (Anterior/Próxima).
  - *Esforço: Baixo | Impacto: Alto | Prioridade: Alta*
- **Filtro por Tipo de Material:** No backend, existe suporte para Grupos, Classes e PDM, mas na UI o PDM (busca por texto) não está integrado.
  - *Esforço: Baixo | Impacto: Médio | Prioridade: Média*
- **Atualização em Tempo Real de Disputas:** Embora o backend tenha TTL de 15s para disputas, o frontend só carrega esses dados uma vez ao abrir o modal.
  - *Esforço: Médio | Impacto: Alto | Prioridade: Alta*

## 3. Dados Retornados pela API Não Exibidos
- **Processo Detalhado (`/processo`):**
  - Modalidade da Contratação (Lei 14.133 vs outras).
  - Data de publicação oficial.
  - Link direto para o edital (se disponível no objeto).
- **Lista de Oportunidades (`/oportunidades`):**
  - Quantidade de itens no processo.
  - Status atual (Aberto, Suspenso, Encerrado).

## 4. Componentes Alpine.js Ausentes
- **`chatViewer`:** Componente para renderizar balões de mensagens e timestamps.
- **`attachmentList`:** Componente para listar arquivos com ícones de tipo (PDF, DOCX) e botão de download.
- **`statusTimeline`:** Componente visual (linha do tempo) para mostrar a evolução do processo.
- **`paginationHandler`:** Lógica global para gerenciar o estado da página atual em diferentes abas.

## 5. Melhorias de UX Possíveis
| Melhoria | Descrição | Esforço | Impacto | Prioridade |
| :--- | :--- | :--- | :--- | :--- |
| **Skeleton Loading** | Substituir o spinner central por skeletons nos cards de oportunidade. | Baixo | Médio | Média |
| **Empty States** | Ilustrações ou mensagens amigáveis quando nenhum resultado é encontrado nos filtros. | Baixo | Baixo | Baixa |
| **Search-as-you-type** | Busca por palavras-chave no objeto da compra sem necessidade de recarregar a página. | Médio | Alto | Alta |
| **Dark Mode** | Suporte a tema escuro usando classes nativas do Tailwind. | Baixo | Baixo | Baixa |
| **Toast Notifications** | Alertas flutuantes para erros de API ou sucesso em ações. | Baixo | Médio | Média |

---
**Conclusão:** O backend está significativamente mais maduro que o frontend. A prioridade imediata deve ser a implementação de **Paginação** e a exibição de **Anexos e Chat** dentro do modal de detalhes, que são dados cruciais para a tomada de decisão do usuário.
