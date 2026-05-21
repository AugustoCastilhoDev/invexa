# Invexa — Próximos Passos
> Sessão anterior: **21/05/2026** · Baseado em auditoria real do repositório

---

## ✅ Concluído na última sessão

- PDF de estoque: campo `quantity` corrigido (estava `current_stock` / `stock_quantity`)
- Relatório de devoluções (tela): valor `price` corrigido + layout no padrão do app
- PDF de devoluções: `unit_price → price` + status em português com badge correto
- Dashboard: filter-bar alinhada (`Hoje` / `7 dias` / `Este mês` + filtro de datas)
- `.gitignore`: `stripe.exe` adicionado
- Criado `invexa-next-steps.md` com auditoria completa do projeto

---

## 🔴 IMEDIATO — Fazer primeiro (baixo esforço, alto impacto)

### 1. Limites de plano nos Controllers
**Problema:** `canAddProduct()`, `canAddUser()`, `canAddCustomer()` existem no `Company` model mas **nenhum controller os chama**. Usuário Free cria recursos ilimitados.

**Arquivos:**
- `app/Http/Controllers/ProductController.php` → método `store()`
- `app/Http/Controllers/UserController.php` → método `store()`
- `app/Http/Controllers/CustomerController.php` → método `store()`
- `app/Http/Controllers/SupplierController.php` → método `store()`

**Código a adicionar no início de cada `store()`:**
```php
$company = auth()->user()->company;
if (! $company->canAddProduct()) {
    return back()->with('error', 'Limite do plano atingido. Faça upgrade para continuar.');
}
```

---

### 2. `$totalValue` zerado no Relatório de Devoluções (KPI)
**Problema:** O KPI "Valor Devolvido" na tela e no PDF usa `$totalValue` calculado no `ReportController`. Pode estar somando campo `total` inexistente na `SaleReturn`.

**Arquivo:** `app/Http/Controllers/ReportController.php` → método `returns()`

**Verificar e corrigir:**
```php
// ERRADO (se SaleReturn não tiver campo 'total' populado)
$totalValue = $returns->sum('total');

// CORRETO
$totalValue = $returns->flatMap->items->sum(fn($i) => $i->quantity * $i->price);

// E também o $totalItems:
$totalItems = $returns->flatMap->items->sum('quantity');
```

---

### 3. Rate Limiting no Login
**Problema:** Rota de login sem throttle — vulnerável a brute force.

**Arquivo:** `routes/web.php`

**Adicionar:**
```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');
```

---

### 4. Scheduler `CheckFinancialAlerts`
**Problema:** `app/Console/Commands/CheckFinancialAlerts.php` existe mas pode não estar registrado no scheduler.

**Arquivo:** `routes/console.php`

**Verificar/adicionar:**
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('financial:alerts')->dailyAt('08:00');
```

**Testar:**
```bash
php artisan schedule:list
php artisan schedule:test financial:alerts
```

---

## 🟠 CURTO PRAZO — Próximas 1–2 sessões

### 5. Upload de Logo da Empresa
**Problema:** Campo `logo` existe na tabela `companies` mas o upload não está implementado no `CompanyProfileController.php`.

**O que fazer:**
- Adicionar input `file` na view `resources/views/settings/company.blade.php`
- `CompanyProfileController@update`:
```php
if ($request->hasFile('logo')) {
    $path = $request->file('logo')->store('logos/' . $company->id, 'public');
    $company->update(['logo' => $path]);
}
```
- Exibir logo no `layouts/app.blade.php` (sidebar/topbar)
- Substituir texto estático nos PDFs pelo logo da empresa

---

### 6. E-mail Transacional
**Problema:** `.env` usa `MAIL_MAILER=log` — nenhum e-mail é enviado em produção.

**O que fazer:**
1. Configurar **Resend** (recomendado Brasil) no `.env` de produção:
```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@invexa.com.br
MAIL_FROM_NAME=Invexa
RESEND_API_KEY=re_xxxxxxxx
```
2. Verificar/criar Mailables em `app/Mail/`:
   - `WelcomeMail.php` — disparar no registro (`AuthController@register`)
   - `TrialEndingMail.php` — disparar 3 dias antes do trial expirar
   - `OverdueBillMail.php` — integrar com `CheckFinancialAlerts`
   - `PaymentFailedMail.php` — disparar no webhook Stripe

---

### 7. Landing Page `/` — View
**Problema:** `LandingController.php` existe mas não há view associada. A rota `/` pode estar redirecionando para login.

**O que criar:**
- `resources/views/landing/index.blade.php`:
  - Hero com CTA "Comece grátis por 14 dias"
  - Seção de features (Vendas, Estoque, Financeiro, Relatórios)
  - Tabela comparativa de planos (Free / Pro / Business)
  - Footer com e-mail de contato
- Garantir rota pública em `routes/web.php`:
```php
Route::get('/', [LandingController::class, 'index'])->name('landing');
```
- SEO: `<title>`, `<meta description>`, Open Graph

---

### 8. Painel Super-Admin — Views
**Problema:** `app/Http/Controllers/SuperAdmin/` existe mas as views precisam ser verificadas.

**O que verificar/criar:**
- `resources/views/admin/dashboard.blade.php` — MRR, total empresas, novos usuários, churn
- `resources/views/admin/companies/index.blade.php` — listagem com plano, status, usuários
- Ação de ativar/desativar empresa manualmente
- Migration para role `superadmin`:
```bash
php artisan make:migration add_superadmin_to_users_role
```

---

## 🟡 MÉDIO PRAZO — Para monetizar

### 9. Trial Period (`trial_ends_at`)
**O que fazer:**
```bash
php artisan make:migration add_trial_ends_at_to_companies_table
```
```php
// Na migration:
$table->timestamp('trial_ends_at')->nullable()->after('plan');

// No AuthController@register, ao criar a empresa:
$company->update(['trial_ends_at' => now()->addDays(14)]);
```
- Criar `app/Http/Middleware/CheckSubscription.php`
- Registrar middleware em `bootstrap/app.php`
- Criar banner de aviso nos últimos 7 dias: `resources/views/partials/trial-banner.blade.php`

---

### 10. Stripe — Integração Completa
**Situação:** Controllers e webhook já existem. Verificar o que falta:

```bash
# Verificar migrations existentes
php artisan migrate:status | grep subscription
php artisan migrate:status | grep trial
```

**Possíveis lacunas:**
- Migration `create_subscriptions_table` (se ausente)
- View `resources/views/subscription/upgrade.blade.php`
- View `resources/views/subscription/index.blade.php` (plano atual + histórico)
- Webhook events a implementar: `invoice.payment_failed`, `customer.subscription.deleted`

---

### 11. Onboarding Wizard
**Situação:** `OnboardingController.php` existe. Verificar views.

**O que verificar/criar em `resources/views/onboarding/`:**
- `step1.blade.php` — Dados da empresa (nome, CNPJ, logo)
- `step2.blade.php` — Criar primeiro produto
- `step3.blade.php` — Criar primeiro cliente
- Checklist no dashboard: "Complete seu perfil — X de 5 etapas"
- Migration: `add_onboarding_completed_at_to_companies_table`

---

## 📦 LONGO PRAZO

### 12. 2FA
```bash
composer require laravel/fortify
```
- Habilitar `Features::twoFactorAuthentication()` em `config/fortify.php`
- View `/settings/security` com QR Code (Google Authenticator / Authy)

### 13. API REST + Sanctum
- Rotas em `routes/api.php`: `/api/v1/products`, `/api/v1/sales`, `/api/v1/customers`
- Tela de geração de tokens: `/settings/api`
- Documentação:
```bash
composer require knuckleswtf/scribe
```

### 14. Exportação de Dados (LGPD)
- Job assíncrono `ExportCompanyData` → ZIP com CSVs de todas as tabelas
- Queue driver `database` já configurado — só criar o Job
- Notificação interna quando pronto para download

### 15. Testes Automatizados Expandidos
```bash
php artisan test --coverage
```
- Teste de isolamento multitenant
- Teste de limite de plano (ProductController retorna 403)
- Teste de trial expirado
- Teste de webhook Stripe

---

## 🔧 Comandos úteis para retomar

```bash
# Atualizar código
git pull origin main

# Limpar caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Verificar scheduler
php artisan schedule:list

# Verificar migrations pendentes
php artisan migrate:status

# Verificar se stripe.exe ainda está no repositório
git ls-files stripe.exe
# Se retornar algo, remover:
git rm --cached stripe.exe && git commit -m "chore: remove stripe.exe"

# Rodar testes
php artisan test
```

---

## 📊 Tabela de Prioridades

| # | Item | Esforço | Impacto | Quando |
|---|------|---------|---------|--------|
| 1 | Limites de plano nos Controllers | 🟢 Baixo | 🔴 Alto | Imediato |
| 2 | `$totalValue` devoluções (ReportController) | 🟢 Baixo | 🔴 Alto | Imediato |
| 3 | Rate limiting no login | 🟢 Baixo | 🔴 Alto | Imediato |
| 4 | Scheduler `CheckFinancialAlerts` | 🟢 Baixo | 🟠 Médio | Imediato |
| 5 | Upload de logo + PDFs com logo | 🟡 Médio | 🟠 Médio | Curto prazo |
| 6 | E-mail transacional (Resend) | 🟡 Médio | 🔴 Alto | Curto prazo |
| 7 | Landing page `/` (view) | 🟡 Médio | 🔴 Alto | Curto prazo |
| 8 | Painel Super-Admin (views) | 🟡 Médio | 🟠 Médio | Curto prazo |
| 9 | Trial `trial_ends_at` + middleware + banner | 🟡 Médio | 🔴 Alto | Médio prazo |
| 10 | Stripe — pagamento real funcional | 🔴 Alto | 🔴 Crítico | Médio prazo |
| 11 | Onboarding wizard (views) | 🔴 Alto | 🟠 Médio | Médio prazo |
| 12 | Testes automatizados expandidos | 🔴 Alto | 🟠 Médio | Médio prazo |
| 13 | 2FA | 🟡 Médio | 🟡 Baixo | Longo prazo |
| 14 | API REST + Sanctum | 🔴 Alto | 🟡 Baixo | Longo prazo |
| 15 | Exportação LGPD | 🟡 Médio | 🟡 Baixo | Longo prazo |

---

*Gerado em 21/05/2026 · Repositório: `AugustoCastilhoDev/invexa`*
