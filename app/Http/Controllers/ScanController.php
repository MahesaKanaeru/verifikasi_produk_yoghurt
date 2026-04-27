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

    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $raw = trim($request->qr_data);
            if (filter_var($raw, FILTER_VALIDATE_URL)) {
                $query = parse_url($raw, PHP_URL_QUERY) ?? '';
                parse_str($query, $params);

                if (!empty($params['scan'])) {
                    // URL: https://domain/?scan=ENCRYPTED
                    $raw = $params['scan'];
                }
            }

            $aes       = new AesService();
            $decrypted = $aes->decrypt($raw);
            $parts     = explode('-', $decrypted);

            if (count($parts) !== 2) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Format QR Code tidak valid.',
                    'integrity' => 'CORRUPTED',
                ], 400);
            }

            [$decryptedProductionCode, $decryptedExpirationDate] = $parts;
            $production = Production::with('product')
                ->where('production_code', $decryptedProductionCode)
                ->firstOrFail();

            $databaseExpirationDate = Carbon::parse($production->expiration_date)->format('Ymd');
            $isIntegrityValid =
                ($decryptedProductionCode === $production->production_code) &&
                ($decryptedExpirationDate === $databaseExpirationDate);

            if (!$isIntegrityValid) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'PERINGATAN: QR Code telah dimanipulasi atau rusak!',
                    'integrity' => 'MANIPULATED',
                ], 403);
            }
            $expDate   = Carbon::createFromFormat('Ymd', $decryptedExpirationDate);
            $isExpired = Carbon::now()->startOfDay()->greaterThan($expDate);

            return response()->json([
                'success'   => true,
                'integrity' => 'VALID',
                'data'      => [
                    'production_code' => $production->production_code,
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

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success'   => false,
                'message'   => 'Produksi tidak ditemukan. QR Code mungkin palsu.',
                'integrity' => 'NOT_FOUND',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => 'Gagal membaca QR Code: ' . $e->getMessage(),
                'integrity' => 'DECRYPT_ERROR',
            ], 400);
        }
    }
}