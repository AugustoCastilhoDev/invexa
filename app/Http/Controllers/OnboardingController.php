<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function show()
    {
        $company = auth()->user()->company;

        if ($company->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Passo atual baseado no progresso salvo na session
        $step = session('onboarding_step', 1);

        return view('onboarding.wizard', compact('company', 'step'));
    }

    /**
     * Passo 1: dados da empresa
     */
    public function store(Request $request)
    {
        $step = (int) $request->input('step', 1);

        // ── Passo 1: empresa
        if ($step === 1) {
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
                'name'    => $request->name,
                'cnpj'    => $request->cnpj,
                'phone'   => $request->phone,
                'address' => $request->address,
            ];

            if ($request->hasFile('logo')) {
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }

            $company->update($data);
            session(['onboarding_step' => 2]);

            return redirect()->route('onboarding.show');
        }

        // ── Passo 2: primeiro produto (opcional)
        if ($step === 2) {
            if ($request->filled('product_name')) {
                $request->validate([
                    'product_name'  => ['required', 'string', 'max:150'],
                    'product_price' => ['required', 'numeric', 'min:0'],
                    'product_qty'   => ['required', 'integer', 'min:0'],
                ]);

                $company = auth()->user()->company;

                // Cria ou reutiliza categoria padrão "Geral"
                $category = Category::firstOrCreate(
                    ['company_id' => $company->id, 'name' => 'Geral'],
                    ['active' => true]
                );

                Product::create([
                    'company_id'   => $company->id,
                    'category_id'  => $category->id,
                    'name'         => $request->product_name,
                    'sale_price'   => $request->product_price,
                    'quantity'     => $request->product_qty,
                    'min_quantity' => 0,
                    'active'       => true,
                ]);
            }

            session(['onboarding_step' => 3]);
            return redirect()->route('onboarding.show');
        }

        // ── Passo 3: primeiro cliente (opcional)
        if ($step === 3) {
            if ($request->filled('customer_name')) {
                $request->validate([
                    'customer_name'  => ['required', 'string', 'max:150'],
                    'customer_email' => ['nullable', 'email', 'max:150'],
                    'customer_phone' => ['nullable', 'string', 'max:20'],
                ]);

                $company = auth()->user()->company;

                Customer::create([
                    'company_id' => $company->id,
                    'name'       => $request->customer_name,
                    'email'      => $request->customer_email,
                    'phone'      => $request->customer_phone,
                    'active'     => true,
                ]);
            }

            // Finaliza onboarding
            auth()->user()->company->update(['onboarding_completed' => true]);
            session()->forget('onboarding_step');

            return redirect()->route('dashboard')
                ->with('success', 'Bem-vindo ao Invexa! Sua empresa foi configurada com sucesso. 🚀');
        }

        return redirect()->route('onboarding.show');
    }

    public function skip()
    {
        auth()->user()->company->update(['onboarding_completed' => true]);
        session()->forget('onboarding_step');
        return redirect()->route('dashboard');
    }
}
