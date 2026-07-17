<?php

/**
 * Namespace shadows that FAIL ON DEMAND, for the error branches whose
 * precondition is the environment misbehaving: the CSPRNG or sodium throwing,
 * a disk writing fewer bytes than asked, a PHP build without fileinfo, the
 * SAPI request table disagreeing with the superglobals. Nothing in-process can
 * make the real functions do any of that, so each gets a shadow in the calling
 * namespace (an unqualified call resolves there first — the filter-input.php
 * mechanism) that passes through to the global builtin until a test arms it.
 *
 * Production is untouched: these functions exist only in the test process, and
 * an unarmed shadow is behaviourally the builtin. Every armed failure is
 * disarmed by resetGlobalState() between tests; a test should still disarm in
 * its own finally, so a failure inside it cannot leak.
 *
 * SHADOWED NAMES — these calls must stay unqualified in the plugin, or the
 * shadow silently loses its grip (a `\` prefix or `use function` bypasses it):
 *   Modules:          random_bytes, sodium_crypto_secretbox, sodium_crypto_secretbox_open
 *   Modules\Avatars:  fwrite
 *   (root):           extension_loaded
 *   filter_input is armed here too; its shadow lives in filter-input.php.
 */

namespace GeminiLabs\SiteReviews\Tests;

/**
 * The registry. Pass an array to replace it; call with no arguments to read.
 */
function failingFunctions(?array $set = null): array
{
    static $armed = [];
    if (null !== $set) {
        $armed = $set;
    }
    return $armed;
}

function armFailingFunction(string ...$functions): void
{
    failingFunctions(array_merge(failingFunctions(), array_fill_keys($functions, true)));
}

function disarmFailingFunctions(): void
{
    failingFunctions([]);
}

function functionFails(string $function): bool
{
    return !empty(failingFunctions()[$function]);
}

namespace GeminiLabs\SiteReviews\Modules;

use function GeminiLabs\SiteReviews\Tests\functionFails;

if (!function_exists(__NAMESPACE__.'\random_bytes')) {
    function random_bytes(int $length): string
    {
        if (functionFails('random_bytes')) {
            throw new \Exception('random_bytes failed (armed by the test suite)');
        }
        return \random_bytes($length);
    }
}

if (!function_exists(__NAMESPACE__.'\sodium_crypto_secretbox')) {
    function sodium_crypto_secretbox(string $message, string $nonce, string $key): string
    {
        if (functionFails('sodium_crypto_secretbox')) {
            throw new \SodiumException('sodium_crypto_secretbox failed (armed by the test suite)');
        }
        return \sodium_crypto_secretbox($message, $nonce, $key);
    }
}

if (!function_exists(__NAMESPACE__.'\sodium_crypto_secretbox_open')) {
    /**
     * @return string|false
     */
    function sodium_crypto_secretbox_open(string $ciphertext, string $nonce, string $key)
    {
        if (functionFails('sodium_crypto_secretbox_open')) {
            throw new \SodiumException('sodium_crypto_secretbox_open failed (armed by the test suite)');
        }
        return \sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    }
}

namespace GeminiLabs\SiteReviews\Modules\Avatars;

use function GeminiLabs\SiteReviews\Tests\functionFails;

if (!function_exists(__NAMESPACE__.'\fwrite')) {
    /**
     * Armed, it writes nothing and reports it — the short write of a full disk.
     *
     * @param resource $stream
     * @return int|false
     */
    function fwrite($stream, string $data, ?int $length = null)
    {
        if (functionFails('fwrite')) {
            return 0;
        }
        return null === $length ? \fwrite($stream, $data) : \fwrite($stream, $data, $length);
    }
}

namespace GeminiLabs\SiteReviews;

use function GeminiLabs\SiteReviews\Tests\functionFails;

if (!function_exists(__NAMESPACE__.'\extension_loaded')) {
    function extension_loaded(string $extension): bool
    {
        if (functionFails('extension_loaded')) {
            return false;
        }
        return \extension_loaded($extension);
    }
}
