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
     * Verifikasi QR Code — pendekatan dosen.
     *
     * Perubahan dari pendekatan lama:
     *
     * LAMA:
     *   1. Decrypt cipher → dapat "VY00001-20250607"
     *   2. Split by "-" → [production_code, expiration_date]
     *   3. Cari di DB by plain production_code
     *   4. Bandingkan expiration_date dari QR vs DB
     *
     * BARU (pendekatan dosen):
     *   1. Ambil cipher dari ?scan= (tidak perlu decrypt dulu)
     *   2. Cari langsung di DB: WHERE production_code = cipher
     *      (bisa karena ECB deterministik → cipher sama = bisa exact match)
     *   3. Jika ketemu → decrypt expiration_date dari DB
     *   4. Tampilkan info produk + status kedaluwarsa
     *   5. Tidak perlu cek integritas manual karena jika cipher tidak ada di DB
     *      → otomatis QR palsu (tidak bisa dibuat tanpa kunci AES)
     */
    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $raw = trim($request->qr_data);

            // Ekstrak cipher dari URL jika format QR adalah URL penuh
            // Contoh: https://domain.com/?scan=A1b2C3d4%3D%3D
            if (filter_var($raw, FILTER_VALIDATE_URL)) {
                $query = parse_url($raw, PHP_URL_QUERY) ?? '';
                parse_str($query, $params);

                if (!empty($params['scan'])) {
                    $raw = $params['scan'];
                }
            }

            // Tidak perlu decrypt — langsung cari cipher di DB
            // ECB deterministik: cipher(VY00001) selalu sama → exact match bisa dilakukan
            $production = Production::with('product')
                ->where('production_code', $raw)
                ->first();

            // Jika tidak ditemukan → QR palsu atau dimanipulasi
            if (!$production) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'QR Code tidak dikenali. Produk mungkin tidak terdaftar atau QR telah dipalsukan.',
                    'integrity' => 'NOT_FOUND',
                ], 404);
            }

            // Dekripsi expiration_date dari DB (isinya cipher dari format Ymd)
            $aes         = new AesService();
            $plainExpiry = $aes->decrypt($production->expiration_date); // → "20250607"

            if (!$plainExpiry || strlen($plainExpiry) !== 8) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Data kedaluwarsa tidak dapat dibaca.',
                    'integrity' => 'DECRYPT_ERROR',
                ], 400);
            }

            $expDate   = Carbon::createFromFormat('Ymd', $plainExpiry);
            $isExpired = Carbon::now()->startOfDay()->greaterThan($expDate);

            return response()->json([
                'success'   => true,
                'integrity' => 'VALID',
                'data'      => [
                    // Tampilkan production_number (plain) bukan production_code (cipher)
                    'production_code' => $production->production_number,
                    'product_name'    => $production->product?->nama_produk ?? 'N/A',
                    'product_size'    => $production->product?->ukuran ?? 'N/A',
                    'product_image'   => $production->product?->foto_produk
                        ? asset('storage/' . $production->product->foto_produk)
                        : asset('images/no-image.png'),
                    'production_date' => Carbon::parse($production->production_date)->format('d M Y'),
                    'expiration_date' => $expDate->format('d M Y'),
                    'is_expired'      => $isExpired,
                    'status'          => $isExpired ? 'KEDALUWARSA' : 'AMAN',
                    'status_color'    => $isExpired ? 'danger' : 'success',
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