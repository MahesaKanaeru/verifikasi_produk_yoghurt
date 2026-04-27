<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\Product;
use App\Services\AesService;
use App\Services\QrService;
use App\Services\LabelService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with('product')
            ->whereHas('product')
            ->latest()
            ->get();

        $products = Product::all();
        return view('produksi.index', compact('productions', 'products'));
    }
    public function store(Request $request, AesService $aes, QrService $qrService, LabelService $labelService)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'production_date' => 'required|date',
        ]);

        $product        = Product::findOrFail($request->product_id);
        $productionDate = Carbon::parse($request->production_date);
        $expirationDate = $productionDate->copy()->addDays($product->estimasi_expired);
        $productionCode = Production::generateProductionCode();

        $plainText     = $productionCode . '-' . $expirationDate->format('Ymd');
        $encryptedText = $aes->encrypt($plainText);

        $qrPath = $qrService->generate($productionCode, $encryptedText);

        $finalLabelPath = $labelService->mergeQrToLabel(
            $product->foto_label,
            $qrPath,
            $productionCode,
            $productionDate,
            $expirationDate
        );
        Production::create([
            'production_code'  => $productionCode,
            'product_id'       => $product->id,
            'production_date'  => $productionDate->format('Y-m-d'),
            'expiration_date'  => $expirationDate->format('Y-m-d'),
            'encrypted_text'   => $encryptedText,
            'qr_code_path'     => $qrPath,
            'final_label_path' => $finalLabelPath,
        ]);

        return redirect()->back()->with('success', 'Produksi & QR Code berhasil di-generate!');
    }
    public function downloadLabel(Production $production)
    {
        if (!$production->final_label_path || !Storage::disk('public')->exists($production->final_label_path)) {
            return redirect()->back()->with('error', 'Label final belum tersedia.');
        }

        return response()->download(
            Storage::disk('public')->path($production->final_label_path)
        );
    }
    public function downloadQr(Production $production)
    {
        if (!$production->qr_code_path || !Storage::disk('public')->exists($production->qr_code_path)) {
            return redirect()->back()->with('error', 'QR Code belum tersedia.');
        }

        return response()->download(
            Storage::disk('public')->path($production->qr_code_path)
        );
    }

    public function destroy(Production $production)
    {
        if ($production->qr_code_path && Storage::disk('public')->exists($production->qr_code_path)) {
            Storage::disk('public')->delete($production->qr_code_path);
        }
        if ($production->final_label_path && Storage::disk('public')->exists($production->final_label_path)) {
            Storage::disk('public')->delete($production->final_label_path);
        }

        $production->delete();
        return redirect()->back()->with('success', 'Data produksi berhasil dihapus.');
    }
}