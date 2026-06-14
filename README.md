# Invexa — Sistema de Gestão Comercial SaaS

> Aplicação web **SaaS multi-tenant** para gestão completa de estoque, vendas, compras, financeiro e relatórios, desenvolvida com **Laravel 13** e **Bootstrap 5**.

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Status](https://img.shields.io/badge/status-em%20produção-green?style=flat-square)](https://invexa-app.com.br)

---

## Sobre o Projeto

O **Invexa** é um ERP leve voltado a pequenas e médias empresas, distribuído como SaaS com planos **Free / Pro / Business**. A arquitetura multi-tenant garante isolamento total de dados por empresa — produtos, vendas, compras, clientes, financeiro e usuários são sempre segregados por `company_id`. O controle de acesso é baseado em papéis (`admin`, `gerente`, `vendedor`), com visibilidades e permissões distintas em toda a interface e nas rotas.

🌐 **Produção:** [invexa-app.com.br](https://invexa-app.com.br)
📚 **API Docs:** [invexa-app.com.br/api-docs](https://invexa-app.com.br/api-docs)

---

## Stack Tecnológica

| Camada          | Tecnologia                                   |
| --------------- | -------------------------------------------- |
| Backend         | PHP 8.3 + Laravel 13                         |
| Frontend        | Bootstrap 5.3, Bootstrap Icons, Chart.js     |
| Bundler         | Vite                                         |
| Template        | Blade (layouts, componentes)                 |
| Banco de dados  | MySQL 8                                      |
| Geração de PDF  | barryvdh/laravel-dompdf 3.x                  |
| Auth            | Laravel Breeze customizado + Sanctum (API)   |
| Filas           | Redis                                        |
| E-mail          | Resend (transacional)                        |
| Pagamentos SaaS | Stripe (assinaturas) + Cashier               |
| Pagamentos Pix  | Asaas (multi-tenant por empresa)             |
| Monitoramento   | Flare (erros em produção)                    |
| Observabilidade | Laravel Telescope (superadmin)               |
| Testes          | PHPUnit 12                                   |
| Dev tools       | Laravel Pint, Laravel Pail, Concurrently     |

---

## Estado Atual — Produção

### ✅ Módulos Implementados e Funcionando

| Módulo                              | Status        |
| ----------------------------------- | ------------- |
| Autenticação & Multi-Tenant         | ✅ Completo    |
| Papéis e Permissões (RBAC)          | ✅ Completo    |
| Dashboard Analítico                 | ✅ Completo    |
| Produtos & Categorias               | ✅ Completo    |
| Vendas (PDV + itens)                | ✅ Completo    |
| Clientes                            | ✅ Completo    |
| Devoluções                          | ✅ Completo    |
| Fornecedores                        | ✅ Completo    |
| Ordens de Compra                    | ✅ Completo    |
| Contas a Pagar                      | ✅ Completo    |
| Contas a Receber                    | ✅ Completo    |
| Relatório de Vendas (PDF/CSV)       | ✅ Completo    |
| Relatório de Compras (PDF/CSV)      | ✅ Completo    |
| Gestão de Usuários                  | ✅ Completo    |
| Painel Super-Admin                  | ✅ Completo    |
| Audit Log                           | ✅ Completo    |
| Trial + Bloqueio automático         | ✅ Completo    |
| Planos Free / Pro / Business        | ✅ Completo    |
| Assinaturas via Stripe              | ✅ Completo    |
| E-mail transacional (Resend)        | ✅ Completo    |
| Monitoramento de erros (Flare)      | ✅ Completo    |
| Backup automático diário            | ✅ Completo    |
| LGPD — Privacidade & Termos         | ✅ Completo    |
| Testes automatizados                | ✅ Completo    |
| Pix multi-tenant (Asaas)            | ✅ Completo    |
| App Mobile (PWA)                    | ✅ Completo    |
| API pública documentada             | ✅ Completo    |

### 🔄 Em Andamento

| Item                               | Status          |
| ---------------------------------- | --------------- |
| NF-e / NFS-e integrada (Focus NFe) | 🔄 Em andamento |

---

## Funcionalidades Implementadas

### Autenticação e Multi-Tenant

- Registro de usuário com criação automática de empresa vinculada
- Login / Logout com sessão segura (CSRF, hashing bcrypt)
- Middleware `EnsureHasCompany` — bloqueia acesso se o usuário não tiver empresa associada
- Middleware `CheckRole` — controle de acesso granular por papel
- Trait `BelongsToCompany` — aplica escopo global de `company_id` em todos os models principais
- Isolamento total: nenhum usuário acessa dados de outra empresa

### Planos e Assinaturas

| Plano        | Produtos | Clientes | Usuários | Cobrança      |
| ------------ | -------- | -------- | -------- | ------------- |
| **Free**     | 50       | 100      | 2        | Gratuito      |
| **Pro**      | 500      | 1.000    | 10       | R$ 39,90/mês  |
| **Business** | ∞        | ∞        | ∞        | R$ 119,90/mês |

- Trial de 30 dias com acesso completo (sem cartão de crédito)
- Bloqueio automático ao expirar o trial — dados preservados
- Cobrança via Stripe (mensal e anual com desconto de 20%)

### Papéis e Permissões

| Papel          | Acesso                                               |
| -------------- | ---------------------------------------------------- |
| **admin**      | Acesso total — inclui gestão de usuários             |
| **gerente**    | Estoque, Compras, Financeiro, Relatórios, Vendas     |
| **vendedor**   | Dashboard (parcial), Vendas, Clientes e Devoluções   |
| **superadmin** | Painel global do SaaS — gerencia todas as empresas   |

### Dashboard Analítico

Visão geral em tempo real com filtro de intervalo (Hoje / 7 dias / Este mês / Personalizado).

- KPI Cards: produtos, categorias, vendas, faturamento com variação percentual
- Painel Financeiro: A Receber / A Pagar / Saldo Previsto / Vencimentos próximos
- Gráficos: Evolução de Vendas, Fluxo de Caixa, Top Produtos, Ranking de Vendas
- Tabelas: Últimas vendas, produtos com estoque abaixo do mínimo, últimas devoluções

### PDV e Vendas

- Criação de vendas com múltiplos itens (`SaleItem`)
- Numeração sequencial automática por empresa (`sale_number`)
- Status: `concluida`, `pendente`, `cancelada`
- Devoluções com estorno automático no estoque
- QR Code Pix gerado diretamente no PDV e na nota/invoice PDF

### Financeiro

- Contas a Pagar e Receber com baixa individual e **baixa em lote**
- Parcelamento, recorrência e controle de inadimplência
- Alertas de vencimento no dashboard
- Baixa automática de Conta a Receber ao confirmar pagamento Pix

### Relatórios

- Filtros por período (7d / 30d / 90d / 1 ano / personalizado)
- Exportação em **PDF** e **CSV**
- Relatório de Vendas e Relatório de Compras

### Pix multi-tenant (Asaas)

Cada empresa conecta sua própria conta Asaas — o Invexa não intermedia os recebimentos.

- Tela de configuração Asaas no painel da empresa (`asaas_api_key` + `asaas_wallet_id`)
- `PixPaymentService` isolado por empresa autenticada
- Geração de cobrança Pix na finalização de venda — retorna QR Code e copia-e-cola
- Webhook Asaas por empresa para confirmação automática de pagamento
- Baixa automática do Conta a Receber ao confirmar pagamento
- QR Code exibido no PDV e no PDF da nota/invoice
- Fluxo completo: venda → Pix → webhook → baixa automática

### App Mobile (PWA)

O Invexa é instalável como app nativo em Android e iOS sem necessidade de loja.

- Service Worker com cache offline de assets
- "Adicionar à tela inicial" testado em Android e iOS
- Ícones e splash screen do Invexa configurados
- Telas mobile otimizadas: Dashboard, Nova Venda, Estoque

### API Pública

API RESTful v1 com autenticação via Sanctum (Bearer Token).

- Documentação pública em [`/api-docs`](https://invexa-app.com.br/api-docs)
- Rate limiting: 60 req/min por token
- Tokens gerenciáveis em **Settings → API Tokens**

| Endpoint                                       | Descrição                        |
| ---------------------------------------------- | -------------------------------- |
| `POST /api/v1/auth/token`                      | Gerar token de acesso            |
| `DELETE /api/v1/auth/token`                    | Revogar token                    |
| `GET /api/v1/me`                               | Dados do usuário autenticado     |
| `GET\|POST\|PUT\|DELETE /api/v1/products`      | CRUD completo de produtos        |
| `GET\|POST\|PUT\|DELETE /api/v1/customers`     | CRUD completo de clientes        |
| `GET\|POST /api/v1/sales`                      | Listar e criar vendas            |
| `GET /api/v1/stock`                            | Estoque geral                    |
| `GET /api/v1/stock/low`                        | Produtos com estoque baixo       |
| `POST /api/v1/stock/movement`                  | Registrar movimentação           |

### Audit Log

- `AuditLog::record()` implementado nos Controllers críticos
- Eventos logados: vendas, financeiro, assinaturas, login/logout, usuários, impersonation
- View de consulta de logs no painel Admin e Super-Admin

### Painel Super-Admin

- Métricas globais do SaaS: MRR estimado, total de empresas, churn
- Distribuição de planos com barra de progresso
- Ações por empresa: impersonation, ativar/desativar, excluir

---

## Infraestrutura de Produção

| Componente      | Configuração                                    |
| --------------- | ----------------------------------------------- |
| Servidor        | VPS Hostinger (PHP-FPM + Nginx + MySQL 8)       |
| PHP             | 8.3                                             |
| Framework       | Laravel 13                                      |
| SSL             | Certbot (Let's Encrypt) — auto-renovação        |
| Filas           | Redis                                           |
| E-mail          | Resend                                          |
| Monitoramento   | Flare (alertas por e-mail para novos erros)     |
| Observabilidade | Laravel Telescope (acesso restrito superadmin)  |
| Backup          | MySQL dump diário às 03h — retenção 30 dias     |

---

## Instalação Local

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

### Variáveis de ambiente necessárias

```env
APP_NAME=Invexa
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invexa
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=resend
RESEND_API_KEY=your_resend_key

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Pix via Asaas — configurado por empresa no painel
# ASAAS_ENVIRONMENT=sandbox  (sandbox | production)
```

### Iniciar servidor de desenvolvimento

```bash
composer run dev
```

---

## Banco de Dados

### Relacionamentos principais

```
companies
  └── users              (company_id)
  └── products           (company_id)
  └── categories         (company_id)
  └── sales              (company_id)
  └── customers          (company_id)
  └── suppliers          (company_id)
  └── purchase_orders    (company_id)
  └── bills              (company_id)
  └── receivables        (company_id)
  └── asaas_api_key      (campo — integração Pix por empresa)
  └── asaas_wallet_id    (campo — integração Pix por empresa)

sales
  └── sale_items (sale_id → product_id)
  └── returns    (sale_id)

purchase_orders
  └── purchase_order_items (purchase_order_id → product_id)
```

---

## Testes

```bash
php artisan test
```

| Arquivo                     | Cenários cobertos                                                             |
| --------------------------- | ----------------------------------------------------------------------------- |
| `BillBulkPayTest`           | Baixa em lote, ignorar já pagas, validação, isolamento multi-tenant           |
| `ReceivableBulkReceiveTest` | Recebimento em lote, ignorar já recebidas, validação, isolamento multi-tenant |

---

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## Autor

**Augusto Castilho** — [GitHub @AugustoCastilhoDev](https://github.com/AugustoCastilhoDev) · [Instagram @castilho_digital](https://www.instagram.com/castilho_digital/)
