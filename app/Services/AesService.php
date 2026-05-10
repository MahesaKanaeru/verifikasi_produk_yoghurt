<?php

namespace App\Services;

class AesService
{
    protected $key;
    protected $cipher;

    public function __construct()
    {
        $this->key = config('label.aes_key');
        // $env = env('AES_KEY', '1234567890123456');
        // $this->key = config($env);
        $this->cipher = 'aes-128-ecb'; 
    }
    public function encrypt($plainText)
    {
        $encrypted = openssl_encrypt($plainText, $this->cipher, $this->key, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }

    public function decrypt($encryptedBase64Text)
    {
        $decoded = base64_decode($encryptedBase64Text);
        $decrypted = openssl_decrypt($decoded, $this->cipher, $this->key, OPENSSL_RAW_DATA);
        return $decrypted;
    }
}