<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sale;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    /**
     * Recebe notificações do Asaas por empresa.
     * Rota: POST /webhook/asaas/{company_slug}
     */
    public function handle(Request $request, string $companySlug)
    {
        $company = Company::where('slug', $companySlug)->first();

        if (! $company) {
            Log::warning("Asaas webhook: empresa não encontrada [{$companySlug}]");
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Valida token do webhook configurado no .env
        $expectedToken = config('services.asaas.webhook_token');
        if ($expectedToken && $request->header('asaas-access-token') !== $expectedToken) {
            Log::warning("Asaas webhook: token inválido para [{$companySlug}]");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event   = $request->input('event');
        $payment = $request->input('payment');

        Log::info("Asaas webhook recebido", [
            'company' => $companySlug,
            'event'   => $event,
            'payment' => $payment['id'] ?? null,
        ]);

        if ($event !== 'PAYMENT_RECEIVED' || empty($payment['id'])) {
            return response()->json(['received' => true]);
        }

        $chargeId = $payment['id'];

        // Localiza a venda pelo pix_charge_id — sem global scope (webhook não tem auth)
        $sale = Sale::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->where('pix_charge_id', $chargeId)
            ->first();

        if (! $sale) {
            Log::warning("Asaas webhook: charge_id [{$chargeId}] não encontrado para empresa [{$companySlug}]");
            return response()->json(['received' => true]);
        }

        if ($sale->status === 'concluida') {
            return response()->json(['received' => true]);
        }

        // Atualiza venda e conta a receber
        $sale->update([
            'status'      => 'concluida',
            'pix_paid_at' => now(),
        ]);

        if ($sale->receivable) {
            $sale->receivable->update([
                'status'          => 'recebida',
                'amount_received' => $sale->total,
                'received_at'     => now(),
            ]);
        }

        AuditLogger::action('sale.pix_confirmed', $sale, [
            'charge_id' => $chargeId,
            'company'   => $companySlug,
        ]);

        Log::info("Pix confirmado: venda #{$sale->sale_number} — empresa {$companySlug}");

        return response()->json(['received' => true]);
    }
}
