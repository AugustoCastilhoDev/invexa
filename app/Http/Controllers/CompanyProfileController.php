<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        abort_unless($company, 403);
        return view('settings.company', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;
        abort_unless($company, 403);

        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'nullable|email|max:120',
            'phone'   => 'nullable|string|max:20',
            'cnpj'    => 'nullable|string|max:18',
            'address' => 'nullable|string|max:255',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Remove logo antigo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    public function destroyLogo()
    {
        $company = auth()->user()->company;
        abort_unless($company && $company->logo, 404);

        Storage::disk('public')->delete($company->logo);
        $company->update(['logo' => null]);

        return back()->with('success', 'Logo removido com sucesso.');
    }
    public function updateAsaas(Request $request)
    {
        abort_unless(config('features.pix_enabled'), 404);

        $request->validate([
            'asaas_api_key'     => ['nullable', 'string', 'max:255'],
            'asaas_environment' => ['required', 'in:sandbox,production'],
        ]);

        $company = auth()->user()->company;
        $data    = ['asaas_environment' => $request->asaas_environment];

        if ($request->boolean('remove_api_key')) {
            $data['asaas_api_key']   = null;
            $data['asaas_wallet_id'] = null;
            $company->update($data);
            return back()->with('success', 'Integração Asaas removida.');
        }

        if ($request->filled('asaas_api_key')) {
            $data['asaas_api_key'] = encrypt($request->asaas_api_key);
        }

        $company->update($data);

        if ($request->filled('asaas_api_key')) {
            $company->refresh();
            $service = new \App\Services\PixPaymentService($company);
            if (! $service->testConnection()) {
                return back()->with('warning', 'Chave salva, mas não foi possível validar a conexão com o Asaas. Verifique a chave e o ambiente selecionado.');
            }
            return back()->with('success', 'Integração Asaas configurada e conexão validada!');
        }

        return back()->with('success', 'Configurações Asaas atualizadas.');
    }

}
