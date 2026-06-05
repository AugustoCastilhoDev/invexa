<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso — INVEXA</title>
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
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">Contrato de uso da plataforma</span>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Termos de Uso</h1>
            <p class="text-gray-500 text-sm">Versão 1.0 &mdash; Vigência: Junho de 2026</p>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-10 text-sm text-amber-800">
            <strong>Atenção:</strong> Ao criar uma conta ou utilizar o INVEXA, você declara que leu e concorda com estes Termos de Uso e com a <a href="{{ route('privacidade') }}" class="underline font-medium">Política de Privacidade</a>.
        </div>

        <div class="space-y-10">

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">1. Identificação do Prestador</h2>
                <div class="bg-gray-100 rounded-lg p-4 text-sm text-gray-700 space-y-1">
                    <p><strong>Augusto Corrêa Castilho</strong></p>
                    <p>CPF: 079.607.886-69</p>
                    <p>Rua Agostinho Silvino Teixeira de Resende, 200 — Três Cruzes, Leopoldina/MG</p>
                    <p>E-mail: <a href="mailto:contato@invexa-app.com.br" class="text-emerald-600 hover:underline">contato@invexa-app.com.br</a></p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">2. Descrição do Serviço</h2>
                <p class="text-gray-600 leading-relaxed mb-3">O INVEXA é uma plataforma SaaS de gestão empresarial voltada para pequenas e médias empresas, oferecendo:</p>
                <ul class="space-y-1.5 text-sm text-gray-600">
                    @foreach(['Controle de vendas e PDV (ponto de venda)', 'Gestão de estoque e produtos', 'Controle financeiro (contas a pagar e a receber)', 'Cadastro de clientes e fornecedores', 'Relatórios e dashboards analíticos', 'Gestão de usuários com controle de acesso por perfil'] as $item)
                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5">•</span> {{ $item }}</li>
                    @endforeach
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">3. Cadastro e Conta</h2>
                <div class="space-y-3 text-gray-600 text-sm leading-relaxed">
                    <p><strong class="text-gray-800">3.1 Requisitos:</strong> O usuário deve ter capacidade civil plena (18 anos ou mais), fornecer informações verdadeiras e manter a confidencialidade de suas credenciais.</p>
                    <p><strong class="text-gray-800">3.2 Responsabilidade:</strong> O usuário é integralmente responsável por todas as atividades realizadas em sua conta. Em caso de acesso não autorizado, notifique imediatamente <a href="mailto:contato@invexa-app.com.br" class="text-emerald-600 hover:underline">contato@invexa-app.com.br</a>.</p>
                    <p><strong class="text-gray-800">3.3 Conta por empresa:</strong> Cada conta está vinculada a uma empresa. A criação de múltiplas contas para contornar limitações de plano não é permitida.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">4. Planos e Pagamentos</h2>
                <div class="space-y-3 text-gray-600 text-sm leading-relaxed">
                    <p><strong class="text-gray-800">4.1 Planos:</strong> Diferentes planos com funcionalidades distintas estão disponíveis em <a href="{{ route('upgrade') }}" class="text-emerald-600 hover:underline">invexa-app.com.br/upgrade</a>.</p>
                    <p><strong class="text-gray-800">4.2 Trial:</strong> Novos usuários podem ter acesso a período de avaliação gratuito conforme condições vigentes no momento do cadastro.</p>
                    <p><strong class="text-gray-800">4.3 Cobrança:</strong> Planos cobrados de forma recorrente (mensal ou anual). Processamento via Stripe, Inc. (PCI DSS). Preços em Reais (BRL), sujeitos a alteração com aviso prévio de 30 dias.</p>
                    <p><strong class="text-gray-800">4.4 Cancelamento:</strong> Pode ser feito a qualquer momento nas configurações. Vigente ao término do período pago — sem reembolso proporcional. Dados disponíveis para exportação por 30 dias após o cancelamento.</p>
                    <p><strong class="text-gray-800">4.5 Inadimplência:</strong> Em caso de falha no pagamento, até 3 tentativas automáticas. Persistindo, o acesso será suspenso até regularização.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">5. Uso Aceitável</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        <p class="font-semibold text-emerald-800 text-sm mb-2">✓ Você pode</p>
                        <ul class="space-y-1 text-xs text-emerald-700">
                            <li>• Usar para gerenciar sua própria empresa</li>
                            <li>• Cadastrar colaboradores da empresa</li>
                            <li>• Exportar seus próprios dados</li>
                        </ul>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="font-semibold text-red-800 text-sm mb-2">✗ Você não pode</p>
                        <ul class="space-y-1 text-xs text-red-700">
                            <li>• Usar para fins ilegais</li>
                            <li>• Revender ou sublicenciar o acesso</li>
                            <li>• Realizar engenharia reversa</li>
                            <li>• Atacar ou sobrecarregar os servidores</li>
                            <li>• Criar múltiplas contas para contornar limites</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">6. Propriedade Intelectual</h2>
                <p class="text-gray-600 text-sm leading-relaxed"><strong class="text-gray-800">Seus dados:</strong> Os dados inseridos por você pertencem exclusivamente a você. O INVEXA os utiliza apenas para prestar o serviço.<br><br><strong class="text-gray-800">Plataforma:</strong> O código-fonte, design, marca e logotipo INVEXA são de propriedade de Augusto Corrêa Castilho, protegidos pela legislação brasileira de direitos autorais e propriedade industrial.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">7. Disponibilidade</h2>
                <p class="text-gray-600 text-sm leading-relaxed">O INVEXA empreende esforços para manter a plataforma disponível <strong>24h/7d</strong> com meta de <strong>99,5% ao mês</strong>. Não constituem violação: manutenções programadas (comunicadas com 24h de antecedência), interrupções por terceiros ou casos de força maior.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">8. Limitação de Responsabilidade</h2>
                <p class="text-gray-600 text-sm leading-relaxed">O INVEXA não se responsabiliza por decisões de negócio tomadas com base na plataforma, nem por perdas decorrentes de uso indevido pelo usuário. A responsabilidade total fica limitada ao valor pago nos últimos <strong>3 meses</strong> de assinatura. Nada exclui responsabilidade por dolo ou fraude.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">9. Encerramento de Conta</h2>
                <p class="text-gray-600 text-sm leading-relaxed">O INVEXA pode suspender ou encerrar contas por: violação destes Termos, uso ilegal ou fraudulento, inadimplência não regularizada ou solicitação do próprio usuário. Em caso de encerramento por violação, não haverá reembolso.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">10. Disposições Gerais</h2>
                <div class="space-y-2 text-sm text-gray-600 leading-relaxed">
                    <p><strong class="text-gray-800">Lei aplicável:</strong> Legislação da República Federativa do Brasil.</p>
                    <p><strong class="text-gray-800">Foro:</strong> Comarca de Leopoldina/MG, com renúncia a qualquer outro.</p>
                    <p><strong class="text-gray-800">Alterações:</strong> Comunicadas com antecedência mínima de 15 dias. O uso continuado implica aceitação.</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">11. Contato</h2>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm">
                    <p class="text-emerald-800"><strong>E-mail:</strong> <a href="mailto:contato@invexa-app.com.br" class="underline">contato@invexa-app.com.br</a></p>
                    <p class="text-emerald-800 mt-1"><strong>Responsável:</strong> Augusto Corrêa Castilho — Leopoldina/MG</p>
                </div>
            </section>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white mt-16">
        <div class="max-w-4xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} INVEXA — Augusto Corrêa Castilho. Todos os direitos reservados.</p>
            <div class="flex gap-4">
                <a href="{{ route('privacidade') }}" class="hover:text-emerald-600">Privacidade</a>
                <a href="{{ route('termos') }}" class="text-emerald-600 hover:underline">Termos de Uso</a>
            </div>
        </div>
    </footer>

</body>
</html>
