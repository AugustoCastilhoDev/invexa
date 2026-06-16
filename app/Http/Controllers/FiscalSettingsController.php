<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FiscalSettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('settings.fiscal', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'focusnfe_token'     => 'nullable|string|max:255',
            'ambiente_nfe'       => 'required|in:homologacao,producao',
            'inscricao_estadual' => 'nullable|string|max:20',
            'inscricao_municipal'=> 'nullable|string|max:20',
            'regime_tributario'  => 'required|in:1,2,3',
            'serie_nfe'          => 'required|string|max:3',
            'proximo_numero_nfe' => 'required|integer|min:1',
            'csc_token'          => 'nullable|string|max:255',
            'csc_id'             => 'nullable|string|max:10',
        ]);

        // Se o token vier vazio, mantém o atual (segurança)
        if (empty($validated['focusnfe_token'])) {
            unset($validated['focusnfe_token']);
        }
        if (empty($validated['csc_token'])) {
            unset($validated['csc_token']);
        }

        $company->update($validated);

        return back()->with('success', 'Configurações fiscais salvas com sucesso.');
    }
}
