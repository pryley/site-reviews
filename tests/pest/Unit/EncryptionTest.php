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

test('a crypto failure while encrypting is logged and answered with false', function () {
    // random_bytes() throws only when the CSPRNG itself fails — armed here
    // through its namespace shadow (Support/failable-functions.php)
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('random_bytes');
    try {
        expect(encryption()->encrypt('a secret'))->toBeFalse();
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
    expect(glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain('random_bytes failed');
});

test('a crypto failure while decrypting is logged and answered with false', function () {
    // the message is genuine — sodium itself is what fails
    $encrypted = encryption()->encrypt('a secret');
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('sodium_crypto_secretbox_open');
    try {
        expect(encryption()->decrypt($encrypted))->toBeFalse();
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
    expect(glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain('secretbox_open failed');
});

test('without keying material the legacy key is the key', function () {
    // A wp-config.php with no salts defined: the armed defined() shadow reports every
    // constant missing, and key() falls back to the legacy derivation — which pads an
    // empty key out with # so encryption still round-trips rather than fataling.
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('defined');
    try {
        expect(encryptionMethod('key'))->toBe(str_repeat('#', SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
});
