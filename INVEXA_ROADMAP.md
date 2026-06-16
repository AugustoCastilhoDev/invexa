# 🗺️ Invexa — Roadmap de Evolução do Produto

> **Documento vivo** — atualizar status e datas conforme cada item for concluído.
> Última revisão: 16 Jun 2026 | Analista: Claude (Anthropic) + Augusto Castilho

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

### 5.2 — NF-e integrada (Focus NFe)

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Muito alto (~4–6 semanas)
- **Impacto:** Altíssimo — principal driver de upgrade e retenção
- **Status:** 🔄 Em andamento — backend concluído, UI e validação pendentes

**Decisão tomada:** Focus NFe, NF-e manual (usuário clica "Emitir"), cada empresa com suas próprias credenciais.

#### ✅ Concluído (16 Jun 2026)

- [x] Campos fiscais adicionados à tabela `companies`: `focusnfe_token`, `focusnfe_ambiente`, `nfe_serie`
- [x] Campos fiscais adicionados à tabela `products`: `ncm`, `cfop`, `unidade_tributavel`, `barcode`
- [x] Campos de endereço fiscal adicionados à tabela `customers`: `logradouro`, `numero_endereco`, `bairro`, `municipio`, `uf`, `cep`, `complemento`
- [x] Model `Nfe` criado com todos os status e constantes (`STATUS_PENDENTE`, `STATUS_AUTORIZADA`, etc.)
- [x] Migration `create_nfes_table` — tabela completa com `ref_focusnfe`, `chave_acesso`, `protocolo`, `xml_path`, `pdf_path`, etc.
- [x] `FocusNfeService` implementado:
  - `emitir(Sale $sale)` — monta payload e envia para Focus NFe
  - `consultar(string $ref)` — consulta status na Focus
  - `cancelar(string $ref, string $justificativa)` — cancelamento
  - `cartaCorrecao(string $ref, string $correcao)` — CC-e
  - `syncStatus(Nfe $nfe)` — sincroniza status com a SEFAZ
  - `buildPayload()` — monta o payload completo: emitente, destinatário, itens, tributos
- [x] Campos ICMS/PIS/COFINS corrigidos para Simples Nacional: `icms_csosn: '102'`, `pis_situacao_tributaria: '07'`, `cofins_situacao_tributaria: '07'`
- [x] Payload usa CNPJ de homologação (`34.785.515/0001-66`) em ambiente `homologacao`
- [x] Série automática: `2` em homologação, `1` (configurável) em produção
- [x] Model `NfeNumeration` criado com controle de sequência atômico (SELECT FOR UPDATE):
  - `proximoNumero($companyId, $serie, $ambiente)` — incrementa e retorna próximo número
  - `atualNumero(...)` — consulta sem incrementar
  - `definirNumero(...)` — sincronização manual com numeração SEFAZ
- [x] Migration `create_nfe_numerations_table` — unique em `(company_id, ambiente, serie)`
- [x] `FocusNfeService` atualizado para usar `NfeNumeration::proximoNumero()` no payload

#### 🔲 Pendente — próximas sessões

**Backend restante:**
- [ ] `NfeController` — rotas e actions: `emitir`, `consultar`, `cancelar`, `cartaCorrecao`, `download` (XML/DANFE)
- [ ] Job/Command para sincronização automática de status (`SyncNfeStatus`) — consulta NFs `processando` a cada 5 min
- [ ] Storage de XML e DANFE retornados pela Focus (S3 ou disco local)
- [ ] Evento `NfeAutorizada` + notificação in-app para o usuário

**UI — Módulo Fiscal:**
- [ ] Tela de listagem de NFs: número, série, destinatário, valor, status, data de emissão
- [ ] Botão "Emitir NF-e" no detalhe da venda (apenas para vendas concluídas)
- [ ] Modal de confirmação antes de emitir (mostra resumo: itens, valor, destinatário)
- [ ] Badge de status na listagem (Pendente / Processando / Autorizada / Rejeitada / Cancelada)
- [ ] Botões de ação por NF: Download XML, Download DANFE, Cancelar, Carta de Correção
- [ ] Tela de configuração fiscal da empresa: `focusnfe_token`, `focusnfe_ambiente`, `nfe_serie`
- [ ] Campos fiscais no cadastro de produto: NCM, CFOP, unidade tributável, código de barras
- [ ] Campos de endereço fiscal no cadastro de cliente

**Configuração & Infraestrutura:**
- [ ] Certificado A1 real (e-CNPJ ou e-CPF) cadastrado na Focus NFe — necessário para validar fluxo completo
- [ ] Testes extensivos em homologação com certificado real antes de produção
- [ ] Documentação interna: configuração fiscal por estado (CFOP interestad. vs. intraestadual)
- [ ] Atualizar `INVEXA_ROADMAP.md` ao concluir cada pendência acima

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

> Atualizado em 16 Jun 2026.

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
- [x] 5.2a NF-e — backend: FocusNfeService, Nfe model, NfeNumeration, campos fiscais
- [ ] 5.2b NF-e — UI: módulo Fiscal, listagem, emissão, download, configuração
- [ ] 5.2c NF-e — infraestrutura: certificado A1, job de sync, storage XML/DANFE
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
| D6 | Regime tributário NF-e | Simples Nacional — CSOSN 102, PIS/COFINS 07 | Jun/2026 |
| D7 | Controle de numeração | `nfe_numerations` por empresa/série/ambiente com lock atômico | Jun/2026 |

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
| Focus NFe — Docs NF-e | https://developer.focusnfe.com.br/reference/nfe |
| eNotas API | https://enotas.com.br |
| Asaas (Pix/boleto BR) | https://asaas.com/developers |
| iubenda (LGPD) | https://iubenda.com |
| Laravel Telescope | https://laravel.com/docs/telescope |
| Laravel Cashier (Stripe) | https://laravel.com/docs/billing |
| shots.so (screenshots) | https://shots.so |
| Invexa API Docs | https://invexa-app.com.br/api-docs |
| Tabela NCM — Receita Federal | https://www.receita.fazenda.gov.br/orientacao/tributaria/classificacoes/tipi.htm |

---

*Documento atualizado em 16 Jun 2026 — Invexa v1.0 em produção*
*Repositório: https://github.com/AugustoCastilhoDev/invexa*
