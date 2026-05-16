<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [ScanController::class, 'index'])->name('welcome');
Route::post('/api/verify-qr', [ScanController::class, 'verifyQr'])->name('verify-qr');


/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', ProductController::class);

    Route::resource('production', ProductionController::class);
    Route::post('/production/bulk-store', [ProductionController::class, 'bulkStore'])->name('production.bulk-store');
    Route::get('production/{production}/download-qr',    [ProductionController::class, 'downloadQr'])->name('production.download-qr');
    Route::get('production/{production}/download-label', [ProductionController::class, 'downloadLabel'])->name('production.download-label');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf',  [LaporanController::class, 'pdf'])->name('laporan.pdf');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});