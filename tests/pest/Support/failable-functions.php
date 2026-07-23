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
 *   Modules:          random_bytes, sodium_crypto_secretbox, sodium_crypto_secretbox_open,
 *                     defined, function_exists
 *   Modules\Avatars:  fwrite
 *   Controllers:      function_exists
 *   Helpers:          extension_loaded, preg_replace_callback
 *   Migrations\Migrate_8_0_0: defined, class_exists
 *   (root):           extension_loaded, class_exists, set_transient, error_get_last
 *   filter_input is armed here too; its shadow lives in filter-input.php.
 *
 * Armed behaviours that are not a throw: defined/function_exists/class_exists/
 * extension_loaded answer false (a build without the thing), set_transient
 * answers false (the write raced and lost), preg_replace_callback answers null
 * (PCRE giving up), error_get_last answers a fatal error inside the plugin
 * (the one state catchFatalError() exists for, and one nothing in-process can
 * put into the real error_get_last()).
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

if (!function_exists(__NAMESPACE__.'\defined')) {
    /**
     * Armed, no constant exists — the state of a wp-config.php with no salts,
     * which Encryption's key derivation has a fallback for.
     */
    function defined(string $constant): bool
    {
        if (functionFails('defined')) {
            return false;
        }
        return \defined($constant);
    }
}

if (!function_exists(__NAMESPACE__.'\function_exists')) {
    /**
     * Armed, no function exists — a load order in which Action Scheduler (the
     * Queue's guards) or ini_get (SystemInfo's) is not available.
     */
    function function_exists(string $function): bool
    {
        if (functionFails('function_exists')) {
            return false;
        }
        return \function_exists($function);
    }
}

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

namespace GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0;

use function GeminiLabs\SiteReviews\Tests\functionFails;

if (!function_exists(__NAMESPACE__.'\defined')) {
    /**
     * Armed, FUSION_BUILDER_VERSION is "not defined" — a site without Avada,
     * which the stubs otherwise make impossible to be.
     */
    function defined(string $constant): bool
    {
        if (functionFails('defined')) {
            return false;
        }
        return \defined($constant);
    }
}

if (!function_exists(__NAMESPACE__.'\class_exists')) {
    /**
     * Armed, Elementor\Plugin is "not loaded" — a site without Elementor,
     * which the stubs otherwise make impossible to be.
     */
    function class_exists(string $class, bool $autoload = true): bool
    {
        if (functionFails('class_exists')) {
            return false;
        }
        return \class_exists($class, $autoload);
    }
}

namespace GeminiLabs\SiteReviews\Controllers;

use function GeminiLabs\SiteReviews\Tests\functionFails;

if (!function_exists(__NAMESPACE__.'\function_exists')) {
    /**
     * Armed, remove_submenu_page() is "not loaded yet" — the admin_init timing
     * the menu controllers' require_once guard exists for.
     */
    function function_exists(string $function): bool
    {
        if (functionFails('function_exists')) {
            return false;
        }
        return \function_exists($function);
    }
}

namespace GeminiLabs\SiteReviews\Helpers;

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

if (!function_exists(__NAMESPACE__.'\preg_replace_callback')) {
    /**
     * Armed, PCRE gives up (backtrack limit) — the null Text::replaceTags()
     * recovers from by leaving the text untagged.
     *
     * @param callable|string|array $pattern
     * @return string|array|null
     */
    function preg_replace_callback($pattern, callable $callback, $subject, int $limit = -1, &$count = null, int $flags = 0)
    {
        if (functionFails('preg_replace_callback')) {
            return null;
        }
        return \preg_replace_callback($pattern, $callback, $subject, $limit, $count, $flags);
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

if (!function_exists(__NAMESPACE__.'\class_exists')) {
    /**
     * Armed, no class exists — the ordinary web request in which WP_CLI is
     * not defined and the CLI constructor must register nothing.
     */
    function class_exists(string $class, bool $autoload = true): bool
    {
        if (functionFails('class_exists')) {
            return false;
        }
        return \class_exists($class, $autoload);
    }
}

if (!function_exists(__NAMESPACE__.'\set_transient')) {
    /**
     * Armed, the transient write reports failure — two requests racing for
     * the Router's mutex lock, where the loser must be refused.
     *
     * @param mixed $value
     */
    function set_transient(string $transient, $value, int $expiration = 0): bool
    {
        if (functionFails('set_transient')) {
            return false;
        }
        return \set_transient($transient, $value, $expiration);
    }
}

if (!function_exists(__NAMESPACE__.'\error_get_last')) {
    /**
     * Armed, the process just died of a fatal error inside the plugin — the
     * one state catchFatalError() exists for, and one nothing in-process can
     * put into the real error_get_last().
     */
    function error_get_last(): ?array
    {
        if (functionFails('error_get_last')) {
            return [
                'type' => E_ERROR,
                'message' => 'Allowed memory size exhausted in '.glsr()->path('plugin/Application.php'),
                'file' => glsr()->path('plugin/Application.php'),
                'line' => 1,
            ];
        }
        return \error_get_last();
    }
}
