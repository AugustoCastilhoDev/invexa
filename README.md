# Invexa — Sistema de Gestão Comercial

> Aplicação web SaaS multi-tenant para gestão completa de estoque, vendas, compras, financeiro e relatórios, desenvolvida com **Laravel 13** e **Bootstrap 5**.

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)

---

## Sobre o Projeto

O **Invexa** é um sistema de gestão comercial completo voltado a pequenas e médias empresas. A arquitetura multi-tenant garante isolamento total de dados por empresa — produtos, vendas, compras, clientes, financeiro e usuários são sempre segregados por `company_id`. O controle de acesso é baseado em papéis (admin, gerente, vendedor), com cada papel tendo visibilidade e permissões distintas na interface e nas rotas.

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Chart.js |
| Bundler | Vite |
| Template | Blade (layouts, componentes) |
| Banco de dados | MySQL 8 (compatível com SQLite para desenvolvimento) |
| Geração de PDF | barryvdh/laravel-dompdf 3.x |
| Testes | PHPUnit 12 |
| Dev tools | Laravel Pint, Laravel Pail, Concurrently |

---

## Funcionalidades Implementadas

### Autenticação e Multi-Tenant

- Registro de usuário com criação automática de empresa vinculada
- Login / Logout com sessão segura (CSRF, hashing bcrypt)
- Middleware `EnsureHasCompany` — bloqueia acesso se o usuário não tiver empresa associada
- Middleware `CheckRole` — controle de acesso granular por papel
- Trait `BelongsToCompany` — aplica escopo global de `company_id` em todos os models principais
- Isolamento total: nenhum usuário acessa dados de outra empresa

### Papéis e Permissões

| Papel | Acesso |
|---|---|
| **admin** | Acesso total — inclui gestão de usuários e todas as funcionalidades de gerente |
| **gerente** | Estoque, Compras, Financeiro, Relatórios, Vendas (edição e exclusão incluídas) |
| **vendedor** | Dashboard (parcial), Vendas, Clientes e Devoluções |

### Dashboard Analítico

Visão geral em tempo real com filtro de intervalo (Hoje / 7 dias / Mês).

**KPI Cards — visíveis para todos os papéis:**
- Total de produtos ativos no estoque
- Total de categorias cadastradas
- Total de vendas no período
- Faturamento líquido (bruto − devoluções) com variação percentual vs. período anterior

**Painel Financeiro — exclusivo para Gerente e Admin:**
- A Receber (pendente + valor vencido em atraso)
- A Pagar (pendente + valor vencido em atraso)
- Saldo Previsto (A Receber − A Pagar)
- Contador de vencimentos nos próximos 7 dias

**Fluxo de Caixa — exclusivo para Gerente e Admin:**
- Gráfico de barras empilhadas (Chart.js) com 4 séries: A Receber, Já Recebido, A Pagar, Saldo Acumulado
- Eixo Y secundário para o saldo acumulado
- Cores distintas: verde (receber), vermelho (pagar), âmbar (saldo +), roxo (saldo −)
- Filtro de intervalo (Hoje / 7 dias / Mês)

**Tabelas e métricas — visíveis para todos os papéis:**
- Próximos vencimentos (7 dias) com alerta visual para os que vencem hoje
- Gráfico de faturamento por dia (bruto × devoluções, tooltip com líquido)
- Resumo rápido: faturamento bruto, devoluções, faturamento líquido, venda de hoje, ticket médio, variação de receita
- Últimas 5 vendas com badge de status
- Produtos com estoque abaixo do mínimo
- Devoluções recentes (últimos 5 registros)
- Ações rápidas: Exportar CSV/PDF (gerente+), Nova Venda, Nova Devolução

### Produtos

- CRUD completo (criar, listar, editar, excluir)
- Campos: nome, descrição, preço de custo, preço de venda, quantidade em estoque, estoque mínimo
- Vínculo com categoria (`belongsTo`)
- Alertas de estoque baixo no dashboard e no menu de navegação (badge pulsante vermelho)
- Acesso restrito a **gerente** e **admin**

### Categorias

- CRUD completo
- Relacionamento `hasMany` com produtos
- Acesso restrito a **gerente** e **admin**

### Vendas

- Criação de vendas com múltiplos itens (`SaleItem`)
- Campos: data, nome do cliente, status, total calculado automaticamente
- Status disponíveis: `concluida`, `pendente`, `cancelada`
- Edição e exclusão restritas a **gerente** e **admin**
- Visualização de detalhes disponível para todos os papéis

### Clientes

- CRUD de clientes vinculados à empresa
- Campos: nome, e-mail, telefone, CPF/CNPJ, endereço, observações
- Relacionamento com vendas
- Acesso a **vendedor**, **gerente** e **admin**

### Devoluções

- Registro de devoluções vinculadas a uma venda existente
- Campos: motivo, itens devolvidos, valor estornado
- Estorno automático no estoque dos produtos devolvidos
- Badge de label de motivo no detalhe e no dashboard
- Acesso a **vendedor**, **gerente** e **admin**

### Fornecedores

- CRUD completo
- Campos: nome, CNPJ, e-mail, telefone, endereço, observações
- Relacionamento com Ordens de Compra
- Acesso restrito a **gerente** e **admin**

### Ordens de Compra

- CRUD completo com fluxo de status: `rascunho → enviada → recebida_parcial → recebida` (ou `cancelada`)
- Campos do cabeçalho: número automático (OC-000001), fornecedor, data prevista, notas, total
- Itens da OC: produto, quantidade, preço unitário, subtotal
- Ações de transição: Enviar, Receber, Cancelar (habilitadas conforme status atual)
- Entrada automática no estoque ao receber a OC
- Número único por empresa gerado automaticamente
- Acesso restrito a **gerente** e **admin**

### Financeiro — Contas a Pagar

- CRUD completo
- Campos: descrição, valor, vencimento, categoria, forma de pagamento, status (`pendente`, `paga`, `vencida`, `cancelada`)
- Baixa individual com data e forma de pagamento
- **Baixa em lote**: selecione múltiplas contas pendentes/vencidas e quite todas de uma vez
- Filtros por período e paginação
- Acesso restrito a **gerente** e **admin**

### Financeiro — Contas a Receber

- CRUD completo
- Campos: descrição, valor, vencimento, categoria, status (`pendente`, `recebida`, `vencida`, `cancelada`)
- Baixa individual com data de recebimento
- **Baixa em lote**: selecione múltiplas contas e confirme o recebimento de uma vez
- Filtros por período e paginação
- Acesso restrito a **gerente** e **admin**

### Relatório de Vendas

- Filtros por período (7d / 30d / 90d / 1 ano / personalizado), com seletor de datas
- KPIs: total de vendas, faturamento bruto, devoluções, faturamento líquido
- Tabela de vendedores por volume
- Top produtos mais vendidos (quantidade, pedidos, receita)
- Tabela detalhada de todas as vendas no período
- Exportação em **PDF** e **CSV**
- Acesso restrito a **gerente** e **admin**

### Relatório de Compras

- Filtros por período, fornecedor e status da OC
- KPIs: total de OCs, valor total, valor recebido, valor pendente
- Tabela de compras por fornecedor (quantidade + total)
- Top produtos mais comprados (quantidade, OCs, custo total)
- Tabela detalhada de OCs no período com coluna de Recebimento
- Exportação em **PDF** e **CSV**
- Acesso restrito a **gerente** e **admin**

### Gestão de Usuários

- CRUD de usuários — exclusivo para **admin**
- Toggle de ativo/inativo via `PATCH /users/{user}/toggle-active`
- Campos: nome, e-mail, papel, empresa vinculada, status ativo
- Edição de perfil próprio disponível para todos os papéis

### Audit Log

- Model `AuditLog` com migration dedicada
- Estrutura base implementada para registro de ações críticas

---

## Estrutura de Arquivos Relevantes

```
invexa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── BillController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── PurchaseOrderController.php
│   │   │   ├── ReceivableController.php
│   │   │   ├── ReportController.php
│   │   │   ├── ReturnController.php
│   │   │   ├── SaleController.php
│   │   │   ├── SupplierController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── EnsureHasCompany.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Bill.php
│   │   ├── Category.php
│   │   ├── Company.php
│   │   ├── Customer.php
│   │   ├── Product.php
│   │   ├── PurchaseOrder.php
│   │   ├── PurchaseOrderItem.php
│   │   ├── Receivable.php
│   │   ├── Return.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── Supplier.php
│   │   └── User.php
│   └── Traits/
│       └── BelongsToCompany.php
├── resources/views/
│   ├── auth/
│   ├── bills/
│   ├── categories/
│   ├── customers/
│   ├── exports/
│   ├── layouts/
│   ├── products/
│   ├── purchase-orders/
│   ├── receivables/
│   ├── reports/
│   │   ├── index.blade.php       # Relatório de vendas
│   │   └── purchases.blade.php   # Relatório de compras
│   ├── returns/
│   ├── sales/
│   ├── suppliers/
│   ├── users/
│   └── dashboard.blade.php
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

categories
  └── products (category_id)

sales
  └── sale_items (sale_id → product_id)
  └── returns    (sale_id)

purchase_orders
  └── purchase_order_items (purchase_order_id → product_id)

users → audit_logs
```

### Tabelas

| Tabela | Descrição |
|---|---|
| `users` | Usuários com papel (admin/gerente/vendedor), empresa e flag ativo |
| `companies` | Empresas — unidade de isolamento multi-tenant |
| `categories` | Categorias de produtos por empresa |
| `products` | Produtos com estoque, preços, estoque mínimo e categoria |
| `customers` | Clientes vinculados à empresa |
| `sales` | Cabeçalho da venda (cliente, data, status, total) |
| `sale_items` | Itens de cada venda (produto, qtd, preço unitário) |
| `returns` | Devoluções vinculadas a vendas |
| `suppliers` | Fornecedores por empresa |
| `purchase_orders` | Ordens de compra com status e número automático |
| `purchase_order_items` | Itens das ordens de compra |
| `bills` | Contas a pagar |
| `receivables` | Contas a receber |
| `audit_logs` | Log de auditoria de ações do sistema |
| `cache` | Cache de sessão do Laravel |
| `jobs` | Filas de jobs do Laravel |

---

## Instalação e Configuração

### Pré-requisitos

- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL 8 ou SQLite

### Passo a passo

```bash
# 1. Clonar o repositório
git clone https://github.com/AugustoCastilhoDev/invexa.git
cd invexa

# 2. Instalar dependências e configurar automaticamente
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
| GET | `/login` | Tela de login |
| POST | `/login` | Autenticar usuário |
| POST | `/logout` | Encerrar sessão |
| GET | `/register` | Tela de registro |
| POST | `/register` | Criar conta e empresa |

### Protegidas (autenticado + empresa)

| Método | URI | Papel mínimo | Descrição |
|---|---|---|---|
| GET | `/dashboard` | vendedor | Dashboard principal |
| GET | `/dashboard/export/csv` | gerente | Exportar CSV do dashboard |
| GET | `/dashboard/export/pdf` | gerente | Exportar PDF do dashboard |
| GET/POST | `/sales` | vendedor | Listar / Criar vendas |
| GET | `/sales/{id}` | vendedor | Detalhes da venda |
| GET/PUT/DELETE | `/sales/{id}/edit` | gerente | Editar / Atualizar / Excluir venda |
| GET/POST/PUT/DELETE | `/customers` | vendedor | CRUD de clientes |
| GET/POST/PUT/DELETE | `/returns` | vendedor | CRUD de devoluções |
| GET/POST/PUT/DELETE | `/products` | gerente | CRUD de produtos |
| GET/POST/PUT/DELETE | `/categories` | gerente | CRUD de categorias |
| GET/POST/PUT/DELETE | `/suppliers` | gerente | CRUD de fornecedores |
| GET/POST/PUT/DELETE | `/purchase-orders` | gerente | CRUD de ordens de compra |
| GET/POST/PUT/DELETE | `/bills` | gerente | CRUD de contas a pagar |
| POST | `/bills/bulk-pay` | gerente | Baixa em lote — contas a pagar |
| PATCH | `/bills/{bill}/pay` | gerente | Baixa individual |
| GET/POST/PUT/DELETE | `/receivables` | gerente | CRUD de contas a receber |
| POST | `/receivables/bulk-receive` | gerente | Baixa em lote — contas a receber |
| PATCH | `/receivables/{receivable}/receive` | gerente | Baixa individual |
| GET | `/reports` | gerente | Relatório de vendas |
| GET | `/reports/pdf` | gerente | Exportar PDF — vendas |
| GET | `/reports/csv` | gerente | Exportar CSV — vendas |
| GET | `/reports/purchases` | gerente | Relatório de compras |
| GET | `/reports/purchases/pdf` | gerente | Exportar PDF — compras |
| GET | `/reports/purchases/csv` | gerente | Exportar CSV — compras |
| GET/POST/PUT/DELETE | `/users` | admin | CRUD de usuários |
| PATCH | `/users/{id}/toggle-active` | admin | Ativar/desativar usuário |
| GET/PUT | `/profile` | vendedor | Editar perfil próprio |

---

## Design e Interface

A interface utiliza tema escuro personalizado consistente em todas as telas:

- **Paleta**: fundo `#08101d` com gradiente radial sutil em azul e verde
- **KPI Cards**: gradiente colorido (azul, ciano, verde, âmbar) com badge de tendência percentual
- **Dashboard cards**: fundo `rgba(15,23,42,.88)` com borda sutil `rgba(148,163,184,.14)`
- **Badges de status**: pill translúcidos com ponto indicador colorido
- **Tabelas**: cabeçalho uppercase em fonte menor com separação visual sutil
- **Gráficos**: Chart.js com paleta escura e tooltip personalizado em `pt-BR`
- **Alerta de estoque**: badge pulsante vermelho no menu (animação CSS)
- **Barra de ação em lote**: flutuante no rodapé, aparece ao selecionar checkboxes

---

## Testes

```bash
php artisan test
# ou
composer run test
```

### Cobertura de testes Feature implementada

| Arquivo | Cenários cobertos |
|---|---|
| `BillBulkPayTest` | Baixa em lote com sucesso, ignorar já pagas, validação de campos, isolamento multi-tenant |
| `ReceivableBulkReceiveTest` | Recebimento em lote com sucesso, ignorar já recebidas, validação de campos, isolamento multi-tenant |

---

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## Autor

**Augusto Castilho** — [GitHub @AugustoCastilhoDev](https://github.com/AugustoCastilhoDev) · [Instagram @castilho_digital](https://www.instagram.com/castilho_digital/)
