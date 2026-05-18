<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q         = trim($request->get('q', ''));
        $companyId = auth()->user()->company_id;

        if (strlen($q) < 2) {
            return view('search.results', [
                'q'         => $q,
                'customers' => collect(),
                'products'  => collect(),
                'suppliers' => collect(),
                'sales'     => collect(),
            ]);
        }

        $like = '%' . $q . '%';

        $customers = Customer::where('company_id', $companyId)
            ->where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('cpf_cnpj', 'like', $like)
            )
            ->limit(8)
            ->get();

        $products = Product::where('company_id', $companyId)
            ->where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('description', 'like', $like)
            )
            ->limit(8)
            ->get();

        $suppliers = Supplier::where('company_id', $companyId)
            ->where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('cnpj', 'like', $like)
                ->orWhere('email', 'like', $like)
            )
            ->limit(8)
            ->get();

        $sales = Sale::with('customer')
            ->where('company_id', $companyId)
            ->where(fn($query) => $query
                ->where('customer_name', 'like', $like)
                ->orWhere('id', is_numeric($q) ? $q : -1)
            )
            ->limit(5)
            ->get();

        return view('search.results', compact('q', 'customers', 'products', 'suppliers', 'sales'));
    }
}
