<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user  = Auth::user();

        $salesToday       = null;
        $revenueToday     = null;
        $billsDueToday    = null;
        $receivablesToday = null;
        $lowStockAlert    = 0;

        if ($user && $user->isGerente()) {
            $companyId = $user->company_id;

            // Vendas do dia (excluindo canceladas)
            $salesToday = Sale::where('company_id', $companyId)
                ->whereDate('created_at', $today)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            // Receita do dia
            $revenueToday = Sale::where('company_id', $companyId)
                ->whereDate('created_at', $today)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total');

            // Contas a pagar vencidas ou vencendo hoje
            $billsDueToday = Bill::where('company_id', $companyId)
                ->whereIn('status', ['pending', 'overdue'])
                ->whereDate('due_date', '<=', $today)
                ->count();

            // Contas a receber previstas para hoje
            $receivablesToday = Receivable::where('company_id', $companyId)
                ->where('status', 'pending')
                ->whereDate('due_date', $today)
                ->count();

            // Produtos ativos com estoque abaixo do mínimo definido
            $lowStockAlert = Product::where('company_id', $companyId)
                ->where('active', 1)
                ->where('min_quantity', '>', 0)
                ->whereColumn('quantity', '<=', 'min_quantity')
                ->count();
        }

        return view('home', compact(
            'salesToday',
            'revenueToday',
            'billsDueToday',
            'receivablesToday',
            'lowStockAlert'
        ));
    }
}
