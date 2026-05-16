<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Production;
use App\Services\AesService;
use Carbon\Carbon;

class ScanController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('welcome', compact('products'));
    }

    /**
     * Verifikasi QR Code.
     *
     * Skenario:
     *   1. QR asli & produk AMAN     → success: true, status: AMAN
     *   2. QR asli & produk KEDALUWARSA → success: true, status: KEDALUWARSA
     *   3. QR palsu / random / tidak dikenali → success: false, integrity: NOT_FOUND
     */
    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);
        try {
            $raw = trim($request->qr_data);
            if (filter_var($raw, FILTER_VALIDATE_URL)) {
                parse_str(parse_url($raw, PHP_URL_QUERY) ?? '', $params);
                if (!empty($params['scan'])) {
                    $raw = $params['scan'];
                }
            }

            // Cari production_code (hex) langsung di DB
            // QR random / palsu / bukan buatan aplikasi ini → otomatis tidak ketemu
            $production = Production::with('product')
                ->where('production_code', $raw)
                ->first();

            // Tidak ditemukan → QR bukan dari aplikasi ini
            if (!$production) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Data produk tidak ditemukan. QR Code ini tidak terdaftar dalam sistem kami — produk kemungkinan tidak asli atau informasi telah dimanipulasi. Harap waspada dan jangan konsumsi produk ini sebelum memastikan keasliannya.',
                    'integrity' => 'NOT_FOUND',
                ], 404);
            }

            // Dekripsi expiration_date (hex) dari DB → plaintext format "Ymd"
            $aes         = new AesService();
            $plainExpiry = $aes->decrypt($production->expiration_date);

            if (!$plainExpiry || strlen($plainExpiry) !== 8) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Data kedaluwarsa tidak dapat dibaca.',
                    'integrity' => 'DECRYPT_ERROR',
                ], 400);
            }

            $expDate   = Carbon::createFromFormat('Ymd', $plainExpiry);
            $isExpired = Carbon::now()->startOfDay()->greaterThan($expDate);
            $aes         = new AesService();
            $productionCode = $aes->decrypt($production->production_code);
            return response()->json([
                'success'   => true,
                'integrity' => 'VALID',
                'data'      => [
                    'production_code'           => $productionCode,
                    'production_code_encrypted' => $production->production_code,   // ← cipher dari DB
                    'expiry_encrypted'          => $production->expiration_date,   // ← cipher dari DB
                    'product_name'              => $production->product?->nama_produk ?? 'N/A',
                    'product_size'              => $production->product?->ukuran ?? 'N/A',
                    'product_image'             => $production->product?->foto_produk
                        ? asset('storage/' . $production->product->foto_produk)
                        : asset('images/no-image.png'),
                    'production_date'           => Carbon::parse($production->production_date)->format('d M Y'),
                    'expiration_date'           => $expDate->format('d M Y'),
                    'is_expired'                => $isExpired,
                    'status'                    => $isExpired ? 'KEDALUWARSA' : 'AMAN',
                    'status_color'              => $isExpired ? 'danger' : 'success',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => 'Gagal memproses QR Code: ' . $e->getMessage(),
                'integrity' => 'ERROR',
            ], 400);
        }
    }
}