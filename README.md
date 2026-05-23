# Invexa — Sistema de Gestão Comercial SaaS

> Aplicação web **SaaS multi-tenant** para gestão completa de estoque, vendas, compras, financeiro e relatórios, desenvolvida com **Laravel 13** e **Bootstrap 5**.

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/status-em%20desenvolvimento-orange?style=flat-square)

---

## Sobre o Projeto

O **Invexa** é um ERP leve voltado a pequenas e médias empresas, distribuído como SaaS com planos **Free / Pro / Business**. A arquitetura multi-tenant garante isolamento total de dados por empresa — todos os recursos são segregados por `company_id`. O controle de acesso é baseado em papéis (`superadmin`, `admin`, `gerente`, `vendedor`), com visibilidades e permissões distintas em toda a interface e nas rotas.

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Chart.js |
| Bundler | Vite |
| Template | Blade (layouts, componentes) |
| Banco de dados | MySQL 8 (SQLite para desenvolvimento local) |
| Geração de PDF | barryvdh/laravel-dompdf 3.x |
| Pagamentos | Laravel Cashier + Stripe |
| Auth | Laravel Breeze customizado + 2FA (TOTP) |
| Filas | Database driver |
| Testes | PHPUnit 12 |
| Dev tools | Laravel Pint, Laravel Pail, Concurrently |

---

## Módulos Implementados

| Módulo | Status |
|---|---|
| Landing Page pública (`/`) | ✅ Completo |
| Página de Pricing (`/pricing`) | ✅ Completo |
| Autenticação & Multi-Tenant | ✅ Completo |
| 2FA — Autenti. em dois fatores (TOTP) | ✅ Completo |
| Onboarding pós-cadastro | ✅ Completo |
| Papéis e Permissões | ✅ Completo |
| Trial Period + middleware de expiração | ✅ Completo |
| Assinatura / Stripe (Cashier) | ✅ Completo |
| Upgrade de plano (`/upgrade`) | ✅ Completo |
| Portal de cobrança Stripe | ✅ Completo |
| Perfil da empresa + upload de logo | ✅ Completo |
| Tokens de API (`/settings/api`) | ✅ Completo |
| Dashboard Analítico | ✅ Completo |
| Produtos & Categorias (+ import CSV) | ✅ Completo |
| Movimentação de Estoque | ✅ Completo |
| Vendas (PDV + itens + numeração) | ✅ Completo |
| Clientes | ✅ Completo |
| Devoluções | ✅ Completo |
| Fornecedores | ✅ Completo |
| Ordens de Compra | ✅ Completo |
| Contas a Pagar (baixa unitária + lote) | ✅ Completo |
| Contas a Receber (baixa unitária + lote) | ✅ Completo |
| Relatório de Vendas (PDF/CSV) | ✅ Completo |
| Relatório Financeiro (PDF/CSV) | ✅ Completo |
| Relatório de Compras (PDF/CSV) | ✅ Completo |
| Relatório de Estoque (PDF/CSV) | ✅ Completo |
| Relatório de Fornecedores (PDF/CSV) | ✅ Completo |
| Relatório de Devoluções (PDF/CSV) | ✅ Completo |
| Relatório de Lucratividade (PDF/CSV) | ✅ Completo |
| Relatório Top Produtos (PDF/CSV) | ✅ Completo |
| Notificações internas | ✅ Completo |
| Gestão de Usuários | ✅ Completo |
| Painel Super-Admin | ✅ Completo |
| Impersonation (suporte por empresa) | ✅ Completo |
| Busca global | ✅ Completo |
| Rate limiting (login + registro) | ✅ Completo |
| Audit Log (estrutura base) | ✅ Estrutura criada |
| Testes automatizados | ✅ Feature + Unit |

---

## Funcionalidades em Detalhe

### Autenticação e Multi-Tenant

- Registro de usuário com criação automática de empresa vinculada
- Login / Logout com sessão segura (CSRF, hashing bcrypt)
- Rate limiting: `throttle:10,1` no login, `throttle:5,1` no registro
- Middleware `company` — bloqueia acesso se o usuário não tiver empresa
- Middleware `trial` — verifica trial ativo ou assinatura válida
- Middleware `onboarding` — redireciona para wizard se não concluído
- Trait `BelongsToCompany` — escopo global de `company_id` em todos os models
- Isolamento total: nenhum usuário acessa dados de outra empresa

### 2FA — Dois Fatores

- Suporte a TOTP (Google Authenticator, Authy)
- QR Code gerado via `settings/security`
- Middleware `TwoFactorMiddleware` redireciona para verificação pós-login
- Habilitar / desabilitar pelo painel de segurança

### Onboarding

- Wizard pós-registro: dados da empresa → primeiro produto → primeiro cliente
- `CheckOnboarding` middleware redireciona automaticamente enquanto não concluído
- Opção de pular o wizard

### Papéis e Permissões

| Papel | Acesso |
|---|---|
| **superadmin** | Painel global do SaaS — gerencia todas as empresas |
| **admin** | Acesso total dentro da empresa, inclui gestão de usuários |
| **gerente** | Estoque, Compras, Financeiro, Relatórios, Vendas (edição incluída) |
| **vendedor** | Dashboard (parcial), Vendas, Clientes e Devoluções |

### Assinaturas e Planos

- Integração com **Stripe via Laravel Cashier**
- Webhook Stripe em `POST /stripe/webhook` (sem CSRF)
- Checkout de plano via `SubscriptionController`
- Portal de cobrança Stripe (gerenciar cartão, cancelar, ver faturas)
- Download de faturas individuais
- Página de upgrade (`/upgrade`) com comparação de planos
- Trial period com middleware de expiração e redirect para `/upgrade`
- Planos: `free`, `pro`, `business`

### Perfil da Empresa

- Edição de nome, e-mail e dados da empresa
- Upload e remoção de logo (`DELETE /settings/company/logo`)
- Exclusivo para o papel **admin**

### Dashboard Analítico

Visão geral em tempo real com filtro de intervalo (Hoje / 7 dias / Este mês / Personalizado).

**KPI Cards — todos os papéis:**
- Total de produtos, categorias, vendas no período e faturamento líquido com variação %

**Painel Financeiro — gerente+:**
- A Receber / A Pagar / Saldo Previsto / Vencimentos próximos 7 dias

**Gráficos:**
- Evolução de Vendas (barras: Vendas / Devoluções / Líquido, toggle interativo)
- Fluxo de Caixa (barras: A Receber / Recebido / A Pagar / Pago / Saldo) — gerente+
- Top Produtos: doughnut chart com legenda e centro dinâmico
- Ranking de Vendas: lista com barra de progresso e percentual

**Tabelas:**
- Últimas 5 vendas com `sale_number` sequencial e badge de status
- Produtos com estoque abaixo do mínimo
- Últimas devoluções

### Produtos

- CRUD completo + import via CSV
- Campos: nome, descrição, preço de custo, preço de venda, estoque, estoque mínimo, categoria
- Alerta de estoque baixo no menu (badge pulsante vermelho)
- Restrito a **gerente** e **admin**

### Movimentação de Estoque

- Registro manual de entradas e saídas de estoque
- Histórico de movimentações com exclusão

### Vendas

- Criação com múltiplos itens (`SaleItem`)
- Numeração sequencial automática por empresa (`sale_number`)
- Status: `concluida`, `pendente`, `cancelada`
- Geração de nota/invoice em PDF
- Cancelar, restaurar e exclusão permanente (admin)
- Edição e exclusão restritas a **gerente** e **admin**

### Clientes e Fornecedores

- CRUD completo para ambos
- Campos completos: nome, e-mail, telefone, documento, endereço, observações
- Busca rápida de clientes (`GET /customers/search`)

### Devoluções

- Vinculadas a venda existente
- Estorno automático no estoque dos produtos devolvidos

### Ordens de Compra

- Fluxo de status: `rascunho → enviada → recebida_parcial → recebida` (ou `cancelada`)
- Número automático único por empresa (`OC-000001`)
- Entrada automática no estoque ao receber

### Financeiro

- **Contas a Pagar e a Receber** com CRUD completo
- Baixa individual e **baixa em lote** para ambos
- Status: `pendente`, `paga/recebida`, `vencida`, `cancelada`
- Filtros por período e paginação

### Relatórios (todos com PDF e CSV)

| Relatório | Rota |
|---|---|
| Vendas | `/reports/sales` |
| Financeiro | `/reports/financial` |
| Compras | `/reports/purchases` |
| Estoque | `/reports/stock` |
| Fornecedores | `/reports/suppliers` |
| Devoluções | `/reports/returns` |
| Lucratividade | `/reports/profitability` |
| Top Produtos | `/reports/top-products` |

### Notificações

- Listagem, marcar como lida, marcar todas como lidas, excluir
- Endpoint de não lidas (`GET /notifications/unread`)

### Tokens de API

- Geração e revogação de tokens Sanctum via `/settings/api`

### Painel Super-Admin

- Métricas globais: MRR, total de empresas, novas no mês, churn
- Distribuição de planos com barra percentual
- Listagem com numeração sequencial (independente de exclusões)
- Ações: Ativar/Desativar, Impersonation (entrar como admin da empresa), Excluir
- Sair do modo impersonation com banner identificador

### Gestão de Usuários

- CRUD completo — exclusivo **admin**
- Toggle ativo/inativo
- Edição de perfil próprio disponível para todos

---

## Estrutura de Arquivos

```
invexa/
├── app/
│   ├── Console/Commands/
│   │   └── CheckFinancialAlerts.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── ApiTokenController.php
│   │   │   ├── BillController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CompanyProfileController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   ├── LandingController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── OnboardingController.php
│   │   │   ├── ProductController.php
│   │   │   ├── PurchaseOrderController.php
│   │   │   ├── ReceivableController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SaleController.php
│   │   │   ├── SaleReturnController.php
│   │   │   ├── StockMovementController.php
│   │   │   ├── StripeWebhookController.php
│   │   │   ├── SubscriptionController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── SuperAdmin/SuperAdminController.php
│   │   │   ├── TwoFactorController.php
│   │   │   ├── UpgradeController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       ├── CheckCompanyAccess.php
│   │       ├── CheckOnboarding.php
│   │       ├── CheckRole.php
│   │       ├── CompanyMiddleware.php
│   │       ├── EnsureHasCompany.php
│   │       ├── ImpersonateBannerMiddleware.php
│   │       ├── SuperAdminMiddleware.php
│   │       └── TwoFactorMiddleware.php
│   └── Traits/
│       └── BelongsToCompany.php
├── resources/views/
│   ├── auth/
│   ├── bills/
│   ├── categories/
│   ├── components/
│   ├── customers/
│   ├── emails/
│   ├── errors/
│   ├── exports/
│   ├── layouts/
│   ├── notifications/
│   ├── onboarding/
│   ├── products/
│   ├── profile/
│   ├── purchase-orders/
│   ├── receivables/
│   ├── reports/
│   ├── returns/
│   ├── sales/
│   ├── search/
│   ├── settings/
│   ├── stock/
│   ├── subscription/
│   ├── superadmin/
│   ├── suppliers/
│   ├── upgrade/
│   ├── users/
│   ├── dashboard.blade.php
│   ├── landing.blade.php
│   ├── pricing.blade.php
│   └── upgrade.blade.php
├── tests/
│   └── Feature/
│       ├── BillBulkPayTest.php
│       └── ReceivableBulkReceiveTest.php
└── routes/
    └── web.php
```

---

## Banco de Dados

### Diagrama de Relacionamentos

```
companies
  └── users            (company_id)
  └── products         (company_id)
  └── categories       (company_id)
  └── sales            (company_id)
  └── customers        (company_id)
  └── suppliers        (company_id)
  └── purchase_orders  (company_id)
  └── bills            (company_id)
  └── receivables      (company_id)

categories → products
sales → sale_items → products
sales → sale_returns
purchase_orders → purchase_order_items → products
users → audit_logs
```

### Tabelas Principais

| Tabela | Descrição |
|---|---|
| `companies` | Empresas — unidade de isolamento multi-tenant, plano, trial, logo |
| `users` | Usuários com papel (superadmin/admin/gerente/vendedor), 2FA |
| `categories` | Categorias de produtos por empresa |
| `products` | Produtos com estoque, preços e estoque mínimo |
| `customers` | Clientes vinculados à empresa |
| `sales` | Cabeçalho da venda (sale_number, cliente, status, total) |
| `sale_items` | Itens de cada venda |
| `sale_returns` | Devoluções vinculadas a vendas |
| `stock_movements` | Histórico de movimentações de estoque |
| `suppliers` | Fornecedores por empresa |
| `purchase_orders` | Ordens de compra com status e número automático |
| `purchase_order_items` | Itens das ordens de compra |
| `bills` | Contas a pagar |
| `receivables` | Contas a receber |
| `notifications` | Notificações internas por usuário |
| `personal_access_tokens` | Tokens de API (Sanctum) |
| `subscriptions` | Assinaturas Stripe (Cashier) |
| `audit_logs` | Log de auditoria |

---

## Instalação e Configuração

### Pré-requisitos

- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL 8 ou SQLite

### Passo a passo

```bash
git clone https://github.com/AugustoCastilhoDev/invexa.git
cd invexa
composer run setup
```

O script `setup` executa automaticamente:
1. `composer install`
2. Copia `.env.example` → `.env`
3. Gera a `APP_KEY`
4. Executa as migrations
5. `npm install` + `npm run build`

### Configuração do `.env`

```env
APP_NAME=Invexa
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invexa
DB_USERNAME=root
DB_PASSWORD=

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# E-mail (ex: Resend ou Mailgun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_...
MAIL_FROM_ADDRESS=noreply@invexa.com.br
```

### Iniciar o servidor de desenvolvimento

```bash
composer run dev
```

Sobe simultaneamente:
- `php artisan serve` — servidor Laravel
- `npm run dev` — Vite HMR
- `php artisan queue:listen` — fila de jobs
- `php artisan pail` — log em tempo real

---

## Rotas da Aplicação

### Públicas

| Método | URI | Descrição |
|---|---|---|
| GET | `/` | Landing page |
| GET | `/pricing` | Página de planos |
| GET | `/login` | Tela de login |
| POST | `/login` | Autenticar (throttle: 10/min) |
| POST | `/logout` | Encerrar sessão |
| GET | `/register` | Tela de registro |
| POST | `/register` | Criar conta e empresa (throttle: 5/min) |
| POST | `/stripe/webhook` | Webhook Stripe (sem CSRF) |
| GET | `/two-factor/verify` | Verificação de 2FA pós-login |

### Autenticado — Onboarding

| Método | URI | Descrição |
|---|---|---|
| GET/POST | `/onboarding` | Wizard de configuração inicial |
| POST | `/onboarding/skip` | Pular onboarding |

### Autenticado — Configurações

| Método | URI | Papel | Descrição |
|---|---|---|---|
| GET | `/upgrade` | todos | Página de upgrade de plano |
| GET | `/settings/subscription` | todos | Assinatura atual e faturas |
| POST | `/settings/subscription/checkout` | todos | Iniciar checkout Stripe |
| GET | `/settings/subscription/portal` | todos | Portal Stripe |
| DELETE | `/settings/subscription/cancel` | todos | Cancelar assinatura |
| GET | `/settings/security` | todos | Configuração de 2FA |
| GET | `/settings/api` | todos | Tokens de API |
| GET/PATCH | `/settings/company` | admin | Perfil e logo da empresa |
| GET/PATCH | `/profile` | todos | Perfil do usuário |

### Autenticado — App (requer trial/assinatura ativa)

| Método | URI | Papel mínimo | Descrição |
|---|---|---|---|
| GET | `/dashboard` | vendedor | Dashboard |
| GET/POST | `/sales` | vendedor | Listar / Criar vendas |
| GET | `/sales/{id}` | vendedor | Detalhes |
| GET | `/sales/{id}/pdf` | vendedor | PDF da venda |
| GET/PUT/DELETE | `/sales/{id}/edit` | gerente | Editar / Excluir |
| PATCH | `/sales/{id}/cancel` | gerente | Cancelar venda |
| GET/POST/PUT/DELETE | `/customers` | vendedor | CRUD de clientes |
| GET/POST/PUT/DELETE | `/returns` | vendedor | CRUD de devoluções |
| GET/POST/DELETE | `/stock` | gerente | Movimentação de estoque |
| GET/POST/PUT/DELETE | `/products` | gerente | CRUD de produtos |
| POST | `/products/import` | gerente | Importar produtos via CSV |
| GET/POST/PUT/DELETE | `/categories` | gerente | CRUD de categorias |
| GET/POST/PUT/DELETE | `/suppliers` | gerente | CRUD de fornecedores |
| GET/POST/PUT/DELETE | `/purchase-orders` | gerente | CRUD de ordens de compra |
| PATCH | `/purchase-orders/{id}/receive` | gerente | Receber OC |
| GET/POST/PUT/DELETE | `/bills` | gerente | Contas a pagar |
| POST | `/bills/bulk-pay` | gerente | Baixa em lote |
| PATCH | `/bills/{id}/pay` | gerente | Baixa individual |
| GET/POST/PUT/DELETE | `/receivables` | gerente | Contas a receber |
| POST | `/receivables/bulk-receive` | gerente | Baixa em lote |
| PATCH | `/receivables/{id}/receive` | gerente | Baixa individual |
| GET | `/reports/sales` | gerente | Relatório de vendas |
| GET | `/reports/financial` | gerente | Relatório financeiro |
| GET | `/reports/purchases` | gerente | Relatório de compras |
| GET | `/reports/stock` | gerente | Relatório de estoque |
| GET | `/reports/suppliers` | gerente | Relatório de fornecedores |
| GET | `/reports/returns` | gerente | Relatório de devoluções |
| GET | `/reports/profitability` | gerente | Relatório de lucratividade |
| GET | `/reports/top-products` | gerente | Top produtos |
| GET/POST/PUT/DELETE | `/users` | admin | CRUD de usuários |
| GET | `/notifications` | todos | Notificações |

### Super-Admin

| Método | URI | Descrição |
|---|---|---|
| GET | `/admin` | Painel global + métricas do SaaS |
| POST | `/admin/companies/{company}/impersonate` | Entrar como admin da empresa |
| POST | `/admin/leave-impersonate` | Sair do modo suporte |
| PATCH | `/admin/companies/{company}/toggle` | Ativar/desativar empresa |
| DELETE | `/admin/companies/{company}` | Excluir empresa |

---

## Testes

```bash
php artisan test
```

| Arquivo | Cenários cobertos |
|---|---|
| `BillBulkPayTest` | Baixa em lote, ignorar já pagas, validação, isolamento multi-tenant |
| `ReceivableBulkReceiveTest` | Recebimento em lote, ignorar já recebidas, validação, isolamento multi-tenant |

---

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## Autor

**Augusto Castilho** — [GitHub @AugustoCastilhoDev](https://github.com/AugustoCastilhoDev) · [Instagram @castilho_digital](https://www.instagram.com/castilho_digital/)
