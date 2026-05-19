<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()?->company;

        return view('upgrade', compact('company'));
    }
}
