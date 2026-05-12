<?php

namespace App\Http\Controllers;



use App\Models\Product;
use App\Models\Production;
use App\Services\AesService;
use App\Services\QrService;
use App\Services\LabelService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(AesService $aes)
    {
        $productions = Production::with('product')
            ->whereHas('product')
            ->latest()
            ->get()
            ->map(function ($prod) use ($aes) {
                $plain = $aes->decrypt($prod->expiration_date);
                $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');
                return $prod;
            });

        $products = Product::all();
        return view('produksi.index', compact('productions', 'products'));
    }

    public function store(Request $request, AesService $aes, QrService $qrService, LabelService $labelService)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'production_date' => 'required|date',
            'qty'             => 'required|integer|min:1',
        ]);

        $product        = Product::findOrFail($request->product_id);
        $productionDate = Carbon::parse($request->production_date);
        $expirationDate = $productionDate->copy()->addDays($product->estimasi_expired);

        $plainCode       = Production::generateProductionCode();
        $encryptedCode   = $aes->encrypt($plainCode);
        $encryptedExpiry = $aes->encrypt($expirationDate->format('Ymd'));

        $qrPath = $qrService->generate($plainCode, $encryptedCode);

        $finalLabelPath = $labelService->mergeQrToLabel(
            $product->foto_label,
            $qrPath,
            $plainCode,
            $productionDate,
            $expirationDate,
            $product->ukuran
        );

        Production::create([
            'production_number' => $plainCode,
            'production_code'   => $encryptedCode,
            'product_id'        => $product->id,
            'qty'               => $request->qty,
            'production_date'   => $productionDate->format('Y-m-d'),
            'expiration_date'   => $encryptedExpiry,
            'qr_code_path'      => $qrPath,
            'final_label_path'  => $finalLabelPath,
        ]);

        return redirect()->back()->with('success', 'Produksi & QR Code berhasil di-generate!');
    }

    // ─────────────────────────────────────────────
    //  DOWNLOAD — tidak pakai Storage::disk('public')
    // ─────────────────────────────────────────────

    public function downloadLabel(Production $production)
    {
        if (!$production->final_label_path) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        $absolutePath = public_path('storage/' . $production->final_label_path);

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

        $absolutePath = public_path('storage/' . $production->qr_code_path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'QR Code belum tersedia.');
        }

        return response()->download($absolutePath);
    }

    // ─────────────────────────────────────────────
    //  DELETE — tidak pakai Storage::disk('public')
    // ─────────────────────────────────────────────

    public function destroy(Production $production)
    {
        if ($production->qr_code_path) {
            $qrAbsolute = public_path('storage/' . $production->qr_code_path);
            if (file_exists($qrAbsolute)) {
                unlink($qrAbsolute);
            }
        }

        if ($production->final_label_path) {
            $labelAbsolute = public_path('storage/' . $production->final_label_path);
            if (file_exists($labelAbsolute)) {
                unlink($labelAbsolute);
            }
        }

        $production->delete();
        return redirect()->back()->with('success', 'Data produksi berhasil dihapus.');
    }
}

// old
// use Illuminate\Http\Request;
// use App\Models\Production;
// use App\Models\Product;
// use App\Services\AesService;
// use App\Services\QrService;
// use App\Services\LabelService;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\Storage;

// class ProductionController extends Controller
// {
//     public function index(AesService $aes)
//     {
//         $productions = Production::with('product')
//             ->whereHas('product')
//             ->latest()
//             ->get()
//             ->map(function ($prod) use ($aes) {
//                 // Decrypt expiration_date (isinya cipher) → format untuk tampilan
//                 $plain = $aes->decrypt($prod->expiration_date);
//                 $prod->plain_expiry = Carbon::createFromFormat('Ymd', $plain)->format('d M Y');
//                 return $prod;
//             });

//         $products = Product::all();
//         return view('produksi.index', compact('productions', 'products'));
//     }

//     public function store(Request $request, AesService $aes, QrService $qrService, LabelService $labelService)
//     {
//         $request->validate([
//             'product_id'      => 'required|exists:products,id',
//             'production_date' => 'required|date',
//             'qty'             => 'required|integer|min:1',
//         ]);

//         $product        = Product::findOrFail($request->product_id);
//         $productionDate = Carbon::parse($request->production_date);
//         $expirationDate = $productionDate->copy()->addDays($product->estimasi_expired);

//         $plainCode       = Production::generateProductionCode();
//         $encryptedCode   = $aes->encrypt($plainCode);
//         $encryptedExpiry = $aes->encrypt($expirationDate->format('Ymd'));

//         $qrPath = $qrService->generate($plainCode, $encryptedCode);

//         $finalLabelPath = $labelService->mergeQrToLabel(
//             $product->foto_label,
//             $qrPath,
//             $plainCode,
//             $productionDate,
//             $expirationDate,
//             $product->ukuran   // ← tambah ini
//         );

//         Production::create([
//             'production_number' => $plainCode,
//             'production_code'   => $encryptedCode,
//             'product_id'        => $product->id,
//             'qty'               => $request->qty,
//             'production_date'   => $productionDate->format('Y-m-d'),
//             'expiration_date'   => $encryptedExpiry,
//             'qr_code_path'      => $qrPath,
//             'final_label_path'  => $finalLabelPath,
//         ]);

//         return redirect()->back()->with('success', 'Produksi & QR Code berhasil di-generate!');
//     }

//     public function downloadLabel(Production $production)
//     {
//         if (!$production->final_label_path || !Storage::disk('public')->exists($production->final_label_path)) {
//             return redirect()->back()->with('error', 'Label final belum tersedia.');
//         }

//         return response()->download(
//             Storage::disk('public')->path($production->final_label_path)
//         );
//     }

//     public function downloadQr(Production $production)
//     {
//         if (!$production->qr_code_path || !Storage::disk('public')->exists($production->qr_code_path)) {
//             return redirect()->back()->with('error', 'QR Code belum tersedia.');
//         }

//         return response()->download(
//             Storage::disk('public')->path($production->qr_code_path)
//         );
//     }

//     public function destroy(Production $production)
//     {
//         if ($production->qr_code_path && Storage::disk('public')->exists($production->qr_code_path)) {
//             Storage::disk('public')->delete($production->qr_code_path);
//         }
//         if ($production->final_label_path && Storage::disk('public')->exists($production->final_label_path)) {
//             Storage::disk('public')->delete($production->final_label_path);
//         }

//         $production->delete();
//         return redirect()->back()->with('success', 'Data produksi berhasil dihapus.');
//     }
// }