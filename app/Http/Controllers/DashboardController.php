<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Production;
use App\Services\AesService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(AesService $aes)
    {
        $totalProduk   = Product::count();
        $totalProduksi = Production::count();

        $allProductions = Production::with('product')->latest()->get();

        $allProductions = $allProductions->map(function ($prod) use ($aes) {
            $plain = $aes->decrypt($prod->expiration_date);
            $prod->plain_expiry_carbon = Carbon::createFromFormat('Ymd', $plain);
            $prod->plain_expiry        = $prod->plain_expiry_carbon->format('d M Y');
            $prod->production_code     = $aes->decrypt($prod->production_code);

            // Hitung sisa hari sekali di sini, dipakai juga di view
            $prod->days_left = now()->startOfDay()->diffInDays($prod->plain_expiry_carbon, false);

            return $prod;
        });

        $hampirKedaluwarsa = $allProductions->filter(function ($prod) {
            return $prod->days_left >= 0 && $prod->days_left <= 7;
        });

        return view('dashboard.index', [
            'totalProduk'       => $totalProduk,
            'totalProduksi'     => $totalProduksi,
            'hampirKedaluwarsa' => $hampirKedaluwarsa->count(),
            // urutkan dari yang paling mendesak (sisa hari paling kecil)
            'akanKedaluwarsa'   => $hampirKedaluwarsa->sortBy('days_left')->values(),
            'adminName'         => Auth::user()->name,
        ]);
    }
}