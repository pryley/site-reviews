<?php

/*
 * filter_input(), for a process that never received an HTTP request.
 *
 * THE PROBLEM. filter_input() does not read $_GET or $_POST. It reads the SAPI's own copy of
 * the request — a separate structure, populated by the web server, which PHP gives userland no
 * way to write. A CLI process never received a request, so it has no such structure, and
 * filter_input() returns null there no matter what the superglobals say.
 *
 * That makes roughly seventy call sites across the plugin unreachable from a test. It is why
 * ListTableController sat at 29%, and it is the wall behind AbstractAsset, UpdateController,
 * the list-table column filters and the Gutenberg blocks.
 *
 * THE SOLUTION, and it is a standard one. PHP resolves an UNQUALIFIED call to an internal
 * function by looking in the current namespace first, and only then in the global one. The
 * plugin writes `filter_input(...)`, not `\filter_input(...)`, inside its own namespaces — so
 * declaring a function of that name in those namespaces shadows the internal one for that code
 * and for nothing else. This is exactly what php-mock does, and what Brain Monkey does for
 * WordPress functions; it is hand-rolled here because it is thirty lines and one dependency is
 * one dependency.
 *
 * WHY NOT CHANGE THE PLUGIN. It was tried, and reverted. Reading through a helper that falls
 * back to the superglobal sounds harmless, and is not: filter_input() reads the ORIGINAL
 * request and therefore ignores anything another plugin has since injected into $_GET or
 * $_POST — which, on a site running thirty other plugins, is a property worth having. Swapping
 * it for a superglobal read would have changed live behaviour to make the suite's life easier.
 * That is the thing rule 8 exists to prevent, and the plugin is untouched.
 *
 * Loaded from bootstrap.php, so it exists only in a test process. Every declaration is guarded
 * with function_exists() so that a namespace which gains a real one later does not fatal.
 *
 * The list of namespaces below is not decorative: a filter_input() call in a namespace that is
 * NOT listed still reads the (empty) SAPI table and still returns null. If a test cannot see a
 * $_GET value it just set, this list is the first place to look.
 */

namespace GeminiLabs\SiteReviews\Tests;

/**
 * The superglobal behind an INPUT_* constant.
 */
function inputSuperglobal(int $type): ?array
{
    switch ($type) {
        case INPUT_GET: return $_GET;
        case INPUT_POST: return $_POST;
        case INPUT_COOKIE: return $_COOKIE;
        case INPUT_SERVER: return $_SERVER;
        case INPUT_ENV: return $_ENV;
    }

    return null;
}

/**
 * filter_input(), against the superglobals.
 *
 * The return contract is filter_input()'s own, and the difference matters: NULL means the key
 * was not in the request, FALSE means it was there and the filter rejected it. Code branches on
 * the difference (UpdateController asks FILTER_VALIDATE_INT of `force-check` and treats a
 * non-integer as absent), so it is reproduced rather than approximated.
 *
 * @param mixed $options
 *
 * @return mixed
 */
function filterInputShadow(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
{
    $values = inputSuperglobal($type);
    if (null === $values || !array_key_exists($key, $values)) {
        return null;
    }

    return filter_var($values[$key], $filter, $options);
}

/**
 * filter_input_array(), against the superglobals.
 *
 * @param mixed $options
 *
 * @return mixed
 */
function filterInputArrayShadow(int $type, $options = FILTER_DEFAULT, bool $addEmpty = true)
{
    $values = inputSuperglobal($type);
    if (empty($values)) {
        return null; // what filter_input_array() returns when the request type has no data
    }
    if (is_int($options)) {
        return filter_var_array($values, $options);
    }

    return filter_var_array($values, (array) $options, $addEmpty);
}

/*
 * And the shadows themselves, one pair per namespace the plugin calls filter_input() from.
 */

namespace GeminiLabs\SiteReviews;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Commands;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Controllers;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }

    function filter_input_array(int $type, $options = FILTER_DEFAULT, bool $addEmpty = true)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputArrayShadow($type, $options, $addEmpty);
    }
}

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Modules\Assets;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Modules\Validator;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Notices;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Overrides;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks;

if (!function_exists(__NAMESPACE__.'\filter_input')) {
    function filter_input(int $type, string $key, int $filter = FILTER_DEFAULT, $options = 0)
    {
        return \GeminiLabs\SiteReviews\Tests\filterInputShadow($type, $key, $filter, $options);
    }
}
