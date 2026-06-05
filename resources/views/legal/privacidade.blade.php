<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade — INVEXA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-emerald-600 font-bold text-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                INVEXA
            </a>
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                &larr; Voltar ao início
            </a>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-4xl mx-auto px-6 py-12">

        <div class="mb-10">
            <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">LGPD — Lei nº 13.709/2018</span>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Política de Privacidade</h1>
            <p class="text-gray-500 text-sm">Versão 1.0 &mdash; Vigência: Junho de 2026</p>
        </div>

        <div class="prose prose-gray max-w-none space-y-10">

            {{-- 1 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">1. Identificação do Controlador</h2>
                <p class="text-gray-600 leading-relaxed">O INVEXA é um sistema de gestão empresarial desenvolvido e operado por:</p>
                <div class="mt-3 bg-gray-100 rounded-lg p-4 text-sm text-gray-700 space-y-1">
                    <p><strong>Augusto Corrêa Castilho</strong></p>
                    <p>CPF: 079.607.886-69</p>
                    <p>Rua Agostinho Silvino Teixeira de Resende, 200 — Três Cruzes, Leopoldina/MG</p>
                    <p>E-mail: <a href="mailto:contato@invexa-app.com.br" class="text-emerald-600 hover:underline">contato@invexa-app.com.br</a></p>
                </div>
            </section>

            {{-- 2 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">2. Encarregado de Dados (DPO)</h2>
                <p class="text-gray-600 leading-relaxed">O Encarregado responsável por receber comunicações e solicitações sobre dados pessoais é <strong>Augusto Corrêa Castilho</strong>, pelo e-mail <a href="mailto:contato@invexa-app.com.br" class="text-emerald-600 hover:underline">contato@invexa-app.com.br</a>. Prazo de resposta: até <strong>15 dias úteis</strong>.</p>
            </section>

            {{-- 3 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">3. Dados Coletados</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">3.1 Dados cadastrais</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Nome completo, endereço de e-mail, número de telefone e cargo/função na empresa.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">3.2 Dados da empresa</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Razão social, nome fantasia, CNPJ ou CPF, endereço e dados de contato corporativo.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">3.3 Dados operacionais e financeiros</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Informações de vendas, produtos, clientes, fornecedores, estoque e movimentações financeiras cadastradas pelo próprio usuário.</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">3.4 Dados de pagamento</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Os dados de cartão de crédito <strong>não são armazenados pelo INVEXA</strong>. O processamento é realizado diretamente pelo Stripe, Inc. (PCI DSS). O INVEXA recebe apenas confirmações (ex.: últimos 4 dígitos, bandeira).</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">3.5 Dados de acesso e uso</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Endereço IP, tipo de dispositivo, navegador, páginas acessadas, horários e logs de autenticação.</p>
                    </div>
                </div>
            </section>

            {{-- 4 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">4. Finalidades do Tratamento</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Finalidade</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Base Legal (LGPD)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Criação e gerenciamento de conta</td><td class="px-4 py-3 text-gray-600">Execução de contrato (art. 7º, V)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Prestação dos serviços contratados</td><td class="px-4 py-3 text-gray-600">Execução de contrato (art. 7º, V)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Processamento de pagamentos</td><td class="px-4 py-3 text-gray-600">Execução de contrato (art. 7º, V)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Envio de e-mails transacionais</td><td class="px-4 py-3 text-gray-600">Execução de contrato (art. 7º, V)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Comunicações de marketing</td><td class="px-4 py-3 text-gray-600">Legítimo interesse / Consentimento (art. 7º, IX e I)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Suporte técnico</td><td class="px-4 py-3 text-gray-600">Execução de contrato (art. 7º, V)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Prevenção a fraudes e segurança</td><td class="px-4 py-3 text-gray-600">Legítimo interesse (art. 7º, IX)</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-gray-600">Cumprimento de obrigações legais</td><td class="px-4 py-3 text-gray-600">Obrigação legal (art. 7º, II)</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- 5 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">5. Compartilhamento com Terceiros</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Parceiro</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Finalidade</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">País</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-700">Stripe, Inc.</td><td class="px-4 py-3 text-gray-600">Processamento de pagamentos</td><td class="px-4 py-3 text-gray-600">EUA</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-700">Resend</td><td class="px-4 py-3 text-gray-600">Envio de e-mails transacionais</td><td class="px-4 py-3 text-gray-600">EUA</td></tr>
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-700">Hostinger</td><td class="px-4 py-3 text-gray-600">Hospedagem da aplicação</td><td class="px-4 py-3 text-gray-600">LT / BR</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-gray-600 text-sm mt-3 leading-relaxed">O INVEXA <strong>não vende, aluga nem cede</strong> dados pessoais a terceiros para fins comerciais próprios.</p>
            </section>

            {{-- 6 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">6. Armazenamento e Segurança</h2>
                <p class="text-gray-600 leading-relaxed mb-3">Os dados são armazenados em servidores seguros (Hostinger), com as seguintes medidas implementadas:</p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Comunicação criptografada via TLS/HTTPS</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Autenticação com suporte a dois fatores (2FA)</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Controle de acesso por perfil (RBAC)</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Sessões protegidas por cookies seguros</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Backups automáticos com retenção de 30 dias</li>
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">✓</span> Logs de auditoria de ações críticas</li>
                </ul>
            </section>

            {{-- 7 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">7. Retenção de Dados</h2>
                <p class="text-gray-600 leading-relaxed">Os dados são mantidos pelo tempo necessário para cumprir as finalidades coletadas, obrigações legais (mínimo 5 anos para registros fiscais) ou exercício de direitos em processos. Após o encerramento da conta, os dados ficam disponíveis para exportação por <strong>30 dias</strong>, após o que serão eliminados ou anonimizados.</p>
            </section>

            {{-- 8 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">8. Direitos dos Titulares</h2>
                <p class="text-gray-600 leading-relaxed mb-3">Nos termos do art. 18 da LGPD, você tem direito a:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="font-medium text-gray-800 text-sm">{{ $title }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="text-gray-600 text-sm mt-4">Para exercer qualquer direito, envie e-mail para <a href="mailto:contato@invexa-app.com.br" class="text-emerald-600 hover:underline">contato@invexa-app.com.br</a> com assunto <em>"Direitos LGPD — [seu nome]"</em>. Prazo: até 15 dias úteis.</p>
            </section>

            {{-- 9 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">9. Cookies</h2>
                <p class="text-gray-600 leading-relaxed">O INVEXA utiliza apenas cookies estritamente necessários: <strong>cookies de sessão</strong> (autenticação) e <strong>cookies de segurança CSRF</strong> (proteção contra ataques). Não utilizamos cookies de rastreamento ou publicidade de terceiros.</p>
            </section>

            {{-- 10 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">10. Alterações nesta Política</h2>
                <p class="text-gray-600 leading-relaxed">Alterações relevantes serão comunicadas por e-mail ou aviso na plataforma com antecedência mínima de <strong>15 dias</strong>. O uso continuado após a vigência das mudanças implica aceitação da nova versão.</p>
            </section>

            {{-- 11 --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">11. Contato</h2>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm">
                    <p class="text-emerald-800"><strong>E-mail:</strong> <a href="mailto:contato@invexa-app.com.br" class="underline">contato@invexa-app.com.br</a></p>
                    <p class="text-emerald-800 mt-1"><strong>Responsável:</strong> Augusto Corrêa Castilho — Leopoldina/MG</p>
                    <p class="text-emerald-700 mt-2 text-xs">Também é possível apresentar reclamação diretamente à <a href="https://www.gov.br/anpd" target="_blank" class="underline">ANPD — Autoridade Nacional de Proteção de Dados</a>.</p>
                </div>
            </section>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white mt-16">
        <div class="max-w-4xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} INVEXA — Augusto Corrêa Castilho. Todos os direitos reservados.</p>
            <div class="flex gap-4">
                <a href="{{ route('privacidade') }}" class="text-emerald-600 hover:underline">Privacidade</a>
                <a href="{{ route('termos') }}" class="hover:text-emerald-600">Termos de Uso</a>
            </div>
        </div>
    </footer>

</body>
</html>
