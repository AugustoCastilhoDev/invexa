<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function show()
    {
        $company = auth()->user()->company;

        // Se já completou onboarding, vai para o dashboard
        if ($company->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.wizard', compact('company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'cnpj'    => ['nullable', 'string', 'max:18'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'logo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
            'logo.image'    => 'O arquivo deve ser uma imagem.',
            'logo.max'      => 'A imagem deve ter no máximo 2MB.',
        ]);

        $company = auth()->user()->company;

        $data = [
            'name'                  => $request->name,
            'cnpj'                  => $request->cnpj,
            'phone'                 => $request->phone,
            'address'               => $request->address,
            'onboarding_completed'  => true,
        ];

        // Upload do logo
        if ($request->hasFile('logo')) {
            // Remove logo antigo se existir
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return redirect()->route('dashboard')
            ->with('success', 'Bem-vindo ao Invexa! Sua empresa foi configurada com sucesso. 🚀');
    }

    public function skip()
    {
        auth()->user()->company->update(['onboarding_completed' => true]);
        return redirect()->route('dashboard');
    }
}
