<?php

use GeminiLabs\SiteReviews\Premium\Host\Application as HostAddon;
use GeminiLabs\SiteReviews\Premium\HostedThing\Application as HostedAddon;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Hooks.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Hooks.php');

/*
 * Every hook a plugin fires is namespaced with hookPrefix(). A standalone
 * addon uses its own id, so nothing about an installed addon changes. A
 * HOSTED addon uses its host's id plus its own slug —
 * site-reviews-premium/{slug}/{hook} — because the host alone would collide:
 * core fires {prefix}/activated once per addon and every addon binds its
 * installer to it.
 *
 * The addon's own id keeps firing first as a deprecated alias, so a snippet
 * written against the standalone addon still runs after the site moves to
 * the bundled build.
 */

beforeEach(function () {
    resetPluginState();
    glsr()->register(TestAddon::class);
    // WordPress raises E_USER_DEPRECATED for a legacy hook that still has a
    // listener. That is the point, and it is asserted below through the
    // deprecated_hook_run action rather than by catching the notice.
    add_filter('deprecated_hook_trigger_error', '__return_false');
});

afterEach(function () {
    // The addon registry lives on the Application singleton and resetPluginState()
    // does not clear it, so registering the hosted fixtures here would leak into
    // the next file — AddonSettingsStorageTest asserts that an addon with no main
    // file and no host is REFUSED, which cannot be true if it is already listed.
    $property = new ReflectionProperty(glsr(), 'addons');
    $property->setAccessible(true);
    $property->setValue(glsr(), array_diff_key(
        $property->getValue(glsr()),
        array_flip([HostAddon::ID, HostedAddon::ID])
    ));
});

function hostedFixture(): array
{
    glsr()->register(HostAddon::class);
    $host = glsr(HostAddon::class);
    glsr()->register(HostedAddon::class, $host);
    return [$host, glsr(HostedAddon::class)];
}

test('a standalone addon namespaces its hooks with its own id', function () {
    expect(glsr(TestAddon::class)->hookPrefix())->toBe('site-reviews-test-addon')
        ->and(glsr()->hookPrefix())->toBe(glsr()->id);
});

test('a hosted addon namespaces its hooks with the host and its own slug', function () {
    [$host, $addon] = hostedFixture();

    expect($addon->hookPrefix())->toBe('site-reviews-premium-host/hosted-thing')
        ->and($host->hookPrefix())->toBe('site-reviews-premium-host');
});

test('a hosted addon fires the hosted hook', function () {
    [, $addon] = hostedFixture();
    $heard = null;
    add_filter('site-reviews-premium-host/hosted-thing/greeting', function ($value) use (&$heard) {
        return $heard = $value.' (hosted)';
    });

    expect($addon->filterString('greeting', 'hello'))->toBe('hello (hosted)')
        ->and($heard)->toBe('hello (hosted)');
});

test('a hosted addon still fires the standalone hook it replaced', function () {
    [, $addon] = hostedFixture();
    add_filter('site-reviews-hosted-addon/greeting', fn ($value) => $value.' (legacy)');

    // The legacy listener runs first, and its result reaches the hosted hook.
    add_filter('site-reviews-premium-host/hosted-thing/greeting', fn ($value) => $value.' (hosted)');

    expect($addon->filterString('greeting', 'hello'))->toBe('hello (legacy) (hosted)');
});

test('the legacy alias carries every argument, not just the filtered one', function () {
    [, $addon] = hostedFixture();
    $seen = [];
    add_filter('site-reviews-hosted-addon/greeting', function ($value, $who) use (&$seen) {
        $seen = [$value, $who];
        return $value;
    }, 10, 2);

    $addon->filterString('greeting', 'hello', 'world');

    expect($seen)->toBe(['hello', 'world']);
});

test('an action fires under both names too', function () {
    [, $addon] = hostedFixture();
    $calls = [];
    add_action('site-reviews-hosted-addon/departed', function () use (&$calls) { $calls[] = 'legacy'; });
    add_action('site-reviews-premium-host/hosted-thing/departed', function () use (&$calls) { $calls[] = 'hosted'; });

    $addon->action('departed');

    expect($calls)->toBe(['legacy', 'hosted']);
});

test('a standalone addon fires its hook once, with no deprecation', function () {
    $calls = [];
    add_filter('site-reviews-test-addon/greeting', function ($value) use (&$calls) {
        $calls[] = 'own';
        return $value;
    });

    // No @ here on purpose: a standalone addon must not emit a deprecation
    // notice for its own hook, which is the name it has always used.
    expect(glsr(TestAddon::class)->filterString('greeting', 'hello'))->toBe('hello')
        ->and($calls)->toBe(['own']);
});

test('the deprecation names the hook that replaced it, and only when heard', function () {
    [, $addon] = hostedFixture();
    $deprecations = [];
    add_action('deprecated_hook_run', function ($hook, $replacement) use (&$deprecations) {
        $deprecations[] = [$hook, $replacement];
    }, 10, 2);

    $addon->filterString('unheard', 'x'); // nothing listens to the legacy name
    expect($deprecations)->toBe([]);

    add_filter('site-reviews-hosted-addon/heard', fn ($value) => $value);
    $addon->filterString('heard', 'x');

    expect($deprecations)->toBe([
        ['site-reviews-hosted-addon/heard', 'site-reviews-premium-host/hosted-thing/heard'],
    ]);
});

test('the array-filtering wrappers fire the legacy alias too', function () {
    // filterArrayUnique / …Int / …String each route through deprecatedHook() the same way
    // filter() does, so a legacy snippet feeding one of the array hooks still contributes.
    [, $addon] = hostedFixture();
    add_filter('site-reviews-hosted-addon/a-list', fn ($values) => array_merge($values, ['legacy', 'legacy']));
    add_filter('site-reviews-hosted-addon/some-ints', fn ($values) => array_merge($values, ['3', 3, 'x']));
    add_filter('site-reviews-hosted-addon/some-strings', fn ($values) => array_merge($values, ['b', 'b', 2]));

    expect($addon->filterArrayUnique('a-list', ['own']))->toBe(['own', 'legacy'])
        ->and($addon->filterArrayUniqueInt('some-ints', [1]))->toBe([1, 3])
        ->and($addon->filterArrayUniqueString('some-strings', ['a']))->toBe(['a', 'b', '2']);
});

/**
 * A module whose build stamped a VERSION constant — the merged premium plugin does this at build
 * time so each module reports the standalone release it was merged from.
 */
class VersionStampedModule extends HostedAddon
{
    public const VERSION = '1.2.3';
}

test('a module with a stamped version reports it instead of the host version', function () {
    [$host] = hostedFixture();

    $module = new VersionStampedModule($host);

    expect($module->version)->toBe('1.2.3')
        ->and($host->version)->toBe('9.9.9'); // the fallback the stamp overrides
});

test('the base addon hooks bind to the prefix, not to the addon id', function () {
    // Addons\Hooks builds these two listener names itself. They were "{id}/…",
    // the same string as the prefix until a hosted addon's prefix stopped being
    // its id — after which install() was bound to the deprecated alias instead
    // of the hook core fires, and every render tripped a deprecation notice.
    hostedFixture();
    $baseHooks = fn () => $this->baseHooks([]);
    $hooks = $baseHooks->bindTo(
        glsr(GeminiLabs\SiteReviews\Premium\HostedThing\Hooks::class),
        GeminiLabs\SiteReviews\Addons\Hooks::class
    )();
    $names = array_column($hooks, 1);

    expect($names)->toContain('site-reviews-premium-host/hosted-thing/render/view')
        ->and($names)->toContain('site-reviews-premium-host/hosted-thing/activated')
        ->and($names)->not->toContain('site-reviews-hosted-addon/render/view')
        ->and($names)->not->toContain('site-reviews-hosted-addon/activated')
        // the id still names what is not a hook: text domain, plugin file
        ->and($names)->toContain('gettext_site-reviews-hosted-addon');
});
