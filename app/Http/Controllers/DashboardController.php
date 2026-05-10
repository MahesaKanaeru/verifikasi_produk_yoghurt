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

        // Ambil semua produksi dengan relasi product
        $allProductions = Production::with('product')->latest()->get();

        // Decrypt expiration_date dulu karena isinya cipher, bukan date biasa
        // Tidak bisa pakai whereDate langsung di SQL
        $allProductions = $allProductions->map(function ($prod) use ($aes) {
            $plain = $aes->decrypt($prod->expiration_date);      // → "20250607"
            $prod->plain_expiry_carbon = Carbon::createFromFormat('Ymd', $plain);
            $prod->plain_expiry        = $prod->plain_expiry_carbon->format('d M Y');
            return $prod;
        });

        // Hitung hampir kedaluwarsa di PHP (bukan SQL) karena kolom sudah cipher
        $hampirKedaluwarsa = $allProductions->filter(function ($prod) {
            $expiry = $prod->plain_expiry_carbon;
            return $expiry->gte(now()->startOfDay()) && $expiry->lte(now()->addDays(7)->endOfDay());
        })->count();

        // Ambil 5 terbaru untuk tabel recent QR
        $recentQr = $allProductions->take(5);

        return view('dashboard.index', [
            'totalProduk'       => $totalProduk,
            'totalProduksi'     => $totalProduksi,
            'hampirKedaluwarsa' => $hampirKedaluwarsa,
            'recentQr'          => $recentQr,
            'adminName'         => Auth::user()->name,
        ]);
    }
}