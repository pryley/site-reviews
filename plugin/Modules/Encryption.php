<?php

namespace GeminiLabs\SiteReviews\Modules;

class Encryption
{
    public function decode(string $string): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }

    /**
     * @return string|false
     */
    public function decrypt(string $message)
    {
        try {
            $ciphertext = $this->decode($message);
            return sodium_crypto_secretbox_open($ciphertext, $this->nonce(), $this->key());
        } catch (\Exception $e) {
            glsr_log()->error($e->getMessage());
            return false;
        }
    }

    public function decryptRequest(string $message): array
    {
        if ($message = $this->decrypt($message)) {
            $data = explode('|', $message);
            $data = array_map('sanitize_text_field', $data);
            $action = array_shift($data);
            return compact('action', 'data');
        }
        return [];
    }

    public function encode(string $string): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    /**
     * @return string|false
     */
    public function encrypt(string $message)
    {
        try {
            $ciphertext = sodium_crypto_secretbox($message, $this->nonce(), $this->key());
            return $this->encode($ciphertext);
        } catch (\Exception $e) {
            glsr_log()->error($e->getMessage());
            return false;
        }
    }

    public function encryptRequest(string $action, array $data): string
    {
        $values = array_values(array_map('sanitize_text_field', $data));
        $message = implode('|', $values);
        $message = sprintf('%s|%s', $action, $message);
        return (string) $this->encrypt($message);
    }

    protected function key(): string
    {
        if (!defined('NONCE_KEY')) {
            return '';
        }
        $key = substr(\NONCE_KEY, 0, \SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        return str_pad($key, \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, '#');
    }

    protected function nonce(): string
    {
        if (!defined('NONCE_SALT')) {
            return '';
        }
        $nonce = substr(\NONCE_SALT, 0, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return str_pad($nonce, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '#');
    }
}
