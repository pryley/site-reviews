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
        try {
            $nonceLength = \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
            if (strlen($decoded) >= $nonceLength + 1) { // Minimum for new format
                $nonce = substr($decoded, 0, $nonceLength);
                $ciphertext = substr($decoded, $nonceLength);
                if (strlen($nonce) === $nonceLength) {
                    $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key());
                    if (false !== $plaintext) {
                        return $plaintext; // Success with new format
                    }
                }
            }
            return $this->legacyDecrypt($decoded);
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

    /**
     * @return string|false
     */
    protected function legacyDecrypt(string $ciphertext)
    {
        try {
            $plaintext = sodium_crypto_secretbox_open($ciphertext, $this->legacyNonce(), $this->key());
            if (false === $plaintext) {
                throw new \Exception('Legacy decryption failed');
            }
            return $plaintext;
        } catch (\Exception $e) {
            glsr_log()->error($e->getMessage());
            return false;
        }
    }

    protected function legacyNonce(): string
    {
        $nonce = defined('NONCE_SALT') ? \NONCE_SALT : '';
        $nonce = substr($nonce, 0, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return str_pad($nonce, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '#');
    }

    protected function key(): string
    {
        $key = defined('NONCE_KEY') ? \NONCE_KEY : '';
        $key = substr($key, 0, \SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        return str_pad($key, \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, '#');
    }
}
