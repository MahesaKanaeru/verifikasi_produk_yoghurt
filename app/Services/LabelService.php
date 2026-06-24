<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LabelService
{
    private function storagePath(string $relativePath = ''): string
    {
        // ================= STORAGE HOSTING =================
        // $base = 'D:\Project\vtaya_veryfy shp\verifikasi_produk_yoghurt/storage/app/public';
    
        // ================= STORAGE HOSTING =================
        $base = '/home/cery9751/public_html/vtaya-yoghurt-verify.my.id/storage';


        return $relativePath
            ? $base . '/' . ltrim($relativePath, '/')
            : $base;
    }

    private function getConfig(string $ukuran): array
    {
        $ukuran = strtolower($ukuran);

        if (str_contains($ukuran, '200')) {
            return config('label.cup_200');
        }

        return config('label.bottle_250');
    }

    /**
     * Cari file font yang tersedia.
     * Prioritas: Montserrat-Bold → Montserrat-SemiBold → Montserrat (regular) → null (pakai default GD)
     */
    private function resolveFontPath(): ?string
    {
        $candidates = [
            $this->storagePath('fonts/Montserrat-Bold.ttf'),
            $this->storagePath('fonts/Montserrat-SemiBold.ttf'),
            $this->storagePath('fonts/MontserratBold.ttf'),
            $this->storagePath('fonts/Montserrat.ttf'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                return $path;
            }
        }

        // Font tidak ditemukan → GD akan pakai font bawaan
        return null;
    }

    public function mergeQrToLabel(
        $productLabelPath,
        $qrCodePath,
        $productionCode,
        $productionDate,
        $expirationDate,
        string $ukuran = '250 ml (Bottle)'
    ) {
        if (empty($productLabelPath)) {
            throw new \InvalidArgumentException(
                'Template label produk belum diisi. Tambahkan foto label pada data produk terlebih dahulu.'
            );
        }

        $manager = new ImageManager(new Driver());

        try {
            $label = $manager->read($this->storagePath($productLabelPath));
            $qr    = $manager->read($this->storagePath($qrCodePath));
        } catch (\Exception $e) {
            throw new \RuntimeException('Gagal membaca file label atau QR: ' . $e->getMessage());
        }

        $cfg      = $this->getConfig($ukuran);
        $dpi      = 300;
        $cmToInch = 1 / 2.54;

        $targetW    = (int) ($cfg['label_width_cm']  * $cmToInch * $dpi);
        $targetH    = (int) ($cfg['label_height_cm'] * $cmToInch * $dpi);
        $qrSize     = (int) ($cfg['qr_size_cm']      * $cmToInch * $dpi);
        $posX       = (int) ($cfg['qr_pos_x_cm']     * $cmToInch * $dpi);
        $posY       = (int) ($cfg['qr_pos_y_cm']     * $cmToInch * $dpi);
        $centerX    = (int) ($cfg['center_x_cm']     * $cmToInch * $dpi);
        $prodY      = (int) ($cfg['prod_pos_y_cm']   * $cmToInch * $dpi);
        $expY       = (int) ($prodY + ($cfg['exp_gap_cm'] * $cmToInch * $dpi));

        // ✅ REVISI: Naikkan ukuran font (tambah ~20-30% dari nilai config)
        // Kalau config font_size = 30, sekarang jadi ~38
        $fontSizePx = (int) ($cfg['font_size'] * 1.3);

        $label->resize($targetW, $targetH);
        $qr->resize($qrSize, $qrSize);
        $label->place($qr, 'top-left', $posX, $posY);

        $prodText = 'PROD : ' . date('Ymd', strtotime($productionDate));
        $expText  = 'EXP  : ' . date('Ymd', strtotime($expirationDate));

        // ✅ Cari font terbaik yang tersedia
        $fontPath = $this->resolveFontPath();

        // ✅ TRICK TEBAL: Gambar teks beberapa kali dengan offset 1px
        // Ini mensimulasikan efek bold kalau font Regular yang terpakai
        $this->drawBoldText($label, $prodText, $centerX, $prodY, $fontSizePx, $fontPath);
        $this->drawBoldText($label, $expText,  $centerX, $expY,  $fontSizePx, $fontPath);

        $unique   = time() . '_' . uniqid();
        $filename = $productionCode . '_' . $unique . '_label.png';
        $folder   = $this->storagePath('final_labels');

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $label->save($folder . DIRECTORY_SEPARATOR . $filename, 100);

        return 'final_labels/' . $filename;
    }

    /**
     * Gambar teks dengan efek bold menggunakan teknik stroke/multi-draw.
     * Teks digambar 5x dengan offset kecil → tampak tebal meski pakai font Regular.
     *
     * @param \Intervention\Image\Image $image
     * @param string      $text
     * @param int         $x
     * @param int         $y
     * @param int         $size
     * @param string|null $fontPath
     */
    private function drawBoldText($image, string $text, int $x, int $y, int $size, ?string $fontPath): void
    {
        // Offset untuk efek bold (dalam pixel, sesuaikan jika perlu)
        $offsets = [
            [-1, 0], [1, 0], [0, -1], [0, 1],  // 4 arah
            [0, 0],                               // tengah (lapisan utama)
        ];

        foreach ($offsets as [$dx, $dy]) {
            $image->text($text, $x + $dx, $y + $dy, function ($font) use ($size, $fontPath) {
                if ($fontPath) {
                    $font->file($fontPath);
                }
                $font->size($size);
                $font->color('#000000');
                $font->align('center');
                $font->valign('top');
            });
        }
    }
}