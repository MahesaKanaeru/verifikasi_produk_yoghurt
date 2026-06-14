<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Production;
use App\Services\AesService;
use App\Services\QrService;
use App\Services\LabelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductionController extends Controller
{
    // old
    // private function storagePath(string $relativePath = ''): string
    // {
    //     $base = '/home/cery9751/public_html/vtaya-yoghurt-verify.my.id/storage';
    //     return $relativePath ? $base . '/' . ltrim($relativePath, '/') : $base;
    // }

    private function storagePath(string $relativePath = ''): string
    {
        // ================= STORAGE LOKAL =================
        // Untuk development di laptop / WAMP
        //  $base = 'D:\Project\vtaya_veryfy shp\verifikasi_produk_yoghurt/storage/app/public';

        // ================= STORAGE HOSTING =================
        // Untuk production di Rumahweb hosting
        $base = '/home/cery9751/public_html/vtaya-yoghurt-verify.my.id/storage';

        return $relativePath
            ? $base . '/' . ltrim($relativePath, '/')
            : $base;
    }
    // Odl
    // public function index(AesService $aes)
    // {
    //     $productions = Production::with('product')
    //         ->whereHas('product')
    //         ->latest()
    //         ->get()
    //         ->map(function ($prod) use ($aes) {
    //             $plain = $aes->decrypt($prod->expiration_date);
    //             $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');
    //             return $prod;
    //         });

    //     $products = Product::all();
    //     return view('produksi.index', compact('productions', 'products'));
    // }
    public function index(AesService $aes)
    {
        $productions = Production::with('product')
            ->whereHas('product')
            ->latest()
            ->get()
            ->map(function ($prod) use ($aes) {
                $plain = $aes->decrypt($prod->expiration_date);
                $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');
                $prod->production_code = $aes->decrypt($prod->production_code);
                return $prod;
            });

        $products = Product::all();

        $productsJson = $products->map(function ($p) {
            return [
                'id'      => $p->id,
                'expired' => $p->estimasi_expired,
                'nama'    => $p->nama_produk,
                'kode'    => $p->kode_produk,
            ];
        })->values();

        return view('produksi.index', compact('productions', 'products', 'productsJson'));
    }

    public function store(Request $request, AesService $aes, QrService $qrService, LabelService $labelService)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'production_date' => 'required|date',
            'qty'             => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // ← Cek sebelum proses — pesan lebih spesifik
        if (empty($product->foto_label)) {
            return redirect()->back()
                ->with('error', "Produk \"{$product->nama_produk}\" belum memiliki template label. Tambahkan foto label pada data produk terlebih dahulu.")
                ->withInput();
        }

        try {
            $productionDate  = Carbon::parse($request->production_date);
            $expirationDate  = $productionDate->copy()->addDays($product->estimasi_expired);
            $plainCode       = Production::generateProductionCode();
            $encryptedCode   = $aes->encrypt($plainCode);
            $encryptedExpiry = $aes->encrypt($expirationDate->format('Ymd'));
            $qrPath          = $qrService->generate($plainCode, $encryptedCode);
            $finalLabelPath  = $labelService->mergeQrToLabel(
                $product->foto_label, $qrPath, $plainCode,
                $productionDate, $expirationDate, $product->ukuran
            );

            Production::create([
                'production_code'  => $encryptedCode,
                'product_id'       => $product->id,
                'qty'              => $request->qty,
                'production_date'  => $productionDate->format('Y-m-d'),
                'expiration_date'  => $encryptedExpiry,
                'qr_code_path'     => $qrPath,
                'final_label_path' => $finalLabelPath,
            ]);

            return redirect()->back()->with('success', 'Produksi & QR Code berhasil di-generate!');

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function bulkStore(Request $request, AesService $aes, QrService $qrService, LabelService $labelService)
    {
        $request->validate([
            'production_date'    => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        // ← Validasi semua produk punya label SEBELUM mulai generate
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if (empty($product->foto_label)) {
                return redirect()->back()
                    ->with('error', "Produk \"{$product->nama_produk}\" belum memiliki template label. Tambahkan foto label pada data produk terlebih dahulu.")
                    ->withInput();
            }
        }

        try {
            $productionDate = Carbon::parse($request->production_date);
            $successCount   = 0;

            foreach ($request->items as $item) {
                $product        = Product::findOrFail($item['product_id']);
                $expirationDate = $productionDate->copy()->addDays($product->estimasi_expired);
                $plainCode      = Production::generateProductionCode();
                $encryptedCode  = $aes->encrypt($plainCode);
                $encryptedExpiry = $aes->encrypt($expirationDate->format('Ymd'));
                $qrPath          = $qrService->generate($plainCode, $encryptedCode);
                $finalLabelPath  = $labelService->mergeQrToLabel(
                    $product->foto_label, $qrPath, $plainCode,
                    $productionDate, $expirationDate, $product->ukuran
                );

                Production::create([
                    'production_code'  => $encryptedCode,
                    'product_id'       => $product->id,
                    'qty'              => $item['qty'],
                    'production_date'  => $productionDate->format('Y-m-d'),
                    'expiration_date'  => $encryptedExpiry,
                    'qr_code_path'     => $qrPath,
                    'final_label_path' => $finalLabelPath,
                ]);

                $successCount++;
            }

            return redirect()->back()->with('success', "{$successCount} produksi berhasil di-generate!");

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate: ' . $e->getMessage())->withInput();
        }
    }

    public function downloadLabel(Production $production)
    {
        if (!$production->final_label_path) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        $absolutePath = $this->storagePath($production->final_label_path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        return response()->download($absolutePath);
    }

    public function downloadQr(Production $production)
    {
        if (!$production->qr_code_path) {
            return redirect()->back()->with('error', 'QR Code belum tersedia.');
        }

        $absolutePath = $this->storagePath($production->qr_code_path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'QR Code belum tersedia.');
        }

        return response()->download($absolutePath);
    }

    public function destroy(Production $production)
    {
        if ($production->qr_code_path) {
            $qrAbsolute = $this->storagePath($production->qr_code_path);
            if (file_exists($qrAbsolute)) {
                unlink($qrAbsolute);
            }
        }

        if ($production->final_label_path) {
            $labelAbsolute = $this->storagePath($production->final_label_path);
            if (file_exists($labelAbsolute)) {
                unlink($labelAbsolute);
            }
        }

        $production->delete();
        return redirect()->back()->with('success', 'Data produksi berhasil dihapus.');
    }
    public function printBatch(Production $production)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 120);

        if (!$production->final_label_path) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        $absolutePath = $this->storagePath($production->final_label_path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        $imageData = base64_encode(file_get_contents($absolutePath));
        $ext       = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $imageSrc  = "data:image/{$ext};base64,{$imageData}";

        $qty = $production->qty;

        $pdf = Pdf::loadView('produksi.print-batch', [
            'imageSrc' => $imageSrc,
            'qty'      => $qty,
        ])->setPaper([0, 0, 609.4488, 935.433], 'portrait');

        $namaProduk      = $production->product->nama_produk ?? 'Produk';
        $tanggalProduksi = \Carbon\Carbon::parse($production->production_date)->format('d-m-Y');
        $safeNama        = preg_replace('/[^\w\-]/u', '_', $namaProduk);
        $filename        = "{$safeNama}_{$tanggalProduksi}_{$qty}pcs.pdf";

        $token = 'pdf_ready';

        return $pdf->download($filename)->cookie(
            $token,     
            '1',        
            5,         
            '/',        
            null,       
            false,      
            false       
        );
    }
}
