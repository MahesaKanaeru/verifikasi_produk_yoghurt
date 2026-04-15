<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScanController;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
*/

// Halaman Welcome & Login
Route::middleware('guest')->group(function () {
    // PERBAIKAN: Cukup pakai yang ScanController saja
    Route::get('/', [ScanController::class, 'index'])->name('welcome');

    Route::get('/login', function () { 
        return view('auth.login'); 
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Halaman Verifikasi (Hasil Scan QR) - Harus Publik
Route::get('/verifikasi', function () {
    return view('produk.verify'); // Nanti kita buat file ini untuk proses AES
})->name('verifikasi');


/*
|--------------------------------------------------------------------------
| Admin Routes (Wajib Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', function () { 
        return view('dashboard.index'); 
    })->name('dashboard');

    // CRUD Produk
    // Menggunakan Resource agar otomatis mencakup Index, Store, Update, Delete
    Route::resource('produk', ProductController::class);

    // Placeholder untuk Data Produksi nanti
    Route::get('/produksi', function () {
        return view('produksi.index');
    })->name('produksi.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});