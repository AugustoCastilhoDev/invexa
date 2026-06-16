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

        $request->validate([
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

        // Mapeamento: nome do campo no form => coluna real no banco
        $data = [
            'focusnfe_ambiente'  => $request->ambiente_nfe,
            'ie'                 => $request->inscricao_estadual,
            'im'                 => $request->inscricao_municipal,
            'crt'                => $request->regime_tributario,
            'nfe_serie'          => $request->serie_nfe,
            'nfe_numero_atual'   => $request->proximo_numero_nfe,
        ];

        // Token Focus NFe: só atualiza se vier preenchido
        if (filled($request->focusnfe_token)) {
            $data['focusnfe_token'] = $request->focusnfe_token;
        }

        // CSC (NFCe): só atualiza se vier preenchido
        if (filled($request->csc_token)) {
            $data['csc_token'] = $request->csc_token;
        }
        if (filled($request->csc_id)) {
            $data['csc_id'] = $request->csc_id;
        }

        $company->update($data);

        return back()->with('success', 'Configurações fiscais salvas com sucesso.');
    }
}
