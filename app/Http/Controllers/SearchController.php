<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q         = $request->get('q', '');
        $companyId = auth()->user()->company_id;

        if (strlen($q) < 2) {
            return view('search.results', ['q' => $q, 'results' => []]);
        }

        $like = '%' . $q . '%';

        $products = Product::where('company_id', $companyId)
            ->where(fn($x) => $x->where('name','like',$like)->orWhere('sku','like',$like))
            ->limit(10)->get(['id','name','sku','price','quantity']);

        $customers = Customer::where('company_id', $companyId)
            ->where(fn($x) => $x->where('name','like',$like)->orWhere('email','like',$like)->orWhere('document','like',$like))
            ->limit(10)->get(['id','name','email','phone']);

        $suppliers = Supplier::where('company_id', $companyId)
            ->where(fn($x) => $x->where('name','like',$like)->orWhere('email','like',$like))
            ->limit(10)->get(['id','name','email','phone']);

        $sales = Sale::with('customer')
            ->where('company_id', $companyId)
            ->where(fn($x) => $x->where('customer_name','like',$like)->orWhere('id','like',$like))
            ->limit(10)->get(['id','customer_name','total','status','sale_date']);

        return view('search.results', compact('q','products','customers','suppliers','sales'));
    }
}
