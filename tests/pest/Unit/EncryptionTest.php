<?php

use GeminiLabs\SiteReviews\Modules\Encryption;

/*
 * Encryption protects values that leave the server and come back — the form signature and
 * the tokenized approve/verify links.
 *
 * The key is HKDF-derived from wp_salt('nonce') and from nothing else. WordPress does not
 * define a salt constant that is missing or still the wp-config-sample.php placeholder; it
 * generates a unique value and stores it as a site option. wp_salt() is where that judgement
 * lives, so the plugin asks it rather than reading NONCE_KEY and re-deciding.
 */

test('encrypt decrypt round trip', function () {
    $message = 'approve-review|123|user@example.com';
    $encrypted = encryption()->encrypt($message);
    expect($encrypted)->toBeString();
    expect($message)->not->toBe($encrypted);
    expect($message)->toBe(encryption()->decrypt($encrypted));
});

test('the key is hkdf derived from wp_salt', function () {
    $expected = hash_hkdf(
        'sha256',
        wp_salt('nonce'),
        SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
        'site-reviews-encryption'
    );
    expect($expected)->toBe(encryptionMethod('key'));
    expect(strlen(encryptionMethod('key')))->toBe(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    // Never the pre-8.2.3 truncate-and-pad of NONCE_KEY, which on a site with no
    // NONCE_KEY was 32 bytes of # — public knowledge.
    expect(encryptionMethod('key'))->not->toBe(str_repeat('#', SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
});

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

test('a message sealed with the padded legacy key no longer opens', function () {
    // The pre-8.2.3 derivation, which is public knowledge on a site with no NONCE_KEY.
    // Anything sealed with it must now fail to authenticate.
    $paddedKey = str_repeat('#', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox('a:1:{s:7:"form_id";s:3:"abc";}', $nonce, $paddedKey);

    expect(encryption()->decrypt(encryption()->encode($nonce.$ciphertext)))->toBeFalse();
});

test('a message too short to hold a nonce is refused, not decrypted', function () {
    expect(encryption()->decrypt(encryption()->encode('short')))->toBeFalse();
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
 * Encryption derives its key from wp_salt() and holds no state, so a fresh instance is
 * equivalent to any other and a message encrypted by one decrypts in another, as in production.
 */
function encryption(): Encryption
{
    return new Encryption();
}
