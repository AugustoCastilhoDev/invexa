# Invexa — Sistema de Estoque e Vendas

> Aplicação web para gestão de estoque, vendas e desempenho comercial, desenvolvida com **Laravel 13** e **Bootstrap 5**.

[
[
[
[

***

## Sobre o Projeto

O **Invexa** é um sistema SaaS multi-tenant para controle de estoque e vendas voltado a pequenas e médias empresas. Cada empresa possui seus próprios dados isolados (produtos, categorias, vendas e usuários), com controle de acesso baseado em papéis (admin, gerente, operador). O sistema oferece um dashboard analítico com KPIs em tempo real, gráfico de tendência de receita, relatórios exportáveis em CSV e PDF, e alertas de estoque baixo.

***

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Frontend | Bootstrap 5, Bootstrap Icons, Chart.js |
| Bundler | Vite |
| Template | Blade (layouts, componentes) |
| Banco de dados | MySQL (compatível com SQLite para desenvolvimento) |
| Geração de PDF | barryvdh/laravel-dompdf 3.x |
| Testes | PHPUnit 12 |
| Dev tools | Laravel Pint, Laravel Pail, Concurrently |

***

## Funcionalidades Implementadas

### Autenticação e Multi-Tenant

- Registro de usuário com criação automática de empresa vinculada
- Login / Logout com sessão segura
- Middleware `company` — garante que todo usuário autenticado possui uma empresa associada
- Middleware `role` — controle de acesso granular por papel (admin, gerente, operador)
- Isolamento de dados por `company_id` em todas as tabelas principais

### Papéis e Permissões

| Papel | Permissões |
|---|---|
| **admin** | Acesso total — inclui gestão de usuários, toggle ativo/inativo |
| **gerente** | Produtos, categorias, vendas (edição e exclusão incluídas) |
| **operador** | Visualização do dashboard, criação e visualização de vendas |

### Dashboard Analítico

- **KPI Cards** com tendência percentual (vs. período anterior):
  - Total de produtos ativos
  - Total de categorias
  - Total de vendas no período
  - Faturamento total
- **Gráfico de tendência de receita** (Chart.js — barras por dia, filtro por período)
- **Filtros de intervalo**: Hoje / Últimos 7 dias / Mês
- **Resumo rápido**: Ticket médio, venda de hoje, maior venda do período, variação de receita
- **Tabela de últimas vendas** (5 registros) com badge de status
- **Tabela de estoque baixo** com alertas visuais
- **Ações rápidas**: Exportar CSV, Exportar PDF, Nova Venda
- **Cards de métricas adicionais**: Média de faturamento, menor venda, receita do período anterior

### Produtos

- CRUD completo (criar, listar, editar, excluir)
- Campos: nome, descrição, preço de custo, preço de venda, quantidade em estoque, estoque mínimo
- Vínculo com categoria (relacionamento `belongsTo`)
- Controle de estoque mínimo — produtos abaixo do mínimo aparecem no dashboard
- Acesso restrito a **admin** e **gerente**

### Categorias

- CRUD completo
- Relacionamento `hasMany` com produtos
- Acesso restrito a **admin** e **gerente**

### Vendas

- Criação de vendas com múltiplos itens (`SaleItem`)
- Campos da venda: data, nome do cliente, status, total calculado automaticamente
- Status disponíveis: `concluida`, `pendente`, `cancelada` (e variantes em inglês)
- Edição e exclusão restritas a **admin** e **gerente**
- Visualização de detalhes disponível para todos os papéis
- Relacionamento `Sale → hasMany → SaleItem → belongsTo → Product`

### Exportação de Relatórios

- **Exportar CSV** — dados de vendas do período selecionado
- **Exportar PDF** — relatório formatado via DomPDF, com layout de impressão

### Gestão de Usuários

- CRUD de usuários (exceto `show`) — exclusivo para **admin**
- Toggle de ativo/inativo via `PATCH /users/{user}/toggle-active`
- Campos: nome, e-mail, papel, empresa vinculada, status ativo

### Audit Log

- Model `AuditLog` e migration dedicada
- Preparado para registrar ações críticas no sistema (estrutura base implementada)

***

## Estrutura de Arquivos Relevantes

```
invexa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/               # Login, Registro
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── SaleController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php       # Middleware de papel (admin, gerente, operador)
│   │       ├── CompanyMiddleware.php
│   │       └── EnsureHasCompany.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Category.php
│   │   ├── Company.php
│   │   ├── Product.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   └── User.php
│   └── Traits/
├── database/
│   └── migrations/
│       ├── create_users_table.php
│       ├── create_companies_table.php
│       ├── add_company_to_users_table.php
│       ├── create_audit_logs_table.php
│       ├── create_products_table.php
│       ├── create_sales_table.php
│       ├── create_sale_items_table.php
│       ├── create_categories_table.php
│       ├── add_category_id_to_products_table.php
│       ├── add_role_company_id_active_to_users_table.php
│       └── add_company_id_to_core_tables.php
├── resources/views/
│   ├── auth/               # Login, Registro
│   ├── categories/         # CRUD de categorias
│   ├── exports/            # Templates de PDF/CSV
│   ├── layouts/            # Layout principal (app.blade.php)
│   ├── products/           # CRUD de produtos
│   ├── sales/              # CRUD de vendas
│   ├── users/              # CRUD de usuários
│   ├── dashboard.blade.php # Dashboard principal
│   └── welcome.blade.php   # Landing page pública
└── routes/
    └── web.php             # Todas as rotas da aplicação
```

***

## Banco de Dados

### Diagrama de Relacionamentos

```
companies
  └── users (company_id)
  └── products (company_id)
  └── categories (company_id)
  └── sales (company_id)

categories
  └── products (category_id)

sales
  └── sale_items (sale_id)
        └── products (product_id)

users → audit_logs
```

### Tabelas

| Tabela | Descrição |
|---|---|
| `users` | Usuários com papel (admin/gerente/operador), empresa vinculada e flag ativo |
| `companies` | Empresas — unidade de isolamento multi-tenant |
| `categories` | Categorias de produtos por empresa |
| `products` | Produtos com estoque, preços e categoria |
| `sales` | Cabeçalho da venda (cliente, data, status, total) |
| `sale_items` | Itens de cada venda (produto, quantidade, preço unitário) |
| `audit_logs` | Log de auditoria de ações do sistema |
| `cache` | Cache de sessão/queue do Laravel |
| `jobs` | Filas de jobs do Laravel |

***

## Instalação e Configuração

### Pré-requisitos

- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL ou SQLite

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

### Configuração manual do `.env`

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

***

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
| GET | `/dashboard` | operador | Dashboard principal |
| GET | `/dashboard/export/csv` | operador | Exportar CSV |
| GET | `/dashboard/export/pdf` | operador | Exportar PDF |
| GET | `/sales` | operador | Listar vendas |
| GET | `/sales/create` | operador | Formulário de nova venda |
| POST | `/sales` | operador | Salvar nova venda |
| GET | `/sales/{id}` | operador | Detalhes da venda |
| GET | `/sales/{id}/edit` | gerente | Editar venda |
| PUT | `/sales/{id}` | gerente | Atualizar venda |
| DELETE | `/sales/{id}` | gerente | Excluir venda |
| GET/POST/PUT/DELETE | `/products` | gerente | CRUD de produtos |
| GET/POST/PUT/DELETE | `/categories` | gerente | CRUD de categorias |
| GET/POST/PUT/DELETE | `/users` | admin | CRUD de usuários |
| PATCH | `/users/{id}/toggle-active` | admin | Ativar/desativar usuário |

***

## Design e Interface

A interface utiliza tema escuro personalizado com as seguintes características:

- **Paleta**: fundo `#08101d` com gradiente radial sutil em azul e verde
- **KPI Cards**: cards coloridos com gradiente (azul, ciano, verde, âmbar) e badge de tendência
- **Dashboard cards**: fundo `rgba(15, 23, 42, 0.88)` com borda sutil
- **Badges de status**: pill translúcidos com ponto colorido indicador (verde, amarelo, vermelho)
- **Tabelas**: cabeçalho em uppercase com letra menor e separação visual clara
- **Resumo rápido**: linhas de métrica com ícone alinhado verticalmente à direita
- **Gráfico de receita**: barras com gradiente e tooltip personalizado em `pt-BR`

***

## Testes

```bash
composer run test
# ou
php artisan test
```

Configuração em `phpunit.xml` — testes unitários e de feature via PHPUnit 12.

***

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

***

## Autor

**Augusto Castilho** — [GitHub @AugustoCastilhoDev](https://github.com/AugustoCastilhoDev)
