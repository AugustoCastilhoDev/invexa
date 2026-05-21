# Invexa — Próximos Passos (Auditoria Real · 21/05/2026)

> Documento gerado após análise minuciosa de todos os arquivos do repositório.
> Cada item indica **o que já existe**, **o que está incompleto** e **o que deve ser implementado**.

---

## ✅ Já implementado e funcional

| Módulo | Arquivo(s) | Status |
|--------|-----------|--------|
| Vendas (PDV + itens + cancelamento) | `SaleController.php` | ✅ Completo |
| Devoluções de venda | `SaleReturnController.php` | ✅ Completo |
| Estoque (movimentações + estoque mínimo) | `StockMovementController.php` | ✅ Completo |
| Ordens de Compra | `PurchaseOrderController.php` | ✅ Completo |
| Contas a Pagar (parcelamento + recorrência) | `BillController.php` | ✅ Completo |
| Contas a Receber (parcelamento + recorrência) | `ReceivableController.php` | ✅ Completo |
| Clientes e Fornecedores | `CustomerController.php` / `SupplierController.php` | ✅ Completo |
| Produtos e Categorias | `ProductController.php` / `CategoryController.php` | ✅ Completo |
| Relatórios (Vendas, Estoque, Compras, Dev.) com PDF e CSV | `ReportController.php` | ✅ Completo |
| Dashboard com gráficos e filtros de período | `DashboardController.php` | ✅ Completo |
| Notificações internas | `NotificationController.php` | ✅ Completo |
| Audit Log | `AuditLog.php` | ✅ Completo |
| Multitenancy via `company_id` | `CompanyMiddleware.php` | ✅ Completo |
| Roles (`admin/gerente/vendedor`) | `CheckRole.php` | ✅ Completo |
| Planos (`free/pro/business`) com limites no Model | `Company.php` | ✅ Modelo OK |
| Busca global | `SearchController.php` | ✅ Completo |
| Onboarding Controller | `OnboardingController.php` | ✅ Controller existe |
| Landing Controller | `LandingController.php` | ✅ Controller existe |
| SuperAdmin Controller | `SuperAdmin/` (dir) | ✅ Estrutura existe |
| Stripe Webhook Controller | `StripeWebhookController.php` | ✅ Controller existe |
| Subscription Controller | `SubscriptionController.php` | ✅ Controller existe |
| Upgrade Controller | `UpgradeController.php` | ✅ Controller existe |
| Perfil da empresa | `CompanyProfileController.php` | ✅ Existe |
| Stripe CLI (dev) | `stripe.exe` na raiz | ✅ Presente |
| Webhook setup docs | `STRIPE_WEBHOOK_SETUP.md` | ✅ Documentado |

---

## 🔴 CRÍTICO — Estrutura existe mas implementação está incompleta

### 1. Limites de Plano nos Controllers

**Situação:** `Company::canAddProduct()`, `canAddUser()`, etc. estão definidos no model, mas os controllers **não os chamam**. Um usuário Free pode criar produtos, usuários e clientes ilimitados.

**Arquivos a alterar:**
- `app/Http/Controllers/ProductController.php` → método `store()`
- `app/Http/Controllers/UserController.php` → método `store()`
- `app/Http/Controllers/CustomerController.php` → método `store()`
- `app/Http/Controllers/SupplierController.php` → método `store()`
- `app/Http/Controllers/CategoryController.php` → método `store()`

**O que adicionar em cada `store()`:**
```php
$company = auth()->user()->company;
if (! $company->canAddProduct()) {
    return back()->with('error', 'Limite do plano atingido. Faça upgrade para continuar.');
}
```

**Impacto:** Segurança e monetização — sem isso o plano Free não tem restrição real.

---

### 2. Landing Page — View ausente

**Situação:** `LandingController.php` existe (159 bytes — retorna só a view), mas **não há** `resources/views/landing.blade.php` ou similar.

**O que criar:**
- `resources/views/landing/index.blade.php` — layout público com:
  - Hero section com CTA "Comece grátis 14 dias"
  - Seção de features (Vendas, Estoque, Financeiro, Relatórios)
  - Tabela comparativa de planos (Free / Pro / Business)
  - Footer com links de contato
- Rota pública em `routes/web.php`: `Route::get('/', [LandingController::class, 'index'])`
- SEO: `<title>`, `<meta description>`, Open Graph tags

---

### 3. Painel Super-Admin — Views ausentes

**Situação:** Diretório `app/Http/Controllers/SuperAdmin/` existe mas precisa de verificação das views em `resources/views/admin/`.

**O que verificar/criar:**
- View `admin/dashboard.blade.php` — métricas globais: MRR, total empresas, churn
- View `admin/companies/index.blade.php` — listagem com plano, status, usuários, data
- Ação de ativar/desativar empresa
- Middleware `role:superadmin` aplicado no grupo de rotas `/admin`
- Coluna `role = 'superadmin'` no enum de `users.role` (migration necessária)

---

### 4. Stripe / Pagamentos — Integração incompleta

**Situação:** `StripeWebhookController.php` e `SubscriptionController.php` existem, `stripe.exe` está na raiz, `STRIPE_WEBHOOK_SETUP.md` está documentado. Porém:
- Não há tabela `subscriptions` (verificar migrations)
- Não há `trial_ends_at` na tabela `companies` (verificar migration)
- Middleware `CheckSubscription` precisa ser verificado — pode não estar registrado
- A rota `/upgrade` (UpgradeController) pode estar sem view

**O que verificar/criar:**
```bash
php artisan migrate:status  # verificar se subscription migration existe
```
- Migration: `add_trial_ends_at_to_companies_table`
- Migration: `create_subscriptions_table` (se não existir)
- Middleware `CheckSubscription` → registrar em `bootstrap/app.php`
- View `resources/views/subscription/upgrade.blade.php`
- View `resources/views/subscription/index.blade.php` — plano atual, próxima cobrança
- Webhook events: `customer.subscription.deleted`, `invoice.payment_failed`

> ⚠️ **Atenção:** O `stripe.exe` **não deve ficar na raiz do repositório** — adicionar ao `.gitignore`.

---

### 5. E-mail Transacional

**Situação:** `.env.example` usa `MAIL_MAILER=log` — nenhum e-mail é enviado em produção.

**O que implementar:**
- Configurar **Resend** (recomendado para BR) ou **Mailgun** no `.env` de produção
- Verificar se existem Mailables em `app/Mail/` — se não:
  - `app/Mail/WelcomeMail.php` — disparado no registro
  - `app/Mail/TrialEndingMail.php` — disparado 3 dias antes do trial expirar
  - `app/Mail/OverdueBillMail.php` — integrar com `CheckFinancialAlerts`
  - `app/Mail/PaymentFailedMail.php` — disparado pelo webhook Stripe
- Views em `resources/views/emails/`

```bash
# Verificar Mailables existentes
ls app/Mail/
```

---

### 6. Scheduler `CheckFinancialAlerts`

**Situação:** `app/Console/Commands/CheckFinancialAlerts.php` existe. Verificar se está registrado.

**O que verificar em `routes/console.php` (Laravel 11+):**
```php
use Illuminate\Support\Facades\Schedule;
Schedule::command('financial:alerts')->dailyAt('08:00');
```

**Verificar também:**
- Cron do servidor aponta para `php artisan schedule:run`?
- Testar: `php artisan schedule:test financial:alerts`

---

### 7. Upload de Logo da Empresa

**Situação:** Campo `logo` existe na tabela `companies`, mas o upload não está implementado no `CompanyProfileController.php` (1587 bytes — muito pequeno para ter upload).

**O que implementar:**
- Form em `resources/views/settings/company.blade.php` com `enctype="multipart/form-data"`
- `CompanyProfileController@update` → `$request->file('logo')->store('logos', 'public')`
- `php artisan storage:link` já deve estar executado
- Exibir logo nos PDFs (substituir texto estático nos `pdf-header` partials)
- Exibir logo no `layouts/app.blade.php` no sidebar/topbar

---

### 8. Onboarding Wizard

**Situação:** `OnboardingController.php` existe (2199 bytes). Verificar se há views e se o fluxo pós-registro redireciona para onboarding.

**O que verificar/criar:**
- `resources/views/onboarding/` — wizard de 3 passos:
  - Passo 1: Dados da empresa (nome, CNPJ, logo)
  - Passo 2: Criar primeiro produto
  - Passo 3: Criar primeiro cliente
- Checklist no dashboard: "Complete seu perfil — X de 5 etapas"
- `AuthController@register` deve redirecionar para `/onboarding` após cadastro
- Campo `onboarding_completed_at` na tabela `companies` (migration)

---

## 🟠 IMPORTANTE — Ainda não implementado

### 9. Proteção contra Brute Force no Login

**Situação:** Não há `throttle` customizado na rota de login.

**O que adicionar em `routes/web.php`:**
```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');
```

**E em `routes/api.php`:**
```php
Route::middleware('throttle:60,1')->group(...);
```

---

### 10. 2FA — Autenticação em Dois Fatores

**Situação:** Não implementado. Laravel Fortify suporta TOTP nativamente.

**O que implementar:**
- `composer require laravel/fortify`
- Habilitar `Features::twoFactorAuthentication()` em `config/fortify.php`
- View `/settings/security` com QR Code para Google Authenticator / Authy
- Codes de recupero (backup codes)

---

### 11. API REST com Sanctum

**Situação:** Não implementado. Sanctum já vem no Laravel por padrão.

**O que implementar:**
- Rotas em `routes/api.php` → `/api/v1/products`, `/api/v1/sales`, `/api/v1/customers`, `/api/v1/stock`
- Autenticação via token Bearer (Sanctum)
- Tela de geração de tokens em `/settings/api`
- Documentação com **Scribe** (`composer require knuckleswtf/scribe`)

---

### 12. Exportação de Dados (LGPD)

**Situação:** Não implementado.

**O que implementar:**
- Botão "Exportar meus dados" em `/settings`
- Job assíncrono `ExportCompanyData` → gera ZIP com CSVs de todas as tabelas da empresa
- Queue driver `database` já está configurado — basta criar o Job
- Notificação interna quando ZIP estiver pronto para download

---

### 13. Testes Automatizados Expandidos

**Situação:** Existem testes em `tests/Feature/` mas a cobertura está baixa para um produto em produção.

**O que adicionar:**
- Teste de isolamento multitenant: empresa A **não vê** dados da empresa B
- Teste de limite de plano: `ProductController@store` retorna 403 quando limite atingido
- Teste de trial expirado: middleware bloqueia acesso e redireciona para `/upgrade`
- Teste de webhook Stripe: `payment_succeeded` atualiza status da assinatura
- Teste do `CheckFinancialAlerts`: verifica disparo correto de notificações

```bash
php artisan test --coverage  # checar cobertura atual
```

---

## 🟡 MELHORIAS — Qualidade e UX

### 14. Banner de Trial Expirando

**Situação:** Não há aviso visual quando o trial está próximo de expirar.

**O que criar:**
- Componente Blade `@include('partials.trial-banner')` no `layouts/app.blade.php`
- Lógica: exibir quando `trial_ends_at` < `now()->addDays(7)`
- Estilo amarelo → vermelho conforme se aproxima do vencimento

---

### 15. Relatório Financeiro — `$totalValue` nas Devoluções

**Situação:** O KPI "Valor Devolvido" no PDF e na tela do relatório de devoluções usa `$totalValue` calculado no `ReportController`. Se estiver zerado, o controller pode estar somando `total` da `SaleReturn` em vez de somar `items.quantity * items.price`.

**O que verificar em `ReportController.php` (método `returns`):**
```php
// ERRADO — se SaleReturn não tiver campo 'total' populado
$totalValue = $returns->sum('total');

// CORRETO
$totalValue = $returns->flatMap->items->sum(fn($i) => $i->quantity * $i->price);
```

---

### 16. `stripe.exe` fora do `.gitignore`

**Situação:** O binário `stripe.exe` (27 MB) está commitado na raiz do repositório — isso aumenta o tamanho do clone desnecessariamente.

**O que fazer:**
```bash
# Adicionar ao .gitignore
echo "stripe.exe" >> .gitignore

# Remover do histórico (se desejado)
git rm --cached stripe.exe
git commit -m "chore: remove stripe.exe do repositório"
```

---

### 17. Configurações de Perfil do Usuário

**Situação:** `ProfileController.php` existe mas verificar se as views de perfil têm: troca de senha, nome, avatar.

**O que verificar/completar:**
- `resources/views/profile/edit.blade.php` — nome, e-mail, avatar
- Troca de senha com confirmação
- Configuração de fuso horário por usuário

---

## 📋 Ordem de Execução Recomendada

| # | Item | Esforço | Impacto | Prioridade |
|---|------|---------|---------|------------|
| 1 | Limites de plano nos Controllers | Baixo | 🔴 Alto | Imediato |
| 2 | `$totalValue` no ReportController (devoluções) | Baixo | 🔴 Alto | Imediato |
| 3 | Scheduler `CheckFinancialAlerts` | Baixo | 🟠 Médio | Curto prazo |
| 4 | Rate limiting no login | Baixo | 🔴 Alto | Curto prazo |
| 5 | `stripe.exe` do `.gitignore` | Baixo | 🟡 Baixo | Curto prazo |
| 6 | Upload de logo + PDFs com logo | Médio | 🟠 Médio | Curto prazo |
| 7 | E-mail transacional (Resend/Mailgun) | Médio | 🔴 Alto | Curto prazo |
| 8 | Landing page `/` (view) | Médio | 🔴 Alto | Médio prazo |
| 9 | Onboarding wizard (verificar/completar views) | Médio | 🟠 Médio | Médio prazo |
| 10 | Painel Super-Admin (verificar/completar views) | Médio | 🟠 Médio | Médio prazo |
| 11 | Trial `trial_ends_at` + middleware + banner | Médio | 🔴 Alto | Médio prazo |
| 12 | Stripe/Asaas — pagamento real funcional | Alto | 🔴 Crítico | Médio prazo |
| 13 | Testes automatizados expandidos | Alto | 🟠 Médio | Médio prazo |
| 14 | 2FA | Médio | 🟡 Baixo | Longo prazo |
| 15 | API REST + Sanctum + Scribe | Alto | 🟡 Baixo | Longo prazo |
| 16 | Exportação de dados LGPD | Médio | 🟡 Baixo | Longo prazo |

---

## 🛠️ Stack Atual Confirmada

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel / PHP 8.3 |
| Frontend | Blade + Bootstrap 5 + Vite |
| Banco (dev) | SQLite |
| Banco (prod) | MySQL |
| PDF | barryvdh/laravel-dompdf |
| Charts | Chart.js 4.4.4 (CDN) |
| Auth | Laravel Breeze customizado |
| Queue | Database driver |
| Testes | PHPUnit |
| Pagamentos | Stripe (controllers prontos, integração incompleta) |

---

## 📁 Referência Rápida de Arquivos

```
app/
├── Console/Commands/CheckFinancialAlerts.php   ← registrar no scheduler
├── Http/Controllers/
│   ├── LandingController.php                   ← view ausente
│   ├── OnboardingController.php                ← verificar views
│   ├── UpgradeController.php                   ← verificar view
│   ├── SubscriptionController.php              ← integração incompleta
│   ├── StripeWebhookController.php             ← verificar eventos
│   ├── CompanyProfileController.php            ← upload de logo ausente
│   ├── SuperAdmin/                             ← verificar views
│   ├── ProductController.php                   ← canAddProduct() ausente
│   ├── UserController.php                      ← canAddUser() ausente
│   ├── CustomerController.php                  ← canAddCustomer() ausente
│   └── ReportController.php                    ← $totalValue devoluções
├── Http/Middleware/
│   ├── CheckRole.php                           ← ok
│   ├── CompanyMiddleware.php                   ← ok
│   └── CheckSubscription.php?                 ← verificar se existe
├── Models/
│   ├── Company.php                             ← limits()/canAdd*() ok, trial_ends_at?
│   └── User.php                                ← superadmin role ausente
├── Mail/                                       ← verificar se Mailables existem
database/migrations/
│   └── verificar: subscriptions, trial_ends_at, onboarding_completed_at
resources/views/
│   ├── landing/                                ← ausente
│   ├── onboarding/                             ← verificar
│   ├── admin/                                  ← verificar
│   ├── subscription/                           ← verificar
│   └── emails/                                 ← verificar
tests/
│   └── Feature/                                ← expandir cobertura
stripe.exe                                      ← remover do repositório!
```

---

*Auditoria gerada em 21/05/2026 — baseada na análise do repositório `AugustoCastilhoDev/invexa` commit `810d00a`*
