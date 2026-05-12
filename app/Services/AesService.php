<?php

namespace App\Services;

class AesService
{
    private const CIPHER     = 'AES-128-ECB';
    private const BLOCK_SIZE = 16;
    private const KEY_LENGTH = 16;

    private string $key;

    public function __construct(?string $rawBinaryKey = null)
    {
        $this->key = $rawBinaryKey ?? $this->loadKey();
    }

    // ─────────────────────────────────────────────────────────
    // ENKRIPSI — dipakai 2x: kode produksi dulu, lalu tanggal
    // ─────────────────────────────────────────────────────────

    /**
     * Enkripsi satu data (kode produksi ATAU tanggal kedaluwarsa).
     *
     * Preprocessing : validasi input tidak boleh kosong.
     *                 Padding \0 ke kelipatan 16 byte.
     * Proses        : AES-128-ECB, RAW DATA + ZERO PADDING.
     * Output        : hex string (aman untuk URL & DB).
     *
     * Contoh pemakaian:
     *   $encryptedKode = $aes->encrypt('ABC1234');   // 7 char → hex 32 karakter
     *   $encryptedTgl  = $aes->encrypt('20251231');  // 8 char → hex 32 karakter
     */
    public function encrypt(string $plaintext): string
    {
        // Validasi input
        if (strlen($plaintext) === 0) {
            throw new \RuntimeException('Data yang akan dienkripsi tidak boleh kosong.');
        }

        // Preprocessing — zero padding ke kelipatan BLOCK_SIZE (16 byte)
        $padLength = (int)(ceil(strlen($plaintext) / self::BLOCK_SIZE) * self::BLOCK_SIZE);
        $padded    = str_pad($plaintext, $padLength, "\0");

        // Proses enkripsi
        $ciphertext = openssl_encrypt(
            $padded,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Enkripsi gagal.');
        }

        return bin2hex($ciphertext); // output: hex string, aman di URL & DB
    }

    // ─────────────────────────────────────────────────────────
    // DEKRIPSI — dipakai 2x: kode produksi dulu, lalu tanggal
    // ─────────────────────────────────────────────────────────

    /**
     * Dekripsi hex string hasil encrypt().
     *
     * Validasi input  : tidak boleh kosong & harus format hex valid.
     * Proses          : hex2bin dulu → openssl_decrypt.
     * Validasi output : jika false → key salah atau data rusak.
     * Post-processing : hapus padding \0 dari hasil dekripsi.
     *
     * Contoh pemakaian:
     *   $kode    = $aes->decrypt($encryptedKode);  // → 'ABC1234'
     *   $tanggal = $aes->decrypt($encryptedTgl);   // → '20251231'
     */
    public function decrypt(string $ciphertext): string
    {
        // Validasi input
        if (strlen($ciphertext) === 0) {
            throw new \RuntimeException('Ciphertext kosong.');
        }

        // Validasi format hex
        if (!ctype_xdigit($ciphertext)) {
            throw new \RuntimeException('Format ciphertext tidak valid (bukan hex).');
        }

        // Konversi hex → raw binary
        $raw = hex2bin($ciphertext);

        // Proses dekripsi
        $plaintext = openssl_decrypt(
            $raw,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );

        // Validasi hasil
        if ($plaintext === false) {
            throw new \RuntimeException('Dekripsi gagal.');
        }

        // Post-processing — hapus null bytes padding
        return rtrim($plaintext, "\0");
    }

    // ─────────────────────────────────────────────────────────
    // KEY MANAGEMENT
    // ─────────────────────────────────────────────────────────

    /**
     * Constraint key:
     * - Wajib 16 byte.
     * - Format 'hex2bin:xxxx' → konversi hex ke binary.
     * - Lebih dari 16 byte → dipotong.
     * - Kurang dari 16 byte → dipadding dengan \0.
     */
    private function loadKey(): string
    {
        $raw = config('label.aes_key');

        if (empty($raw)) {
            throw new \RuntimeException('Key belum dikonfigurasi.');
        }

        if (str_starts_with($raw, 'hex2bin:')) {
            return substr(hex2bin(substr($raw, 8)), 0, self::KEY_LENGTH);
        }

        return str_pad(substr($raw, 0, self::KEY_LENGTH), self::KEY_LENGTH, "\0");
    }
}