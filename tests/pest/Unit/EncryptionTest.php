<?php

use GeminiLabs\SiteReviews\Modules\Encryption;

test('encrypt decrypt round trip', function () {
    $message = 'approve-review|123|user@example.com';
    $encrypted = encryption()->encrypt($message);
    expect($encrypted)->toBeString();
    expect($message)->not->toBe($encrypted);
    expect($message)->toBe(encryption()->decrypt($encrypted));
});

test('key is hkdf derived', function () {
    $expected = hash_hkdf(
        'sha256',
        NONCE_KEY,
        SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
        'site-reviews-encryption',
        NONCE_SALT
    );
    expect($expected)->toBe(encryptionMethod('key'));
    expect(strlen(encryptionMethod('key')))->toBe(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    // The HKDF key must differ from the legacy truncate-and-pad key.
    expect(encryptionMethod('legacyKey'))->not->toBe(encryptionMethod('key'));
});

test('data encrypted with legacy key still decrypts', function () {
    // Emulate the pre-HKDF format: random nonce prepended, legacy key.
    $message = 'legacy-payload|42';
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox($message, $nonce, encryptionMethod('legacyKey'));
    $encrypted = encryption()->encode($nonce.$ciphertext);
    expect($message)->toBe(encryption()->decrypt($encrypted));
});

test('legacy nonce format still decrypts', function () {
    // Emulate the oldest format: NONCE_SALT-derived nonce, legacy key, no prepended nonce.
    $message = 'oldest-payload|7';
    $ciphertext = sodium_crypto_secretbox($message, encryptionMethod('legacyNonce'), encryptionMethod('legacyKey'));
    $encrypted = encryption()->encode($ciphertext);
    expect($message)->toBe(encryption()->decrypt($encrypted));
});

/**
 * Invoke a protected method on the Encryption instance.
 *
 * @return mixed
 */
function encryptionMethod(string $name)
{
    $reflection = new \ReflectionMethod(Encryption::class, $name);
    $reflection->setAccessible(true);
    return $reflection->invoke(encryption());
}

/**
 * Encryption derives its key from the WP salts and holds no state, so a fresh instance is
 * equivalent to any other and a message encrypted by one decrypts in another, as in production.
 */
function encryption(): Encryption
{
    return new Encryption();
}
