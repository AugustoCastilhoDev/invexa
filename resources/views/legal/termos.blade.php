<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso — Invexa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --abyss:    #080D1A;
            --navy:     #0D1929;
            --sky:      #0EA5E9;
            --electric: #38BDF8;
            --ice:      #F0F9FF;
            --glow:     rgba(14,165,233,.35);
        }
        * { scroll-behavior: smooth; }
        body {
            background: radial-gradient(circle at top left, rgba(14,165,233,.07), transparent 22%),
                        radial-gradient(circle at bottom right, rgba(56,189,248,.05), transparent 20%),
                        var(--abyss);
            color: #e2e8f0;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        /* NAV */
        .lp-nav {
            background: rgba(8,13,26,.93);
            border-bottom: 1px solid rgba(14,165,233,.12);
            backdrop-filter: blur(14px);
            padding: .6rem 0;
        }
        .lp-nav .navbar-brand {
            display: flex; align-items: center; gap: .5rem;
            font-weight: 700; color: var(--ice) !important;
            font-size: 1rem; text-decoration: none;
        }
        .lp-nav .nav-link { color: rgba(226,232,240,.65) !important; font-size: .875rem; }
        .lp-nav .nav-link:hover { color: var(--ice) !important; }

        /* HERO */
        .legal-hero {
            padding: 56px 0 40px;
            border-bottom: 1px solid rgba(14,165,233,.08);
        }
        .legal-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(14,165,233,.12); border: 1px solid rgba(14,165,233,.25);
            border-radius: 999px; padding: .25rem .9rem;
            font-size: .72rem; font-weight: 700; color: var(--electric);
            margin-bottom: 1rem; letter-spacing: .06em; text-transform: uppercase;
        }
        .legal-hero h1 { font-size: 2rem; font-weight: 800; color: var(--ice); margin-bottom: .5rem; }
        .legal-hero p { color: rgba(226,232,240,.5); font-size: .875rem; }

        /* ALERT */
        .legal-alert {
            background: rgba(251,191,36,.07);
            border: 1px solid rgba(251,191,36,.25);
            border-radius: 10px; padding: 14px 18px;
            font-size: .875rem; color: #FCD34D;
            margin-bottom: 2rem;
        }
        .legal-alert a { color: var(--electric); text-decoration: none; }
        .legal-alert strong { color: #FDE68A; }

        /* CONTENT */
        .legal-content { padding: 48px 0 80px; }
        .legal-section { margin-bottom: 2.5rem; }
        .legal-section h2 {
            font-size: 1rem; font-weight: 700; color: var(--electric);
            border-bottom: 1px solid rgba(14,165,233,.12);
            padding-bottom: .6rem; margin-bottom: 1.1rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .legal-section h3 { font-size: .9rem; font-weight: 600; color: #f1f5f9; margin: 1rem 0 .4rem; }
        .legal-section p, .legal-section li {
            font-size: .875rem; color: rgba(226,232,240,.7); line-height: 1.75;
        }

        /* INFO CARD */
        .info-card {
            background: rgba(13,25,41,.8);
            border: 1px solid rgba(14,165,233,.12);
            border-radius: 12px; padding: 16px 20px;
            font-size: .875rem;
        }
        .info-card strong { color: #f1f5f9; }
        .info-card a { color: var(--electric); text-decoration: none; }

        .highlight-card {
            background: rgba(14,165,233,.07);
            border: 1px solid rgba(14,165,233,.2);
            border-radius: 12px; padding: 16px 20px;
            font-size: .875rem; color: rgba(226,232,240,.75);
        }
        .highlight-card a { color: var(--electric); text-decoration: none; }

        /* CAN / CANNOT */
        .can-card {
            background: rgba(34,197,94,.06);
            border: 1px solid rgba(34,197,94,.2);
            border-radius: 12px; padding: 16px 18px;
        }
        .can-card .card-title { font-size: .82rem; font-weight: 700; color: #4ade80; margin-bottom: .6rem; }
        .can-card ul { list-style: none; padding: 0; margin: 0; }
        .can-card ul li { font-size: .82rem; color: rgba(134,239,172,.8); padding: .2rem 0; display: flex; gap: .5rem; }

        .cannot-card {
            background: rgba(239,68,68,.06);
            border: 1px solid rgba(239,68,68,.2);
            border-radius: 12px; padding: 16px 18px;
        }
        .cannot-card .card-title { font-size: .82rem; font-weight: 700; color: #f87171; margin-bottom: .6rem; }
        .cannot-card ul { list-style: none; padding: 0; margin: 0; }
        .cannot-card ul li { font-size: .82rem; color: rgba(252,165,165,.8); padding: .2rem 0; display: flex; gap: .5rem; }

        /* FOOTER */
        .lp-footer {
            background: rgba(8,13,26,.95);
            border-top: 1px solid rgba(14,165,233,.08);
            padding: 2rem 0; font-size: .8rem; color: rgba(148,163,184,.5);
        }
        .lp-footer a { color: var(--electric); text-decoration: none; }
        .lp-footer a:hover { color: var(--ice); }
    </style>
</head>
<body>

{{-- NAV --}}
<nav class="lp-nav navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#080D1A"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            INVEXA
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="{{ url('/') }}" class="nav-link"><i class="bi bi-arrow-left me-1"></i>Voltar ao início</a>
        </div>
    </div>
</nav>

{{-- HERO --}}
<div class="legal-hero">
    <div class="container">
        <div class="legal-badge"><i class="bi bi-file-earmark-text"></i>Contrato de uso da plataforma</div>
        <h1>Termos de Uso</h1>
        <p>Versão 1.0 — Vigência: Junho de 2026</p>
    </div>
</div>

{{-- CONTENT --}}
<div class="legal-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="legal-alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Ao criar uma conta ou utilizar o INVEXA, você declara que leu e concorda com estes Termos de Uso e com a <a href="{{ route('privacidade') }}">Política de Privacidade</a>.
                </div>

                {{-- 1 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-building"></i>1. Identificação do Prestador</h2>
                    <div class="info-card">
                        <div><strong>Augusto Corrêa Castilho</strong></div>
                        <div class="mt-1" style="color:rgba(226,232,240,.6)">CPF: 079.607.886-69</div>
                        <div style="color:rgba(226,232,240,.6)">Rua Agostinho Silvino Teixeira de Resende, 200 — Três Cruzes, Leopoldina/MG</div>
                        <div class="mt-1">E-mail: <a href="mailto:contato@invexa-app.com.br">contato@invexa-app.com.br</a></div>
                    </div>
                </div>

                {{-- 2 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-grid"></i>2. Descrição do Serviço</h2>
                    <p>O INVEXA é uma plataforma SaaS de gestão empresarial voltada para pequenas e médias empresas, oferecendo:</p>
                    <ul class="mt-2" style="color:rgba(226,232,240,.7);font-size:.875rem;line-height:2">
                        @foreach(['Controle de vendas e PDV (ponto de venda)', 'Gestão de estoque e produtos', 'Controle financeiro (contas a pagar e a receber)', 'Cadastro de clientes e fornecedores', 'Relatórios e dashboards analíticos', 'Gestão de usuários com controle de acesso por perfil'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- 3 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-person-plus"></i>3. Cadastro e Conta</h2>
                    <p><strong style="color:#f1f5f9">3.1 Requisitos:</strong> O usuário deve ter capacidade civil plena (18 anos ou mais), fornecer informações verdadeiras e manter a confidencialidade de suas credenciais.</p>
                    <p><strong style="color:#f1f5f9">3.2 Responsabilidade:</strong> O usuário é integralmente responsável por todas as atividades realizadas em sua conta. Em caso de acesso não autorizado, notifique imediatamente <a href="mailto:contato@invexa-app.com.br" style="color:var(--electric)">contato@invexa-app.com.br</a>.</p>
                    <p><strong style="color:#f1f5f9">3.3 Conta por empresa:</strong> Cada conta está vinculada a uma empresa. A criação de múltiplas contas para contornar limitações de plano não é permitida.</p>
                </div>

                {{-- 4 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-credit-card"></i>4. Planos e Pagamentos</h2>
                    <p><strong style="color:#f1f5f9">4.1 Planos:</strong> Diferentes planos com funcionalidades distintas estão disponíveis em <a href="{{ route('upgrade') }}" style="color:var(--electric)">invexa-app.com.br/upgrade</a>.</p>
                    <p><strong style="color:#f1f5f9">4.2 Trial:</strong> Novos usuários podem ter acesso a período de avaliação gratuito conforme condições vigentes no momento do cadastro.</p>
                    <p><strong style="color:#f1f5f9">4.3 Cobrança:</strong> Planos cobrados de forma recorrente (mensal ou anual). Processamento via Stripe, Inc. (PCI DSS). Preços em Reais (BRL), sujeitos a alteração com aviso prévio de 30 dias.</p>
                    <p><strong style="color:#f1f5f9">4.4 Cancelamento:</strong> Pode ser feito a qualquer momento nas configurações. Vigente ao término do período pago — sem reembolso proporcional. Dados disponíveis para exportação por 30 dias após o cancelamento.</p>
                    <p><strong style="color:#f1f5f9">4.5 Inadimplência:</strong> Em caso de falha no pagamento, até 3 tentativas automáticas. Persistindo, o acesso será suspenso até regularização.</p>
                </div>

                {{-- 5 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-check2-circle"></i>5. Uso Aceitável</h2>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="can-card">
                                <div class="card-title"><i class="bi bi-check-circle-fill me-1"></i>Você pode</div>
                                <ul>
                                    <li><span>•</span>Usar para gerenciar sua própria empresa</li>
                                    <li><span>•</span>Cadastrar colaboradores da empresa</li>
                                    <li><span>•</span>Exportar seus próprios dados</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="cannot-card">
                                <div class="card-title"><i class="bi bi-x-circle-fill me-1"></i>Você não pode</div>
                                <ul>
                                    <li><span>•</span>Usar para fins ilegais</li>
                                    <li><span>•</span>Revender ou sublicenciar o acesso</li>
                                    <li><span>•</span>Realizar engenharia reversa</li>
                                    <li><span>•</span>Atacar ou sobrecarregar os servidores</li>
                                    <li><span>•</span>Criar múltiplas contas para contornar limites</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-award"></i>6. Propriedade Intelectual</h2>
                    <p><strong style="color:#f1f5f9">Seus dados:</strong> Os dados inseridos por você pertencem exclusivamente a você. O INVEXA os utiliza apenas para prestar o serviço.</p>
                    <p><strong style="color:#f1f5f9">Plataforma:</strong> O código-fonte, design, marca e logotipo INVEXA são de propriedade de Augusto Corrêa Castilho, protegidos pela legislação brasileira de direitos autorais e propriedade industrial.</p>
                </div>

                {{-- 7 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-activity"></i>7. Disponibilidade</h2>
                    <p>O INVEXA empreende esforços para manter a plataforma disponível <strong style="color:#f1f5f9">24h/7d</strong> com meta de <strong style="color:#f1f5f9">99,5% ao mês</strong>. Não constituem violação: manutenções programadas (comunicadas com 24h de antecedência), interrupções por terceiros ou casos de força maior.</p>
                </div>

                {{-- 8 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-exclamation-triangle"></i>8. Limitação de Responsabilidade</h2>
                    <p>O INVEXA não se responsabiliza por decisões de negócio tomadas com base na plataforma, nem por perdas decorrentes de uso indevido pelo usuário. A responsabilidade total fica limitada ao valor pago nos últimos <strong style="color:#f1f5f9">3 meses</strong> de assinatura. Nada exclui responsabilidade por dolo ou fraude.</p>
                </div>

                {{-- 9 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-x-octagon"></i>9. Encerramento de Conta</h2>
                    <p>O INVEXA pode suspender ou encerrar contas por: violação destes Termos, uso ilegal ou fraudulento, inadimplência não regularizada ou solicitação do próprio usuário. Em caso de encerramento por violação, não haverá reembolso.</p>
                </div>

                {{-- 10 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-journal-text"></i>10. Disposições Gerais</h2>
                    <p><strong style="color:#f1f5f9">Lei aplicável:</strong> Legislação da República Federativa do Brasil.</p>
                    <p><strong style="color:#f1f5f9">Foro:</strong> Comarca de Leopoldina/MG, com renúncia a qualquer outro.</p>
                    <p><strong style="color:#f1f5f9">Alterações:</strong> Comunicadas com antecedência mínima de 15 dias. O uso continuado implica aceitação.</p>
                </div>

                {{-- 11 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-envelope"></i>11. Contato</h2>
                    <div class="highlight-card">
                        <div><i class="bi bi-envelope me-2" style="color:var(--sky)"></i><strong style="color:#f1f5f9">E-mail:</strong> <a href="mailto:contato@invexa-app.com.br">contato@invexa-app.com.br</a></div>
                        <div class="mt-2"><i class="bi bi-person me-2" style="color:var(--sky)"></i><strong style="color:#f1f5f9">Responsável:</strong> Augusto Corrêa Castilho — Leopoldina/MG</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="lp-footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="7" fill="#0D1929"/>
                        <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                        <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
                    </svg>
                    <span class="fw-bold" style="color:rgba(226,232,240,.6);">INVEXA</span>
                </div>
                <div>Gestão de Estoque e Vendas</div>
            </div>
            <div class="col-md-4 text-md-center mb-3 mb-md-0">
                <a href="{{ url('/') }}">Início</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="{{ route('privacidade') }}">Privacidade</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="{{ route('termos') }}" style="color:var(--electric)">Termos de Uso</a>
            </div>
            <div class="col-md-4 text-md-end">
                <div>© {{ date('Y') }} Invexa · Desenvolvido por</div>
                <a href="https://www.instagram.com/castilho_digital/" target="_blank">
                    <i class="bi bi-instagram me-1"></i>Castilho Soluções Digitais
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
