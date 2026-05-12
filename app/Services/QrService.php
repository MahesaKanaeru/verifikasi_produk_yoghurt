<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Support\Facades\Storage;

class QrService
{
    /**
     * Generate QR Code.
     *
     * $plainCode     : untuk nama file QR saja (tidak masuk URL).
     * $encryptedCode : hex string hasil AesService::encrypt() → masuk ke URL.
     *
     * URL format: https://domain.com/?scan=1a2b3c4d... (hex, aman tanpa encoding tambahan)
     * Hex hanya mengandung 0-9 dan a-f sehingga aman langsung di URL.
     */
    public function generate(string $plainCode, string $encryptedCode): string
    {
        // Hex string aman di URL — urlencode() tidak mengubah apapun pada hex
        $deepLink = rtrim(config('app.url'), '/') . '/?scan=' . urlencode($encryptedCode);

        $options = new QROptions([
            'outputInterface'  => QRGdImagePNG::class,
            'outputBase64'     => false,
            'eccLevel'         => 'M',
            'scale'            => 5,
            'imageTransparent' => false,
            'cachefile'        => null,
        ]);

        $qrImage  = (new QRCode($options))->render($deepLink);
        $fileName = 'qr_codes/' . $plainCode . '.png';

        $qrPath = public_path('storage/qr_codes');

        if (!file_exists($qrPath)) {
            mkdir($qrPath, 0777, true);
        }

        file_put_contents(
            public_path('storage/' . $fileName),
            $qrImage
        );

        return $fileName;
    }
}