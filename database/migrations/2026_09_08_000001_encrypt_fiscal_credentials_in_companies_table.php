<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Criptografa focusnfe_token e csc_token que ainda estejam em texto puro.
     * Idempotente: usa Crypt::decryptString() para detectar valores já
     * criptografados e pula-los, então é seguro rodar mais de uma vez.
     */
    public function up(): void
    {
        // O texto criptografado do Laravel (IV + MAC + JSON + base64) sempre
        // passa de 255 caracteres — essas colunas eram VARCHAR(255), igual
        // asaas_api_key era antes de virar TEXT. Alarga antes de criptografar.
        // SQLite (usado nos testes) não tem MODIFY COLUMN e nem aplica limite
        // de tamanho em VARCHAR, então não precisa de nada lá.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE companies MODIFY focusnfe_token TEXT NULL');
            DB::statement('ALTER TABLE companies MODIFY csc_token TEXT NULL');
        }

        $companies = DB::table('companies')
            ->whereNotNull('focusnfe_token')
            ->orWhereNotNull('csc_token')
            ->select('id', 'focusnfe_token', 'csc_token')
            ->get();

        foreach ($companies as $company) {
            $update = [];

            if (filled($company->focusnfe_token) && ! $this->isAlreadyEncrypted($company->focusnfe_token)) {
                $update['focusnfe_token'] = Crypt::encryptString($company->focusnfe_token);
            }

            if (filled($company->csc_token) && ! $this->isAlreadyEncrypted($company->csc_token)) {
                $update['csc_token'] = Crypt::encryptString($company->csc_token);
            }

            if (! empty($update)) {
                DB::table('companies')->where('id', $company->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        $companies = DB::table('companies')
            ->whereNotNull('focusnfe_token')
            ->orWhereNotNull('csc_token')
            ->select('id', 'focusnfe_token', 'csc_token')
            ->get();

        foreach ($companies as $company) {
            $update = [];

            if (filled($company->focusnfe_token) && $this->isAlreadyEncrypted($company->focusnfe_token)) {
                $update['focusnfe_token'] = Crypt::decryptString($company->focusnfe_token);
            }

            if (filled($company->csc_token) && $this->isAlreadyEncrypted($company->csc_token)) {
                $update['csc_token'] = Crypt::decryptString($company->csc_token);
            }

            if (! empty($update)) {
                DB::table('companies')->where('id', $company->id)->update($update);
            }
        }
    }

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            return false;
        }
    }
};
