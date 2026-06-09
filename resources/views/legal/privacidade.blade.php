<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade — Invexa</title>
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
        .legal-section ul { padding-left: 1.25rem; }
        .legal-section ul li { margin-bottom: .3rem; }

        /* CARDS */
        .info-card {
            background: rgba(13,25,41,.8);
            border: 1px solid rgba(14,165,233,.12);
            border-radius: 12px; padding: 16px 20px;
            font-size: .875rem;
        }
        .info-card strong { color: #f1f5f9; }
        .info-card a { color: var(--electric); text-decoration: none; }
        .info-card a:hover { color: var(--ice); }

        .highlight-card {
            background: rgba(14,165,233,.07);
            border: 1px solid rgba(14,165,233,.2);
            border-radius: 12px; padding: 16px 20px;
            font-size: .875rem; color: rgba(226,232,240,.75);
        }
        .highlight-card a { color: var(--electric); text-decoration: none; }

        /* CHECK LIST */
        .check-list { list-style: none; padding: 0; }
        .check-list li {
            display: flex; align-items: flex-start; gap: .6rem;
            padding: .4rem 0; font-size: .875rem; color: rgba(226,232,240,.7);
        }
        .check-list li i { color: var(--sky); flex-shrink: 0; margin-top: .15rem; }

        /* TABLE */
        .legal-table {
            width: 100%; font-size: .82rem; border-collapse: separate; border-spacing: 0;
            border: 1px solid rgba(14,165,233,.12); border-radius: 10px; overflow: hidden;
        }
        .legal-table thead tr { background: rgba(14,165,233,.1); }
        .legal-table thead th {
            padding: 10px 14px; font-weight: 600; color: var(--electric);
            font-size: .75rem; letter-spacing: .05em; text-transform: uppercase;
            border-bottom: 1px solid rgba(14,165,233,.15);
        }
        .legal-table tbody tr { border-bottom: 1px solid rgba(14,165,233,.06); transition: background .15s; }
        .legal-table tbody tr:last-child { border-bottom: none; }
        .legal-table tbody tr:hover { background: rgba(14,165,233,.05); }
        .legal-table td { padding: 10px 14px; color: rgba(226,232,240,.7); }
        .legal-table td:first-child { font-weight: 500; color: #e2e8f0; }

        /* RIGHTS GRID */
        .rights-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        @media (max-width: 576px) { .rights-grid { grid-template-columns: 1fr; } }
        .right-card {
            background: rgba(13,25,41,.7);
            border: 1px solid rgba(14,165,233,.1);
            border-radius: 10px; padding: 12px 14px;
        }
        .right-card .right-title { font-size: .82rem; font-weight: 600; color: #f1f5f9; margin-bottom: 2px; }
        .right-card .right-desc { font-size: .75rem; color: rgba(148,163,184,.7); }

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
        <div class="legal-badge"><i class="bi bi-shield-check"></i>LGPD — Lei nº 13.709/2018</div>
        <h1>Política de Privacidade</h1>
        <p>Versão 1.0 — Vigência: Junho de 2026</p>
    </div>
</div>

{{-- CONTENT --}}
<div class="legal-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- 1 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-building"></i>1. Identificação do Controlador</h2>
                    <p>O INVEXA é um sistema de gestão empresarial desenvolvido e operado por:</p>
                    <div class="info-card mt-3">
                        <div><strong>Augusto Corrêa Castilho</strong></div>
                        <div class="mt-1" style="color:rgba(226,232,240,.6)">CPF: 079.607.886-69</div>
                        <div style="color:rgba(226,232,240,.6)">Rua Agostinho Silvino Teixeira de Resende, 200 — Três Cruzes, Leopoldina/MG</div>
                        <div class="mt-1">E-mail: <a href="mailto:contato@invexa-app.com.br">contato@invexa-app.com.br</a></div>
                    </div>
                </div>

                {{-- 2 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-person-badge"></i>2. Encarregado de Dados (DPO)</h2>
                    <p>O Encarregado responsável por receber comunicações e solicitações sobre dados pessoais é <strong style="color:#f1f5f9">Augusto Corrêa Castilho</strong>, pelo e-mail <a href="mailto:contato@invexa-app.com.br" style="color:var(--electric)">contato@invexa-app.com.br</a>. Prazo de resposta: até <strong style="color:#f1f5f9">15 dias úteis</strong>.</p>
                </div>

                {{-- 3 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-database"></i>3. Dados Coletados</h2>
                    <h3>3.1 Dados cadastrais</h3>
                    <p>Nome completo, endereço de e-mail, número de telefone e cargo/função na empresa.</p>
                    <h3>3.2 Dados da empresa</h3>
                    <p>Razão social, nome fantasia, CNPJ ou CPF, endereço e dados de contato corporativo.</p>
                    <h3>3.3 Dados operacionais e financeiros</h3>
                    <p>Informações de vendas, produtos, clientes, fornecedores, estoque e movimentações financeiras cadastradas pelo próprio usuário.</p>
                    <h3>3.4 Dados de pagamento</h3>
                    <p>Os dados de cartão de crédito <strong style="color:#f1f5f9">não são armazenados pelo INVEXA</strong>. O processamento é realizado diretamente pelo Stripe, Inc. (PCI DSS). O INVEXA recebe apenas confirmações (ex.: últimos 4 dígitos, bandeira).</p>
                    <h3>3.5 Dados de acesso e uso</h3>
                    <p>Endereço IP, tipo de dispositivo, navegador, páginas acessadas, horários e logs de autenticação.</p>
                </div>

                {{-- 4 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-bullseye"></i>4. Finalidades do Tratamento</h2>
                    <div class="table-responsive">
                        <table class="legal-table">
                            <thead>
                                <tr>
                                    <th>Finalidade</th>
                                    <th>Base Legal (LGPD)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Criação e gerenciamento de conta</td><td>Execução de contrato (art. 7º, V)</td></tr>
                                <tr><td>Prestação dos serviços contratados</td><td>Execução de contrato (art. 7º, V)</td></tr>
                                <tr><td>Processamento de pagamentos</td><td>Execução de contrato (art. 7º, V)</td></tr>
                                <tr><td>Envio de e-mails transacionais</td><td>Execução de contrato (art. 7º, V)</td></tr>
                                <tr><td>Comunicações de marketing</td><td>Legítimo interesse / Consentimento (art. 7º, IX e I)</td></tr>
                                <tr><td>Suporte técnico</td><td>Execução de contrato (art. 7º, V)</td></tr>
                                <tr><td>Prevenção a fraudes e segurança</td><td>Legítimo interesse (art. 7º, IX)</td></tr>
                                <tr><td>Cumprimento de obrigações legais</td><td>Obrigação legal (art. 7º, II)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-share"></i>5. Compartilhamento com Terceiros</h2>
                    <div class="table-responsive">
                        <table class="legal-table">
                            <thead>
                                <tr><th>Parceiro</th><th>Finalidade</th><th>País</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Stripe, Inc.</td><td>Processamento de pagamentos</td><td>EUA</td></tr>
                                <tr><td>Resend</td><td>Envio de e-mails transacionais</td><td>EUA</td></tr>
                                <tr><td>Hostinger</td><td>Hospedagem da aplicação</td><td>LT / BR</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3">O INVEXA <strong style="color:#f1f5f9">não vende, aluga nem cede</strong> dados pessoais a terceiros para fins comerciais próprios.</p>
                </div>

                {{-- 6 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-shield-lock"></i>6. Armazenamento e Segurança</h2>
                    <p>Os dados são armazenados em servidores seguros (Hostinger), com as seguintes medidas implementadas:</p>
                    <ul class="check-list mt-2">
                        <li><i class="bi bi-check-circle-fill"></i>Comunicação criptografada via TLS/HTTPS</li>
                        <li><i class="bi bi-check-circle-fill"></i>Autenticação com suporte a dois fatores (2FA)</li>
                        <li><i class="bi bi-check-circle-fill"></i>Controle de acesso por perfil (RBAC)</li>
                        <li><i class="bi bi-check-circle-fill"></i>Sessões protegidas por cookies seguros</li>
                        <li><i class="bi bi-check-circle-fill"></i>Backups automáticos com retenção de 30 dias</li>
                        <li><i class="bi bi-check-circle-fill"></i>Logs de auditoria de ações críticas</li>
                    </ul>
                </div>

                {{-- 7 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-clock-history"></i>7. Retenção de Dados</h2>
                    <p>Os dados são mantidos pelo tempo necessário para cumprir as finalidades coletadas, obrigações legais (mínimo 5 anos para registros fiscais) ou exercício de direitos em processos. Após o encerramento da conta, os dados ficam disponíveis para exportação por <strong style="color:#f1f5f9">30 dias</strong>, após o que serão eliminados ou anonimizados.</p>
                </div>

                {{-- 8 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-person-check"></i>8. Direitos dos Titulares</h2>
                    <p>Nos termos do art. 18 da LGPD, você tem direito a:</p>
                    <div class="rights-grid mt-3">
                        @foreach([
                            ['Confirmação e acesso', 'Saber se seus dados são tratados e obter cópia'],
                            ['Correção', 'Atualizar dados incompletos ou incorretos'],
                            ['Eliminação', 'Solicitar exclusão dos dados tratados com consentimento'],
                            ['Portabilidade', 'Receber seus dados em formato estruturado'],
                            ['Revogação do consentimento', 'Retirar o consentimento a qualquer momento'],
                            ['Oposição', 'Opor-se ao tratamento por legítimo interesse'],
                            ['Revisão automatizada', 'Solicitar revisão de decisões automatizadas'],
                            ['Reclamação à ANPD', 'Apresentar reclamação à autoridade nacional'],
                        ] as [$title, $desc])
                        <div class="right-card">
                            <div class="right-title">{{ $title }}</div>
                            <div class="right-desc">{{ $desc }}</div>
                        </div>
                        @endforeach
                    </div>
                    <p class="mt-3">Para exercer qualquer direito, envie e-mail para <a href="mailto:contato@invexa-app.com.br" style="color:var(--electric)">contato@invexa-app.com.br</a> com assunto <em>"Direitos LGPD — [seu nome]"</em>. Prazo: até 15 dias úteis.</p>
                </div>

                {{-- 9 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-cookie"></i>9. Cookies</h2>
                    <p>O INVEXA utiliza apenas cookies estritamente necessários: <strong style="color:#f1f5f9">cookies de sessão</strong> (autenticação) e <strong style="color:#f1f5f9">cookies de segurança CSRF</strong> (proteção contra ataques). Não utilizamos cookies de rastreamento ou publicidade de terceiros.</p>
                </div>

                {{-- 10 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-pencil-square"></i>10. Alterações nesta Política</h2>
                    <p>Alterações relevantes serão comunicadas por e-mail ou aviso na plataforma com antecedência mínima de <strong style="color:#f1f5f9">15 dias</strong>. O uso continuado após a vigência das mudanças implica aceitação da nova versão.</p>
                </div>

                {{-- 11 --}}
                <div class="legal-section">
                    <h2><i class="bi bi-envelope"></i>11. Contato</h2>
                    <div class="highlight-card">
                        <div><i class="bi bi-envelope me-2" style="color:var(--sky)"></i><strong style="color:#f1f5f9">E-mail:</strong> <a href="mailto:contato@invexa-app.com.br">contato@invexa-app.com.br</a></div>
                        <div class="mt-2"><i class="bi bi-person me-2" style="color:var(--sky)"></i><strong style="color:#f1f5f9">Responsável:</strong> Augusto Corrêa Castilho — Leopoldina/MG</div>
                        <div class="mt-2" style="font-size:.78rem;color:rgba(226,232,240,.5)"><i class="bi bi-info-circle me-1"></i>Também é possível apresentar reclamação diretamente à <a href="https://www.gov.br/anpd" target="_blank">ANPD — Autoridade Nacional de Proteção de Dados</a>.</div>
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
                <a href="{{ route('privacidade') }}" style="color:var(--electric)">Privacidade</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="{{ route('termos') }}">Termos de Uso</a>
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
