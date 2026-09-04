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
 *
 * The 8.2.x key (HKDF of NONCE_KEY salted with NONCE_SALT) is accepted for decryption only,
 * and only while wp_salt('nonce') returns those two constants unchanged, so approve and
 * verify links and full-page-cached form signatures issued before 8.3.0 keep working on a
 * site with real security keys. On a site whose constants WordPress does not trust, the old
 * key was public knowledge and nothing sealed under it opens.
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
    // Never the pre-8.1.0 truncate-and-pad of NONCE_KEY (kept as a fallback until 8.3.0),
    // which on a site with no NONCE_KEY was 32 bytes of # — public knowledge.
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
    // The pre-8.1.0 derivation, kept as a fallback until 8.3.0. It is public knowledge on a
    // site with no NONCE_KEY, so anything sealed with it must now fail to authenticate.
    $paddedKey = str_repeat('#', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox('a:1:{s:7:"form_id";s:3:"abc";}', $nonce, $paddedKey);

    expect(encryption()->decrypt(encryption()->encode($nonce.$ciphertext)))->toBeFalse();
});

test('a message sealed under the 8.2.x key still opens when WordPress trusts the constants', function () {
    // wp-env defines NONCE_KEY and NONCE_SALT and wp_salt('nonce') returns them unchanged
    // (probed in the container before this test was written), so this is the positive path
    // every upgrading site with real security keys takes.
    expect(wp_salt('nonce'))->toBe(NONCE_KEY.NONCE_SALT);
    $previousKey = hash_hkdf('sha256', NONCE_KEY, SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'site-reviews-encryption', NONCE_SALT);
    expect(encryptionMethod('previousKey'))->toBe($previousKey);
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox('approve|123', $nonce, $previousKey);
    expect(encryption()->decrypt(encryption()->encode($nonce.$ciphertext)))->toBe('approve|123');
    // encrypt() never uses it: a fresh message opens under the current key alone
    $fresh = encryption()->encrypt('approve|456');
    $decoded = encryption()->decode($fresh);
    $opened = sodium_crypto_secretbox_open(
        substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
        substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
        encryptionMethod('key')
    );
    expect($opened)->toBe('approve|456');
});

test('the 8.2.x key is refused when wp_salt no longer returns the constants', function () {
    // wp_salt() applies the "salt" filter on every call, cached or not (pluggable.php, the
    // WordPress 7.1 that runs this suite), so a filter stands in for a site whose constants
    // WordPress does not trust: missing, the wp-config-sample.php placeholder, duplicated,
    // or replaced by a filter or a pluggable override.
    $previousKey = hash_hkdf('sha256', NONCE_KEY, SODIUM_CRYPTO_SECRETBOX_KEYBYTES, 'site-reviews-encryption', NONCE_SALT);
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox('approve|123', $nonce, $previousKey);
    $filter = fn () => 'not the constants';
    add_filter('salt', $filter);
    try {
        expect(encryptionMethod('previousKey'))->toBeNull();
        expect(encryption()->decrypt(encryption()->encode($nonce.$ciphertext)))->toBeFalse();
    } finally {
        remove_filter('salt', $filter);
    }
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
