<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Production;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Product::count();

        $totalProduksi = Production::count();
        $hampirKedaluwarsa = Production::whereDate('expiration_date', '>=', now())
                                       ->whereDate('expiration_date', '<=', now()->addDays(7))
                                       ->count();
        $recentQr = Production::with('product')
                               ->latest()
                               ->take(5)
                               ->get();

        return view('dashboard.index', [
    'totalProduk'       => $totalProduk,
    'totalProduksi'     => $totalProduksi,
    'hampirKedaluwarsa' => $hampirKedaluwarsa,
    'recentQr'          => $recentQr,
    'adminName'         => Auth::user()->name,
]);
    }
}
