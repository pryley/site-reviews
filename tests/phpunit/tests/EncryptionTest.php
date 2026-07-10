<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Modules\Encryption;
use WP_UnitTestCase;

class EncryptionTest extends WP_UnitTestCase
{
    protected Encryption $encryption;

    public function set_up()
    {
        parent::set_up();
        $this->encryption = new Encryption();
    }

    public function test_encrypt_decrypt_round_trip()
    {
        $message = 'approve-review|123|user@example.com';
        $encrypted = $this->encryption->encrypt($message);
        $this->assertIsString($encrypted);
        $this->assertNotSame($message, $encrypted);
        $this->assertSame($message, $this->encryption->decrypt($encrypted));
    }

    public function test_key_is_hkdf_derived()
    {
        $expected = hash_hkdf(
            'sha256',
            NONCE_KEY,
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            'site-reviews-encryption',
            NONCE_SALT
        );
        $this->assertSame($expected, $this->method('key'));
        $this->assertSame(SODIUM_CRYPTO_SECRETBOX_KEYBYTES, strlen($this->method('key')));
        // The HKDF key must differ from the legacy truncate-and-pad key.
        $this->assertNotSame($this->method('legacyKey'), $this->method('key'));
    }

    public function test_data_encrypted_with_legacy_key_still_decrypts()
    {
        // Emulate the pre-HKDF format: random nonce prepended, legacy key.
        $message = 'legacy-payload|42';
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($message, $nonce, $this->method('legacyKey'));
        $encrypted = $this->encryption->encode($nonce.$ciphertext);
        $this->assertSame($message, $this->encryption->decrypt($encrypted));
    }

    public function test_legacy_nonce_format_still_decrypts()
    {
        // Emulate the oldest format: NONCE_SALT-derived nonce, legacy key, no prepended nonce.
        $message = 'oldest-payload|7';
        $ciphertext = sodium_crypto_secretbox($message, $this->method('legacyNonce'), $this->method('legacyKey'));
        $encrypted = $this->encryption->encode($ciphertext);
        $this->assertSame($message, $this->encryption->decrypt($encrypted));
    }

    /**
     * Invoke a protected method on the Encryption instance.
     *
     * @return mixed
     */
    protected function method(string $name)
    {
        $reflection = new \ReflectionMethod(Encryption::class, $name);
        $reflection->setAccessible(true);
        return $reflection->invoke($this->encryption);
    }
}
