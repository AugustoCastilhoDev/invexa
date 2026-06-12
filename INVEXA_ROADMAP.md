# 🗺️ Invexa — Roadmap de Evolução do Produto

> **Documento vivo** — atualizar status e datas conforme cada item for concluído.
> Última revisão: Junho 2026 | Analista: Claude (Anthropic) + Augusto Castilho

---

## 📊 Visão Geral por Fase

| Fase | Foco | Prazo sugerido | Status |
|------|------|---------------|--------|
| **Fase 1** | Credibilidade & Estabilidade | Semanas 1–2 | ✅ Concluído |
| **Fase 2** | Produto & Conversão | Semanas 3–5 | ✅ Concluído |
| **Fase 3** | Conformidade & Suporte | Semanas 6–8 | ✅ Concluído |
| **Fase 4** | Qualidade Técnica | Semanas 9–12 | ✅ Concluído |
| **Fase 5** | Diferenciação de Mercado | Mês 4–6 | 🔄 Em andamento |

---

## 🔴 FASE 1 — Credibilidade & Estabilidade (Semanas 1–2)

> Itens que, se não resolvidos, comprometem a confiança do usuário e a segurança dos dados em produção.

---

### 1.1 — Domínio próprio em produção

- **Prioridade:** 🔴 Crítico
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto — credibilidade imediata com potenciais clientes
- **Status:** ✅ Concluído — `invexa-app.com.br` no ar com SSL

**O que foi feito:**
- [x] Domínio `invexa-app.com.br` registrado e apontado para o VPS
- [x] Nginx reconfigurado com novo domínio
- [x] Certificado SSL emitido via Certbot
- [x] `.env` atualizado com `APP_URL=https://invexa-app.com.br`
- [x] `SESSION_DOMAIN` e `SESSION_COOKIE` atualizados
- [x] Webhook Stripe reconfigurado com novo domínio
- [x] Login, pagamento e rotas testados

---

### 1.2 — Backup automatizado e testado

- **Prioridade:** 🔴 Crítico
- **Esforço:** Médio (~4h setup + teste)
- **Impacto:** Alto — proteção de dados de todos os tenants
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] Script de backup MySQL criado em `/usr/local/bin/invexa-backup.sh`
- [x] Backup agendado via cron diariamente às 03h00
- [x] Retenção de 30 dias configurada
- [x] Restore testado com sucesso

---

### 1.3 — Corrigir SESSION_SECURE_COOKIE no template

- **Prioridade:** 🔴 Crítico
- **Esforço:** Muito baixo (~15min)
- **Impacto:** Segurança de sessão com HTTPS ativo
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] `SESSION_SECURE_COOKIE=true` em produção e no `.env.example`

---

## 🟠 FASE 2 — Produto & Conversão (Semanas 3–5)

> Itens que impactam diretamente a taxa de conversão de visitante para usuário ativo.

---

### 2.1 — Screenshots reais na landing page

- **Prioridade:** 🟠 Alta
- **Esforço:** Baixo (~3h)
- **Impacto:** Alto — maior alavanca de conversão de trial
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] Capturas de tela reais do sistema adicionadas à landing page
- [x] Seção "Veja o sistema em ação" implementada

---

### 2.2 — Depoimentos com credibilidade

- **Prioridade:** 🟠 Alta
- **Esforço:** Médio (~1 semana para coletar)
- **Impacto:** Médio-alto — social proof remove objeções no momento de compra
- **Status:** ✅ Concluído

---

### 2.3 — Canal de suporte visível (WhatsApp / Chat)

- **Prioridade:** 🟠 Alta
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto — essencial para conversão no mercado de PMEs brasileiro
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] Botão flutuante de suporte adicionado na landing e no app
- [x] Canal de suporte configurado

---

### 2.4 — Plano Free / tabela de comparação clara

- **Prioridade:** 🟠 Alta
- **Esforço:** Médio (~4h)
- **Impacto:** Alto — remove objeção no topo do funil
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] Tabela de pricing com comparação de planos implementada
- [x] Trial configurado com bloqueio automático ao vencer

---

## 🟡 FASE 3 — Conformidade & Segurança (Semanas 6–8)

---

### 3.1 — LGPD: Política de Privacidade e Termos de Uso

- **Prioridade:** 🟡 Média-alta
- **Esforço:** Médio (~1 dia)
- **Impacto:** Legal + converte clientes corporativos
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] Rotas públicas `/privacidade` e `/termos` criadas
- [x] Páginas acessíveis em `invexa-app.com.br/privacidade` e `invexa-app.com.br/termos`
- [x] Links adicionados no footer da landing e do app

---

### 3.2 — Monitoramento de erros em produção (Sentry / Flare)

- **Prioridade:** 🟡 Média-alta
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto para operação — sem isso você está cego em produção
- **Status:** ✅ Concluído — Flare instalado e configurado

**O que foi feito:**
- [x] Flare instalado e configurado
- [x] `APP_DEBUG=false` em produção
- [x] Alertas por e-mail para erros novos configurados

---

### 3.3 — Audit Log completo

- **Prioridade:** 🟡 Média
- **Esforço:** Alto (~2–3 dias)
- **Impacto:** Conformidade financeira + suporte ao cliente
- **Status:** ✅ Concluído

**O que foi feito:**
- [x] `AuditLog::record()` implementado nos Controllers críticos
- [x] Eventos logados: vendas, financeiro, assinaturas, login/logout, usuários, impersonation
- [x] View de consulta de logs no painel Admin e Super-Admin

---

## 🟢 FASE 4 — Qualidade Técnica (Semanas 9–12)

---

### 4.1 — Suíte de testes expandida

- **Prioridade:** 🟢 Importante
- **Esforço:** Alto (~1 semana)
- **Impacto:** Segurança para evoluir o produto sem quebrar o que existe
- **Status:** ✅ Concluído

---

### 4.2 — Observabilidade de performance (Laravel Telescope)

- **Prioridade:** 🟢 Importante
- **Esforço:** Baixo (~2h)
- **Impacto:** Identificar queries lentas e N+1 antes de virarem problema
- **Status:** ✅ Concluído

---

### 4.3 — Migração de queue driver para Redis

- **Prioridade:** 🟢 Importante (futuro)
- **Esforço:** Baixo (~3h)
- **Impacto:** Escalabilidade de jobs e notificações
- **Status:** ✅ Concluído

---

## 🔵 FASE 5 — Diferenciação de Mercado (Mês 4–6)

> Features que transformam o Invexa de um bom ERP em um ERP difícil de substituir.

---

### 5.1 — Integração Pix multi-tenant (Asaas por empresa)

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Alto (~2 semanas)
- **Impacto:** Altíssimo — feature mais solicitada por PMEs brasileiras
- **Status:** ✅ Concluído — Pix multi-tenant via Asaas funcionando em produção

**Decisão tomada:** Pix multi-tenant — cada cliente conecta sua própria conta Asaas.

**O que foi feito:**
- [x] Campos `asaas_api_key` e `asaas_wallet_id` na tabela `companies`
- [x] Tela de configuração Asaas no painel da empresa
- [x] `PixPaymentService` usando a chave da empresa autenticada
- [x] Geração de cobrança Pix na finalização de venda
- [x] QR Code e copia-e-cola retornados
- [x] Webhook Asaas para confirmação de pagamento por empresa
- [x] Baixa automática de Conta a Receber ao confirmar Pix
- [x] QR Code exibido no PDV e na nota/invoice PDF
- [x] Fluxo completo testado: venda → Pix → confirmação automática → baixa AR

---

### 5.2 — NF-e / NFS-e integrada

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Muito alto (~4–6 semanas)
- **Impacto:** Altíssimo — principal driver de upgrade e retenção
- **Status:** 🔲 Em andamento — Focus NFe escolhido, modelo multi-tenant (cada empresa usa sua própria conta)

**Decisão tomada:** Focus NFe, NF-e manual (usuário clica "Emitir"), cada empresa com suas próprias credenciais.

**O que fazer:**
- [ ] Criar conta Focus NFe e obter credenciais de homologação
- [ ] Adicionar campos fiscais na tabela `companies`:
  - CNPJ, IE, regime tributário, série NF, certificado A1 (.pfx)
  - Campos `focusnfe_token` e `focusnfe_cnpj`
- [ ] Adicionar campos fiscais na tabela `products`:
  - NCM, CFOP, CST, alíquotas ICMS/PIS/COFINS
- [ ] Adicionar campos fiscais na tabela `customers`:
  - CPF/CNPJ, inscrição estadual
- [ ] Implementar `NFeService`:
  - Emitir NF-e ao concluir venda (botão manual)
  - Download do XML e DANFE em PDF
  - Cancelamento de NF-e
  - Carta de correção
- [ ] Adicionar módulo "Fiscal" no menu (gerente+)
- [ ] Testes extensivos em homologação antes de produção
- [ ] Documentar configuração fiscal por estado

---

### 5.3 — App mobile (PWA)

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Médio (~1 semana)
- **Impacto:** Médio-alto — melhora retenção e uso diário
- **Status:** ✅ Concluído — PWA instalável no Android e iOS com ícones do Invexa

**O que foi feito:**
- [x] Service Worker implementado com cache offline de assets
- [x] Meta tags PWA no layout Blade principal
- [x] "Adicionar à tela inicial" testado em Android e iOS
- [x] Telas mobile otimizadas: Dashboard, Nova Venda, Estoque

---

### 5.4 — API pública documentada

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Alto (~2–3 semanas)
- **Impacto:** Abre ecossistema de integrações e aumenta ticket médio
- **Status:** ✅ Concluído — API v1 no ar com documentação pública em `invexa-app.com.br/api-docs`

**O que foi feito:**
- [x] `routes/api.php` com endpoints RESTful para módulos principais
- [x] Documentação HTML publicada em `public/api-docs.html`
- [x] Rota pública `/api-docs` configurada em `routes/web.php`
- [x] Endpoints implementados:
  - `POST /api/v1/auth/token` — geração de token
  - `DELETE /api/v1/auth/token` — revogar token
  - `GET /api/v1/me` — dados do usuário autenticado
  - `GET|POST|PUT|DELETE /api/v1/products` — CRUD completo
  - `GET|POST|PUT|DELETE /api/v1/customers` — CRUD completo
  - `GET|POST /api/v1/sales` — listar e criar vendas
  - `GET /api/v1/stock` — estoque geral
  - `GET /api/v1/stock/low` — produtos com estoque baixo
  - `POST /api/v1/stock/movement` — registrar movimentação
- [x] Autenticação via Sanctum (Bearer Token)
- [x] Rate limiting: 60 req/min por token
- [x] Tokens gerenciáveis em Settings → API Tokens

---

## 📋 Checklist de Status Geral

> Atualizado em Junho 2026.

### Fase 1 — Credibilidade & Estabilidade
- [x] 1.1 Domínio próprio em produção (`invexa-app.com.br`)
- [x] 1.2 Backup automatizado e testado
- [x] 1.3 SESSION_SECURE_COOKIE corrigido

### Fase 2 — Produto & Conversão
- [x] 2.1 Screenshots reais na landing page
- [x] 2.2 Depoimentos com credibilidade
- [x] 2.3 Canal de suporte (WhatsApp / Chat)
- [x] 2.4 Plano Free / tabela de comparação clara

### Fase 3 — Conformidade & Segurança
- [x] 3.1 LGPD: Política de Privacidade e Termos de Uso
- [x] 3.2 Monitoramento de erros (Flare instalado)
- [x] 3.3 Audit Log completo

### Fase 4 — Qualidade Técnica
- [x] 4.1 Suíte de testes expandida
- [x] 4.2 Observabilidade (Laravel Telescope)
- [x] 4.3 Migração para Redis

### Fase 5 — Diferenciação de Mercado
- [x] 5.1 Pix multi-tenant (Asaas por empresa)
- [ ] 5.2 NF-e / NFS-e integrada (Focus NFe — em andamento)
- [x] 5.3 App mobile (PWA)
- [x] 5.4 API pública documentada (`invexa-app.com.br/api-docs`)

---

## 🧠 Decisões Tomadas

| # | Decisão | Escolha | Data |
|---|---------|---------|------|
| D1 | Estratégia de aquisição | Trial com bloqueio automático ao vencer | Jun/2026 |
| D2 | Gateway Pix | Asaas multi-tenant (cada empresa usa sua conta) | Jun/2026 |
| D3 | API de NF-e | Focus NFe | Jun/2026 |
| D4 | Monitoramento de erros | Flare | Jun/2026 |
| D5 | Documentação da API | HTML estático em `public/api-docs.html` | Jun/2026 |

---

## 📈 Métricas de Sucesso por Fase

| Fase | Métrica-chave | Meta | Status |
|------|--------------|------|--------|
| Fase 1 | Domínio próprio no ar | ✓ / ✗ | ✅ |
| Fase 2 | Taxa de conversão landing → trial | > 5% | ✅ |
| Fase 3 | Uptime em produção | > 99.5% | ✅ |
| Fase 4 | Cobertura de testes (controllers críticos) | > 60% | ✅ |
| Fase 5 | MRR após 6 meses | Definir meta | 🔲 |

---

## 🔗 Referências Úteis

| Recurso | URL |
|---------|-----|
| Flare (monitoramento Laravel) | https://flareapp.io |
| Focus NFe API | https://focusnfe.com.br |
| eNotas API | https://enotas.com.br |
| Asaas (Pix/boleto BR) | https://asaas.com/developers |
| iubenda (LGPD) | https://iubenda.com |
| Laravel Telescope | https://laravel.com/docs/telescope |
| Laravel Cashier (Stripe) | https://laravel.com/docs/billing |
| shots.so (screenshots) | https://shots.so |
| Invexa API Docs | https://invexa-app.com.br/api-docs |

---

*Documento atualizado em Junho 2026 — Invexa v1.0 em produção*
*Repositório: https://github.com/AugustoCastilhoDev/invexa*
