# 🗺️ Invexa — Roadmap de Evolução do Produto

> **Documento vivo** — atualizar status e datas conforme cada item for concluído.
> Última revisão: Junho 2026 | Analista: Claude (Anthropic) + Augusto Castilho

---

## 📊 Visão Geral por Fase

| Fase | Foco | Prazo sugerido | Status |
|------|------|---------------|--------|
| **Fase 1** | Credibilidade & Estabilidade | Semanas 1–2 | 🔲 Pendente |
| **Fase 2** | Produto & Conversão | Semanas 3–5 | 🔲 Pendente |
| **Fase 3** | Conformidade & Suporte | Semanas 6–8 | 🔲 Pendente |
| **Fase 4** | Qualidade Técnica | Semanas 9–12 | 🔲 Pendente |
| **Fase 5** | Diferenciação de Mercado | Mês 4–6 | 🔲 Pendente |

---

## 🔴 FASE 1 — Credibilidade & Estabilidade (Semanas 1–2)

> Itens que, se não resolvidos, comprometem a confiança do usuário e a segurança dos dados em produção.

---

### 1.1 — Domínio próprio em produção

- **Prioridade:** 🔴 Crítico
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto — credibilidade imediata com potenciais clientes
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Registrar domínio próprio (sugestão: `invexa.com.br` ou `invexa.app`)
- [ ] Apontar DNS para o VPS Hostinger atual
- [ ] Reconfigurar Nginx: trocar `server_name invexa.offerjetshop.net` pelo novo domínio
- [ ] Emitir novo certificado SSL via Certbot: `certbot --nginx -d invexa.com.br`
- [ ] Atualizar `.env` de produção: `APP_URL=https://invexa.com.br`
- [ ] Atualizar `SESSION_DOMAIN` e `SESSION_COOKIE` no `.env`
- [ ] Reconfigurar webhook Stripe com o novo domínio no painel Stripe
- [ ] Testar login, pagamento e todas as rotas com o novo domínio
- [ ] Redirecionar domínio antigo (`offerjetshop.net`) com 301 para o novo

**Notas:**
```
Após trocar o domínio, rodar:
php artisan config:cache
php artisan route:cache
systemctl reload nginx
```

---

### 1.2 — Backup automatizado e testado

- **Prioridade:** 🔴 Crítico
- **Esforço:** Médio (~4h setup + teste)
- **Impacto:** Alto — proteção de dados de todos os tenants
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Criar script de backup MySQL (`/usr/local/bin/invexa-backup.sh`)
- [ ] Configurar envio automático para armazenamento externo (Backblaze B2 ou S3)
- [ ] Agendar via cron: backup diário às 03h00
- [ ] Manter retenção de 30 dias de backups
- [ ] **Testar restore completo** (passo mais importante — backup não testado = sem backup)
- [ ] Documentar procedimento de restore em `docs/DISASTER_RECOVERY.md`
- [ ] Configurar alerta por e-mail se o backup falhar

**Script base (adicionar ao VPS):**
```bash
#!/bin/bash
# /usr/local/bin/invexa-backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="invexa"
BACKUP_DIR="/var/backups/invexa"
S3_BUCKET="s3://seu-bucket/invexa-db"

mkdir -p $BACKUP_DIR
mysqldump -u root -p"$DB_PASSWORD" $DB_NAME | gzip > "$BACKUP_DIR/invexa_$DATE.sql.gz"

# Upload para storage externo (requer rclone configurado)
rclone copy "$BACKUP_DIR/invexa_$DATE.sql.gz" "$S3_BUCKET"

# Remover backups locais com mais de 7 dias
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete

echo "Backup concluído: invexa_$DATE.sql.gz"
```

**Crontab:**
```
0 3 * * * /usr/local/bin/invexa-backup.sh >> /var/log/invexa-backup.log 2>&1
```

---

### 1.3 — Corrigir SESSION_SECURE_COOKIE no template

- **Prioridade:** 🔴 Crítico
- **Esforço:** Muito baixo (~15min)
- **Impacto:** Segurança de sessão com HTTPS ativo
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] No `.env.example`, alterar `SESSION_SECURE_COOKIE=false` para `SESSION_SECURE_COOKIE=true`
- [ ] Verificar se em produção o `.env` real também está com `true`
- [ ] Adicionar comentário no `.env.example`:
  ```
  # Em produção com HTTPS, SEMPRE manter como true
  SESSION_SECURE_COOKIE=true
  ```

---

## 🟠 FASE 2 — Produto & Conversão (Semanas 3–5)

> Itens que impactam diretamente a taxa de conversão de visitante para usuário ativo.

---

### 2.1 — Screenshots reais na landing page

- **Prioridade:** 🟠 Alta
- **Esforço:** Baixo (~3h)
- **Impacto:** Alto — maior alavanca de conversão de trial
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Tirar capturas de tela de alta qualidade das telas principais:
  - Dashboard analítico (com dados de exemplo)
  - PDV / Tela de nova venda
  - Relatório de lucratividade
  - Controle financeiro (AP/AR)
  - Visão mobile (responsivo)
- [ ] Usar ferramenta para "embelezar" screenshots (ex: shots.so, screely.com)
- [ ] Adicionar seção "Veja o sistema em ação" na landing page após `#features`
- [ ] Considerar GIF ou vídeo curto (20–30s) mostrando o fluxo de uma venda completa
- [ ] Testar com dados de exemplo realistas (não usar "Produto Teste 1")

---

### 2.2 — Depoimentos com credibilidade

- **Prioridade:** 🟠 Alta
- **Esforço:** Médio (~1 semana para coletar)
- **Impacto:** Médio-alto — social proof remove objeções no momento de compra
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Contatar primeiros usuários/beta testers para depoimento real
- [ ] Solicitar: foto, nome completo, nome da empresa, cidade e segmento
- [ ] Alternativa rápida: substituir depoimentos atuais por um caso de uso detalhado seu (como você usaria para uma loja real)
- [ ] Adicionar nota de rodapé: "Resultados reais de clientes em período de beta"

---

### 2.3 — Canal de suporte visível (WhatsApp / Chat)

- **Prioridade:** 🟠 Alta
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto — essencial para conversão no mercado de PMEs brasileiro
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Criar número de WhatsApp Business dedicado ao Invexa
- [ ] Adicionar botão flutuante de WhatsApp na landing e no app (biblioteca: `whatsapp-button`)
- [ ] Alternativa/complemento: instalar Crisp Chat (plano free disponível) em `app.crisp.chat`
- [ ] Configurar mensagem automática de boas-vindas no WhatsApp Business
- [ ] Adicionar e-mail de suporte visível no footer: `suporte@invexa.com.br`
- [ ] Criar SLA mínimo de resposta (ex: "Respondemos em até 24h úteis")

**Código do botão WhatsApp (adicionar antes de `</body>` na landing):**
```html
<!-- WhatsApp Button -->
<a href="https://wa.me/55SEUNUMERO?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20Invexa!"
   target="_blank"
   style="position:fixed;bottom:24px;right:24px;z-index:9999;
          background:#25D366;border-radius:50%;width:56px;height:56px;
          display:flex;align-items:center;justify-content:center;
          box-shadow:0 4px 12px rgba(0,0,0,0.2);text-decoration:none">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967..."/>
  </svg>
</a>
```

---

### 2.4 — Plano Free / tabela de comparação clara

- **Prioridade:** 🟠 Alta
- **Esforço:** Médio (~4h)
- **Impacto:** Alto — remove objeção no topo do funil
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Decidir estratégia: Freemium permanente OU Trial mais longo (30 dias)
- [ ] **Opção A (Freemium):** Adicionar plano Free com limites claros:
  - Até 30 produtos
  - Até 50 clientes
  - 1 usuário
  - Módulos básicos (sem relatórios avançados)
- [ ] **Opção B (Trial 30 dias):** Estender trial e destacar mais agressivamente
- [ ] Atualizar tabela de pricing com comparação linha a linha de todos os limites
- [ ] Garantir que `PlanLimitService` (ou equivalente) tenha os novos limites configurados

---

## 🟡 FASE 3 — Conformidade & Segurança (Semanas 6–8)

---

### 3.1 — LGPD: Política de Privacidade e Termos de Uso

- **Prioridade:** 🟡 Média-alta
- **Esforço:** Médio (~1 dia)
- **Impacto:** Legal + converte clientes corporativos
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Gerar base via iubenda.com (plano starter ~R$79/ano) ou termly.io
- [ ] Revisar e personalizar para o contexto do Invexa (dados financeiros, multi-tenant)
- [ ] Criar rotas públicas: `/privacy` e `/terms`
- [ ] Adicionar links no footer da landing e do app
- [ ] Adicionar checkbox de aceite nos termos no formulário de registro:
  ```html
  <input type="checkbox" required name="terms_accepted">
  Aceito os <a href="/terms">Termos de Uso</a> e a
  <a href="/privacy">Política de Privacidade</a>
  ```
- [ ] Salvar `terms_accepted_at` no banco (campo na tabela `users`)
- [ ] Documentar processo de exclusão de dados (direito ao esquecimento — LGPD Art. 18)

---

### 3.2 — Monitoramento de erros em produção (Sentry / Flare)

- **Prioridade:** 🟡 Média-alta
- **Esforço:** Baixo (~2h)
- **Impacto:** Alto para operação — sem isso você está cego em produção
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Criar conta no **Flare** (nativo para Laravel, plano free disponível): `flareapp.io`
- [ ] Instalar: `composer require facade/flare`
- [ ] Configurar no `.env`:
  ```
  FLARE_KEY=sua_chave_aqui
  ```
- [ ] Verificar que `APP_DEBUG=false` em produção (erros não expostos ao usuário)
- [ ] Configurar alertas por e-mail para erros novos
- [ ] Alternativa: Sentry (mais robusto, free tier generoso): `composer require sentry/sentry-laravel`

---

### 3.3 — Audit Log completo

- **Prioridade:** 🟡 Média
- **Esforço:** Alto (~2–3 dias)
- **Impacto:** Conformidade financeira + suporte ao cliente
- **Status:** 🔲 Em andamento (estrutura criada)

**O que fazer:**
- [ ] Definir quais eventos devem ser logados (prioridade máxima):
  - Criação/edição/exclusão de Vendas
  - Baixa de Contas a Pagar e Receber
  - Criação/cancelamento de Assinaturas
  - Login/logout / falhas de autenticação
  - Criação/exclusão de Usuários
  - Impersonation (início e fim)
- [ ] Implementar `AuditLog::record()` nos Controllers críticos
- [ ] Estrutura sugerida para cada log:
  ```php
  AuditLog::create([
      'company_id'  => auth()->user()->company_id,
      'user_id'     => auth()->id(),
      'action'      => 'sale.created',    // entidade.ação
      'entity_type' => 'Sale',
      'entity_id'   => $sale->id,
      'old_values'  => null,
      'new_values'  => $sale->toArray(),
      'ip_address'  => request()->ip(),
  ]);
  ```
- [ ] Criar view no painel Admin para consultar logs por empresa
- [ ] Criar view no painel Super-Admin para logs globais
- [ ] Definir política de retenção de logs (sugestão: 12 meses)

---

## 🟢 FASE 4 — Qualidade Técnica (Semanas 9–12)

---

### 4.1 — Suíte de testes expandida

- **Prioridade:** 🟢 Importante
- **Esforço:** Alto (~1 semana)
- **Impacto:** Segurança para evoluir o produto sem quebrar o que existe
- **Status:** 🔲 Pendente

**Meta de cobertura mínima por área:**

| Área | Testes mínimos | Prioridade |
|------|---------------|-----------|
| Autenticação (login, 2FA, throttle) | 5 | 🔴 Alta |
| Multi-tenancy (isolamento de company_id) | 4 | 🔴 Alta |
| RBAC (acesso por papel por rota) | 6 | 🔴 Alta |
| Vendas (criar, cancelar, PDF) | 4 | 🟠 Alta |
| Financeiro (baixa individual e lote) | 4 | 🟠 Alta |
| Stripe Webhook (checkout, cancel, renewal) | 3 | 🟠 Alta |
| Estoque (entrada, saída, alerta mínimo) | 3 | 🟡 Média |
| Onboarding (fluxo completo, skip) | 2 | 🟡 Média |
| Relatórios (geração PDF/CSV) | 3 | 🟡 Média |

**Arquivos a criar:**
```
tests/Feature/Auth/AuthenticationTest.php
tests/Feature/Auth/TwoFactorTest.php
tests/Feature/MultiTenancy/DataIsolationTest.php
tests/Feature/RBAC/RoleAccessTest.php
tests/Feature/Sales/SaleFlowTest.php
tests/Feature/Financial/BillTest.php
tests/Feature/Financial/ReceivableTest.php
tests/Feature/Billing/StripeWebhookTest.php
tests/Feature/Stock/StockMovementTest.php
```

---

### 4.2 — Observabilidade de performance (Laravel Telescope)

- **Prioridade:** 🟢 Importante
- **Esforço:** Baixo (~2h)
- **Impacto:** Identificar queries lentas e N+1 antes de virarem problema
- **Status:** 🔲 Pendente

**O que fazer:**
- [ ] Instalar Telescope apenas em staging/dev: `composer require laravel/telescope --dev`
- [ ] Proteger rota `/telescope` com middleware de autenticação
- [ ] Monitorar: queries lentas (>100ms), jobs falhos, e-mails enviados
- [ ] Identificar e corrigir top 5 queries mais lentas por módulo
- [ ] Adicionar índices necessários nas migrations (verificar `company_id` + `created_at` em tabelas grandes)

---

### 4.3 — Migração de queue driver para Redis

- **Prioridade:** 🟢 Importante (futuro)
- **Esforço:** Baixo (~3h)
- **Impacto:** Escalabilidade de jobs e notificações
- **Status:** 🔲 Pendente (quando atingir >100 usuários ativos)

**O que fazer:**
- [ ] Instalar Redis no VPS: `apt install redis-server`
- [ ] Instalar predis: `composer require predis/predis`
- [ ] Atualizar `.env`: `QUEUE_CONNECTION=redis` e `CACHE_DRIVER=redis`
- [ ] Rodar `php artisan queue:work` via Supervisor para resiliência
- [ ] Configurar Supervisor para reiniciar workers automaticamente

---

## 🔵 FASE 5 — Diferenciação de Mercado (Mês 4–6)

> Features que transformam o Invexa de um bom ERP em um ERP difícil de substituir.

---

### 5.1 — Integração Pix (Asaas ou Pagar.me)

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Alto (~2 semanas)
- **Impacto:** Altíssimo — feature mais solicitada por PMEs brasileiras
- **Status:** 🔲 Planejado

**O que fazer:**
- [ ] Avaliar gateway: **Asaas** (melhor DX para Laravel no Brasil) ou **Pagar.me**
- [ ] Criar conta Asaas e obter chaves de API
- [ ] Instalar SDK: `composer require asaas/asaas-sdk-php` ou implementar via HTTP
- [ ] Implementar `PixPaymentService`:
  - Gerar cobrança Pix na finalização de venda
  - Retornar QR Code e copia-e-cola
  - Webhook Asaas para confirmação de pagamento
  - Baixar automaticamente Conta a Receber ao confirmar Pix
- [ ] Adicionar campo `payment_method` e `pix_charge_id` na tabela `sales`
- [ ] Exibir QR Code no PDV e na nota/invoice PDF
- [ ] Testar fluxo completo: venda → Pix → confirmação automática → baixa AR

---

### 5.2 — NF-e / NFS-e integrada

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Muito alto (~4–6 semanas)
- **Impacto:** Altíssimo — principal driver de upgrade e retenção
- **Status:** 🔲 Planejado

**O que fazer:**
- [ ] Avaliar API de NF-e: **Focus NFe** (mais simples), **eNotas** ou **NFEio**
- [ ] Criar conta e obter credenciais de homologação
- [ ] Implementar `InvoiceService` para NF-e (produtos) e NFS-e (serviços):
  - Emitir NF-e ao concluir venda (opcional ou automático)
  - Download do XML e DANFE em PDF
  - Cancelamento de NF-e
  - Carta de correção
- [ ] Adicionar campos fiscais nos cadastros:
  - Produto: NCM, CFOP, CST, alíquotas ICMS/PIS/COFINS
  - Cliente: CPF/CNPJ, inscrição estadual
  - Empresa: CNPJ, IE, regime tributário, série NF
- [ ] Adicionar módulo "Fiscal" no menu (gerente+)
- [ ] Testes extensivos em homologação antes de produção
- [ ] Documentar configuração fiscal por estado

---

### 5.3 — App mobile (PWA)

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Médio (~1 semana)
- **Impacto:** Médio-alto — melhora retenção e uso diário
- **Status:** 🔲 Planejado

**O que fazer:**
- [ ] Adicionar `manifest.json` com ícones do Invexa
- [ ] Implementar Service Worker básico para cache offline de assets
- [ ] Adicionar meta tags PWA no layout Blade principal:
  ```html
  <meta name="theme-color" content="#1D9E75">
  <link rel="manifest" href="/manifest.json">
  <meta name="apple-mobile-web-app-capable" content="yes">
  ```
- [ ] Testar "Adicionar à tela inicial" em Android e iOS
- [ ] Otimizar as telas mais usadas no mobile: Dashboard, Nova Venda, Estoque

---

### 5.4 — API pública documentada

- **Prioridade:** 🔵 Estratégica
- **Esforço:** Alto (~2–3 semanas)
- **Impacto:** Abre ecossistema de integrações e aumenta ticket médio
- **Status:** 🔲 Planejado (tokens Sanctum já implementados)

**O que fazer:**
- [ ] Criar `routes/api.php` com endpoints RESTful para módulos principais
- [ ] Documentar com Swagger/OpenAPI via `darkaonline/l5-swagger`
- [ ] Endpoints mínimos para v1 da API:
  - `GET /api/v1/products`
  - `POST /api/v1/sales`
  - `GET /api/v1/reports/summary`
  - `GET /api/v1/customers`
- [ ] Rate limiting específico para API (diferente do web)
- [ ] Publicar documentação em `docs.invexa.com.br`

---

## 📋 Checklist de Status Geral

> Atualizar conforme cada item for concluído.

### Fase 1 — Credibilidade & Estabilidade
- [ ] 1.1 Domínio próprio em produção
- [ ] 1.2 Backup automatizado e testado
- [ ] 1.3 SESSION_SECURE_COOKIE corrigido

### Fase 2 — Produto & Conversão
- [ ] 2.1 Screenshots reais na landing page
- [ ] 2.2 Depoimentos com credibilidade
- [ ] 2.3 Canal de suporte (WhatsApp / Chat)
- [ ] 2.4 Plano Free / tabela de comparação clara

### Fase 3 — Conformidade & Segurança
- [ ] 3.1 LGPD: Política de Privacidade e Termos de Uso
- [ ] 3.2 Monitoramento de erros (Sentry / Flare)
- [ ] 3.3 Audit Log completo

### Fase 4 — Qualidade Técnica
- [ ] 4.1 Suíte de testes expandida
- [ ] 4.2 Observabilidade (Laravel Telescope)
- [ ] 4.3 Migração para Redis (quando necessário)

### Fase 5 — Diferenciação de Mercado
- [ ] 5.1 Integração Pix (Asaas / Pagar.me)
- [ ] 5.2 NF-e / NFS-e integrada
- [ ] 5.3 App mobile (PWA)
- [ ] 5.4 API pública documentada

---

## 🧠 Decisões Pendentes

> Registrar decisões estratégicas que precisam ser tomadas antes de implementar.

| # | Decisão | Opções | Prazo | Status |
|---|---------|--------|-------|--------|
| D1 | Estratégia de aquisição: Freemium ou Trial longo? | Free permanente c/ limites vs Trial 30 dias | Fase 2 | ⏳ Pendente |
| D2 | Gateway de pagamento nacional | Asaas vs Pagar.me vs Stripe+Pix | Fase 5 | ⏳ Pendente |
| D3 | API de NF-e | Focus NFe vs eNotas vs NFEio | Fase 5 | ⏳ Pendente |
| D4 | Monitoramento de erros | Flare (Laravel-nativo) vs Sentry | Fase 3 | ⏳ Pendente |

---

## 📈 Métricas de Sucesso por Fase

| Fase | Métrica-chave | Meta |
|------|--------------|------|
| Fase 1 | Domínio próprio no ar | ✓ / ✗ |
| Fase 2 | Taxa de conversão landing → trial | > 5% |
| Fase 3 | Uptime em produção | > 99.5% |
| Fase 4 | Cobertura de testes (controllers críticos) | > 60% |
| Fase 5 | MRR após 6 meses | Definir meta |

---

## 🔗 Referências Úteis

| Recurso | URL |
|---------|-----|
| Flare (monitoramento Laravel) | https://flareapp.io |
| Sentry para Laravel | https://docs.sentry.io/platforms/php/guides/laravel |
| Focus NFe API | https://focusnfe.com.br |
| eNotas API | https://enotas.com.br |
| Asaas (Pix/boleto BR) | https://asaas.com/developers |
| Pagar.me | https://pagar.me/developers |
| iubenda (LGPD) | https://iubenda.com |
| Laravel Telescope | https://laravel.com/docs/telescope |
| Laravel Cashier (Stripe) | https://laravel.com/docs/billing |
| shots.so (screenshots) | https://shots.so |

---

*Documento gerado em Junho 2026 — Invexa v1.0 em produção*
*Repositório: https://github.com/AugustoCastilhoDev/invexa*
