<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    // GET /settings/api
    public function index()
    {
        $tokens = auth()->user()->tokens()->latest()->get();

        return view('settings.api-tokens', compact('tokens'));
    }

    // POST /settings/api/tokens
    public function store(Request $request)
    {
        $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
        ], [
            'token_name.required' => 'Informe um nome para o token.',
        ]);

        $token = auth()->user()->createToken($request->token_name)->plainTextToken;

        return redirect()->route('settings.api')
            ->with('new_token', $token)
            ->with('success', 'Token criado! Copie agora — ele não será exibido novamente.');
    }

    // DELETE /settings/api/tokens/{id}
    public function destroy(Request $request, int $tokenId)
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('settings.api')
            ->with('success', 'Token revogado com sucesso.');
    }
}
