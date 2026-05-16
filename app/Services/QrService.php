<?php

namespace App\Services;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;

class QrService
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
        // $base = 'D:/Skripsi/Projek/vtayaapp/storage/app/public';

        // ================= STORAGE HOSTING =================
        // Untuk production di Rumahweb hosting
        $base = '/home/cery9751/public_html/vtaya-yoghurt-verify.my.id/storage';

        return $relativePath
            ? $base . '/' . ltrim($relativePath, '/')
            : $base;
    }

    public function generate(string $plainCode, string $encryptedCode): string
    {
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

        $safeName = preg_replace('/[^\w\-]/u', '_', $plainCode);
        $unique = time() . '_' . uniqid();
        $filename = $safeName . '_' . $unique . '.png';

        $folder = $this->storagePath('qr_codes');
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        file_put_contents($folder . DIRECTORY_SEPARATOR . $filename, $qrImage);

        return 'qr_codes/' . $filename;
    }
}
