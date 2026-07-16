<?php

/*
 * filter_input(), for a process that never received an HTTP request.
 *
 * filter_input() reads the SAPI's copy of the request, not $_GET/$_POST — a structure the web
 * server populates and userland cannot write. A CLI process has none, so filter_input() returns
 * null there whatever the superglobals hold, making ~70 call sites unreachable from a test (the
 * wall behind ListTableController, AbstractAsset, UpdateController, the column filters and the
 * Gutenberg blocks).
 *
 * The fix is the standard one: PHP resolves an UNQUALIFIED call to an internal function in the
 * current namespace first, so declaring filter_input() in the plugin's namespaces shadows it for
 * that code and nothing else — what php-mock and Brain Monkey do, hand-rolled here to avoid a
 * dependency. The plugin is untouched: routing through a superglobal-reading helper instead was
 * tried and reverted, because filter_input() reads the ORIGINAL request and so ignores anything
 * another plugin injected into $_GET/$_POST since — a property worth keeping on a busy site.
 *
 * Loaded from bootstrap.php, so it exists only in a test process; each declaration is guarded
 * with function_exists(). The namespace list below is load-bearing: a filter_input() call in a
 * namespace not listed still reads the empty SAPI table and returns null. If a test cannot see a
 * $_GET value it just set, look here first.
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
 * filter_input(), against the superglobals. The return contract is filter_input()'s own: NULL
 * means the key was absent, FALSE means it was present and the filter rejected it. Code branches
 * on the difference (UpdateController asks FILTER_VALIDATE_INT of `force-check`), so it is
 * reproduced, not approximated.
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
