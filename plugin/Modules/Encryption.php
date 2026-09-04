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
        $decoded = $this->decode($message);
        if (empty($decoded)) {
            return '';
        }
        $nonceLength = (int) \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
        if (strlen($decoded) < $nonceLength + 1) {
            return false;
        }
        try {
            $nonce = substr($decoded, 0, $nonceLength);
            $ciphertext = substr($decoded, $nonceLength);
            $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key());
            if (false === $plaintext && $previousKey = $this->previousKey()) {
                $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $previousKey); // @compat v8.2
            }
            return $plaintext;
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
            $nonce = random_bytes(\SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $ciphertext = sodium_crypto_secretbox($message, $nonce, $this->key());
            // Prepend nonce to ciphertext
            return $this->encode($nonce.$ciphertext);
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
        return hash_hkdf('sha256', wp_salt('nonce'), (int) \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'site-reviews-encryption');
    }

    /**
     * @todo Remove in v8.5
     */
    protected function previousKey(): ?string
    {
        $key = defined('NONCE_KEY') ? (string) \NONCE_KEY : '';
        $salt = defined('NONCE_SALT') ? (string) \NONCE_SALT : '';
        if ('' === $key || wp_salt('nonce') !== $key.$salt) {
            return null;
        }
        return hash_hkdf('sha256', $key, (int) \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'site-reviews-encryption', $salt);
    }
}
