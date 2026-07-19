<?php

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Modules\Notice;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;

/*
 * The base class every integration extends: the version gate and the notice it raises when the gate
 * closes.
 *
 * The stubs track current releases (make stubs), so which gates are closed on any given boot is a
 * moving target — when this file was first written, fusion-builder, breakdance and wpbakery all
 * declared versions below their gates and notify() ran on every boot; regenerated stubs have since
 * opened them. These tests therefore drive the gate and the notice DIRECTLY, with versions they
 * control, rather than leaning on whichever stub happens to be out of date.
 *
 * notify() fixes the crash that stopped the suite booting at all: integrations hook on
 * plugins_loaded, and since WP 6.7 a translation requested before init triggers _doing_it_wrong(), a
 * notice, which phpunit.xml turns into a failure. The deferral is what these tests pin.
 */

/**
 * A concrete integration whose reported versions the test controls. The two
 * methods it overrides are the only two the version gate reads.
 */
class VersionGatedHooks extends IntegrationHooks
{
    public string $installed = '';
    public string $required = '';

    protected function supportedVersion(): string
    {
        return $this->required;
    }

    protected function version(): string
    {
        return $this->installed;
    }
}

function versionGatedHooks(string $installed, string $required): VersionGatedHooks
{
    $hooks = new VersionGatedHooks();
    $hooks->installed = $installed;
    $hooks->required = $required;

    return $hooks;
}

function versionGateIsOpen(string $installed, string $required): bool
{
    return protectedMethod(VersionGatedHooks::class, 'isVersionSupported')
        ->invoke(versionGatedHooks($installed, $required));
}

function notifyOfUnsupportedVersion(string $name, string $required): void
{
    protectedMethod(VersionGatedHooks::class, 'notify')
        ->invoke(versionGatedHooks('1.0', $required), $name);
}

beforeEach(function () {
    // The Notice module is a singleton (Provider::register), and the three stubs
    // above have already added their warnings to it during the boot. Its notices
    // are held in memory, so neither the transaction rollback nor wp_cache_flush
    // clears them.
    glsr(Notice::class)->clear();
});

afterEach(function () {
    glsr(Notice::class)->clear();
});

test('the gate is open when the integration requires no particular version', function () {
    // supportedVersion() returns '' by default, which is what every integration
    // without a version gate (BuddyBoss, MyCred, …) inherits.
    expect(versionGateIsOpen('1.0.0', ''))->toBeTrue();
    expect(versionGateIsOpen('', ''))->toBeTrue();
});

test('the gate is open on the exact supported version and above', function () {
    expect(versionGateIsOpen('3.12.0', '3.12.0'))->toBeTrue();
    expect(versionGateIsOpen('3.12.1', '3.12.0'))->toBeTrue();
    expect(versionGateIsOpen('4.0', '3.12.0'))->toBeTrue();
});

test('the gate is closed below the supported version', function () {
    expect(versionGateIsOpen('3.11.7', '3.12.0'))->toBeFalse();    // the fusion-builder stub
    expect(versionGateIsOpen('7.9.0', '8.0'))->toBeFalse();        // the wpbakery stub
    expect(versionGateIsOpen('2.3.0-rc.2', '2.5.0'))->toBeFalse(); // the breakdance stub
});

test('the gate is closed when the version cannot be read', function () {
    // Every version() implementation returns '' when its constant is undefined,
    // so an integration that is installed but whose version is unreadable is
    // treated as unsupported rather than as supported. WPLoyalty is in exactly
    // that position under the stubs: its four classes are declared but
    // WLR_PLUGIN_VERSION is not.
    expect(versionGateIsOpen('', '1.4.0'))->toBeFalse();
});

test('the notice is added immediately once init has run', function () {
    // did_action('init') is non-zero for the whole of a test run, so this is the
    // branch a late-registering integration takes.
    expect(did_action('init'))->toBeGreaterThan(0);

    notifyOfUnsupportedVersion('Avada Builder', '3.12.0');

    expect(glsr(Notice::class)->get())
        ->toContain('Update Avada Builder to version 3.12.0 or higher')
        ->toContain('notice-warning');
});

test('the notice is deferred to init when init has not yet run', function () {
    // The real condition: integrations run on plugins_loaded, which is before
    // init. did_action() reads $wp_actions (wp-includes/plugin.php), and Pest.php
    // backs that global up and restores it after every test, so removing the key
    // here is contained to this test.
    unset($GLOBALS['wp_actions']['init']);
    expect(did_action('init'))->toBe(0);

    $callbacks = fn (): array => $GLOBALS['wp_filter']['init']->callbacks[10] ?? [];
    $before = count($callbacks());

    notifyOfUnsupportedVersion('Breakdance', '2.5.0');

    // Nothing was translated and no notice was added: the work is now on init.
    expect(glsr(Notice::class)->get())->toBe('');
    $after = $callbacks();
    expect(count($after))->toBe($before + 1);

    // WP_Hook::add_filter appends within a priority and only re-sorts the
    // priorities themselves, so the callback just registered is the last one.
    $registered = end($after)['function'];
    $registered();

    expect(glsr(Notice::class)->get())->toContain('Update Breakdance to version 2.5.0 or higher');
});
