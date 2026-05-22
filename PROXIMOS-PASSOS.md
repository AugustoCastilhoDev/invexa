# Invexa — Próximos Passos
> Última atualização: **22/05/2026** · Baseado em auditoria completa do repositório

---

## ✅ Concluído até 22/05/2026

- PDF de estoque: campo `quantity` corrigido
- Relatório de devoluções: valor e layout corrigidos
- Dashboard: filter-bar alinhada (Hoje / 7 dias / Este mês + datas customizadas)
- `.gitignore`: `stripe.exe` adicionado
- Rotas API: prefixo `api.` corrigido nos named routes
- PDF produtos mais vendidos: campos `product_name`, `category_name`, `total_qty` corrigidos
- Favicon Invexa: adicionado em todos os 7 PDFs de relatório via partial `pdf-head.blade.php`

---

## 🔴 ALTA PRIORIDADE — Fazer primeiro

### 1. Relatório de Lucratividade
**O que é:** Relatório de margem bruta por produto/período (custo vs. preço de venda).
**Por quê:** Alto valor para o usuário final, implementação rápida no `ReportController` já existente.

**O que criar:**
- Método `profitability(Request $request)` no `ReportController`
- Query: `SaleItem` JOIN `products` → calcular `(unit_price - cost_price) * quantity`
- View `resources/views/reports/profitability.blade.php`
- PDF `resources/views/reports/profitability-pdf.blade.php`
- Exportação CSV
- Rota `reports/lucratividade` → `reports.profitability`
- Card na `reports/index.blade.php`

---

### 2. Controle de Usuários/Operadores por Empresa
**O que é:** Múltiplos usuários por empresa com perfis de acesso (Admin, Gerente, Operador de Caixa, Estoquista).
**Por quê:** Essencial para SaaS B2B — empresas têm mais de um funcionário usando o sistema.

**O que criar:**
- Migration: adicionar coluna `role` em `users` (`admin`, `manager`, `cashier`, `stock`)
- Middleware `CheckRole` para proteger rotas sensíveis
- `UserController` já existe — expandir para gerenciar operadores da mesma empresa
- View `resources/views/users/index.blade.php` com listagem + invite
- Sistema de convite por e-mail (link com token)

---

### 3. Módulo de Orçamentos
**O que é:** Gerar orçamento para cliente que pode ser convertido em venda com 1 clique.
**Por quê:** Diferencial competitivo — muitas empresas precisam cotar antes de vender.

**O que criar:**
```bash
php artisan make:model Quote -m
php artisan make:model QuoteItem -m
php artisan make:controller QuoteController --resource
```
- Tabelas: `quotes` (id, company_id, customer_id, status, valid_until, total) + `quote_items`
- Status: `rascunho` → `enviado` → `aceito` → `recusado` → `expirado`
- Botão "Converter em Venda" no show do orçamento
- PDF do orçamento para enviar ao cliente
- Validade configurável (ex: 7, 15, 30 dias)

---

### 4. Importação de Produtos via CSV
**O que é:** Upload de arquivo CSV para cadastrar/atualizar produtos em lote.
**Por quê:** Elimina atrito no onboarding — clientes com catálogos grandes abandonam sem isso.

**O que criar:**
- Rota `POST /products/import`
- Job `ImportProductsCsv` (queue `database`)
- Validação de cabeçalhos: `name`, `sku`, `price`, `cost_price`, `quantity`, `category`
- Relatório de erros linha a linha
- Template CSV para download
- View modal ou página `resources/views/products/import.blade.php`

---

## 🟠 CURTO PRAZO

### 5. Limites de Plano nos Controllers
**Problema:** `canAddProduct()`, `canAddCustomer()` etc. existem no model `Company` mas nenhum controller os chama.

**Arquivos a corrigir:**
- `ProductController@store`
- `CustomerController@store`
- `UserController@store`
- `SupplierController@store`

```php
$company = auth()->user()->company;
if (! $company->canAddProduct()) {
    return back()->with('error', 'Limite do plano atingido. Faça upgrade para continuar.');
}
```

---

### 6. Rate Limiting no Login
**Problema:** Rota de login sem throttle — vulnerável a brute force.

```php
// routes/web.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');
```

---

### 7. E-mail Transacional
**Problema:** `.env` usa `MAIL_MAILER=log` — nenhum e-mail chega em produção.

**Configurar Resend:**
```env
MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxx
MAIL_FROM_ADDRESS=noreply@invexa.com.br
```

**Mailables a criar:**
- `WelcomeMail` — disparar no registro
- `TrialEndingMail` — 3 dias antes do trial expirar
- `OverdueBillMail` — integrar com `CheckFinancialAlerts`
- `PaymentFailedMail` — webhook Stripe `invoice.payment_failed`

---

### 8. Scheduler `CheckFinancialAlerts`
**Verificar se está registrado:**
```bash
php artisan schedule:list
```

**Adicionar em `routes/console.php` se ausente:**
```php
Schedule::command('financial:alerts')->dailyAt('08:00');
```

---

### 9. Landing Page `/`
**Problema:** `LandingController` existe mas sem view.

**Criar `resources/views/landing/index.blade.php`:**
- Hero com CTA "Comece grátis por 14 dias"
- Seção de features (Vendas, Estoque, Financeiro, Relatórios)
- Tabela comparativa de planos (Free / Pro / Business)
- Footer com e-mail de contato e links legais
- SEO: `<title>`, `<meta description>`, Open Graph

---

### 10. Busca em Tempo Real nas Listagens
**O que é:** Filtro via input com debounce sem recarregar a página (Alpine.js ou JS puro).
**Aplicar em:** Produtos, Clientes, Fornecedores, Vendas.

---

## 🟡 MÉDIO PRAZO

### 11. Trial Period
```bash
php artisan make:migration add_trial_ends_at_to_companies_table
```
- Setar `trial_ends_at = now()->addDays(14)` no registro
- Middleware `CheckSubscription`
- Banner de aviso nos últimos 7 dias

---

### 12. Stripe — Integração Completa
- Verificar migrations de `subscriptions`
- View `subscription/upgrade.blade.php` com planos
- Webhook events: `invoice.payment_failed`, `customer.subscription.deleted`

---

### 13. Histórico de Preços do Produto
- Tabela `product_price_history` (product_id, cost_price, sale_price, changed_at, changed_by)
- Observer no Model `Product` para registrar automaticamente
- Gráfico de evolução de preço na tela de detalhe do produto

---

### 14. Frente de Caixa (PDV Simplificado)
- Tela otimizada para vendas rápidas
- Suporte a leitura de código de barras (campo com auto-foco)
- Atalhos de teclado (F2 = novo item, F10 = finalizar, ESC = cancelar)
- Integrado ao `SaleController` existente

---

### 15. Múltiplos Endereços de Entrega por Cliente
- Tabela `customer_addresses`
- Seleção de endereço na tela de venda

---

## 📦 LONGO PRAZO

### 16. Onboarding Wizard (views)
- `onboarding/step1` — Dados da empresa
- `onboarding/step2` — Primeiro produto
- `onboarding/step3` — Primeiro cliente
- Checklist no dashboard

### 17. Exportação de Dados (LGPD)
- Job `ExportCompanyData` → ZIP com CSVs
- Notificação interna quando pronto

### 18. API REST Pública + Documentação
- Sanctum já instalado
- Documentação com Scribe (`knuckleswtf/scribe`)

### 19. Testes Automatizados
- Isolamento multitenant
- Limites de plano
- Trial expirado
- Webhook Stripe

---

## 📊 Tabela de Prioridades

| # | Item | Esforço | Impacto | Quando |
|---|------|---------|---------|--------|
| 1 | Relatório de Lucratividade | 🟢 Baixo | 🔴 Alto | **Agora** |
| 2 | Controle de Usuários/Operadores | 🟡 Médio | 🔴 Alto | **Agora** |
| 3 | Módulo de Orçamentos | 🔴 Alto | 🔴 Alto | **Agora** |
| 4 | Importação de Produtos CSV | 🟡 Médio | 🔴 Alto | **Agora** |
| 5 | Limites de plano nos Controllers | 🟢 Baixo | 🔴 Alto | Curto prazo |
| 6 | Rate limiting no login | 🟢 Baixo | 🔴 Alto | Curto prazo |
| 7 | E-mail transacional (Resend) | 🟡 Médio | 🔴 Alto | Curto prazo |
| 8 | Scheduler `CheckFinancialAlerts` | 🟢 Baixo | 🟠 Médio | Curto prazo |
| 9 | Landing page `/` | 🟡 Médio | 🔴 Alto | Curto prazo |
| 10 | Busca em tempo real nas listagens | 🟢 Baixo | 🟠 Médio | Curto prazo |
| 11 | Trial `trial_ends_at` + middleware | 🟡 Médio | 🔴 Alto | Médio prazo |
| 12 | Stripe — pagamento real funcional | 🔴 Alto | 🔴 Crítico | Médio prazo |
| 13 | Histórico de preços do produto | 🟢 Baixo | 🟠 Médio | Médio prazo |
| 14 | Frente de Caixa (PDV) | 🔴 Alto | 🔴 Alto | Médio prazo |
| 15 | Múltiplos endereços por cliente | 🟢 Baixo | 🟡 Baixo | Médio prazo |
| 16 | Onboarding wizard | 🔴 Alto | 🟠 Médio | Longo prazo |
| 17 | Exportação LGPD | 🟡 Médio | 🟡 Baixo | Longo prazo |
| 18 | API REST + Documentação | 🔴 Alto | 🟡 Baixo | Longo prazo |
| 19 | Testes automatizados | 🔴 Alto | 🟠 Médio | Longo prazo |

---

## 🔧 Comandos úteis para retomar

```bash
# Atualizar código
git pull origin main

# Limpar caches
php artisan view:clear && php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Verificar scheduler
php artisan schedule:list

# Verificar migrations pendentes
php artisan migrate:status

# Rodar testes
php artisan test
```

---

*Gerado em 22/05/2026 · Repositório: `AugustoCastilhoDev/invexa`*
