<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Integrations\Polylang\Controllers\Controller as PolylangController;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers\Controller as ProfilePressController;
use GeminiLabs\SiteReviews\Integrations\SureCart\Controllers\Controller as SureCartController;
use GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers\Controller as UltimateMemberController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\Controller as WooCommerceController;
use GeminiLabs\SiteReviews\Integrations\WPML\Controllers\Controller as WPMLController;
use GeminiLabs\SiteReviews\Notices\GatekeeperNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The gate an ADDON has to get through.
 *
 * Nothing to do with security, despite the name: a Gatekeeper is handed an addon's dependencies and
 * decides whether it may boot. An addon whose parent plugin is missing, off, or too old would fatal
 * a site — so it does not boot, and the reason goes in a transient for GatekeeperNotice to show next
 * admin page.
 *
 * The dependency used throughout is Site Reviews itself, the one plugin this process is certain is
 * installed AND active. Its version is read from the plugin header, so the tests move the goalposts
 * (minimum and untested versions) around it rather than installing anything.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    delete_transient(glsr()->prefix.'gatekeeper');
    glsr()->store('notices', []); // the plugin's in-memory store is not rolled back
});

afterEach(fn () => set_current_screen('front'));

/**
 * The notice, IF it loaded.
 *
 * A notice decides in its constructor whether it has anything to say, and only then
 * hooks itself onto admin_notices. render() does not ask again — so calling it directly
 * on a notice that did not load would draw a notice that WordPress never would.
 */
function renderNotice(GatekeeperNotice $notice): string
{
    if (false === has_action('admin_notices', [$notice, 'render'])) {
        return '';
    }
    ob_start();
    $notice->render();

    return (string) ob_get_clean();
}

/**
 * A dependency on Site Reviews itself, with the version window given.
 */
function dependencyOnSiteReviews(string $minimum, string $untested): array
{
    return [
        glsr()->basename => [
            'minimum_version' => $minimum,
            'name' => 'Site Reviews',
            'plugin_uri' => 'https://wordpress.org/plugins/site-reviews/',
            'untested_version' => $untested,
        ],
    ];
}

test('an addon with no dependencies is let through', function () {
    $gatekeeper = new Gatekeeper([]);

    expect($gatekeeper->allows())->toBeTrue();
    expect($gatekeeper->hasErrors())->toBeFalse();
});

test('an addon whose plugin is installed, current and switched on is let through', function () {
    $gatekeeper = new Gatekeeper(dependencyOnSiteReviews('1.0.0', '99.0.0'));

    expect($gatekeeper->allows())->toBeTrue();
    expect(get_transient(glsr()->prefix.'gatekeeper'))->toBeFalse(); // nothing to tell anyone
});

test('an addon whose plugin is not installed is stopped, and the reason is kept for the notice', function () {
    // The notice is drawn on the NEXT admin page load — by which time the Gatekeeper
    // object is long gone — so the reason has to survive the request. That is what the
    // transient is for.
    $gatekeeper = new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://example.org/',
            'untested_version' => '2.0.0',
        ],
    ]);

    expect($gatekeeper->allows())->toBeFalse();

    $errors = get_transient(glsr()->prefix.'gatekeeper');
    expect($errors['not-a-plugin/not-a-plugin.php']['error'])->toBe(Gatekeeper::ERROR_NOT_INSTALLED);
    expect($errors['not-a-plugin/not-a-plugin.php']['name'])->toBe('Not A Plugin');
});

test('an addon whose plugin is too old to work with is stopped', function () {
    $gatekeeper = new Gatekeeper(dependencyOnSiteReviews('99.0.0', '100.0.0'));

    expect($gatekeeper->allows())->toBeFalse();

    $errors = get_transient(glsr()->prefix.'gatekeeper');
    expect($errors[glsr()->basename]['error'])->toBe(Gatekeeper::ERROR_NOT_SUPPORTED);
});

test('an addon whose plugin is newer than it has been tested against is stopped', function () {
    // The other end of the window, and the one that catches the real danger: a parent
    // plugin that has moved on without the addon. It is a different error from "too
    // old", because the person is told to update a different thing.
    $gatekeeper = new Gatekeeper(dependencyOnSiteReviews('1.0.0', '1.0.1'));

    expect($gatekeeper->allows())->toBeFalse();

    $errors = get_transient(glsr()->prefix.'gatekeeper');
    expect($errors[glsr()->basename]['error'])->toBe(Gatekeeper::ERROR_NOT_TESTED);
});

test('an untested plugin version warns without refusing to enable', function () {
    // The untested ceiling trips on EVERY new major of the parent plugin, and actual
    // breakage is the exception — so it renders its notice but does not stand in the
    // way of somebody enabling the integration.
    $gatekeeper = new Gatekeeper(dependencyOnSiteReviews('1.0.0', '1.0.1'));

    expect($gatekeeper->allows())->toBeFalse(); // the addon boot gate is unchanged
    expect($gatekeeper->allowsEnabling())->toBeTrue();
    expect(get_transient(glsr()->prefix.'gatekeeper'))->not->toBeFalse(); // the notice still shows
});

test('a satisfied dependency is enabled without weighing the error grades', function () {
    // allowsEnabling() short-circuits on allows(). With no failed check there is no
    // error to grade, so the blocking-grade intersection is never reached.
    $gatekeeper = new Gatekeeper(dependencyOnSiteReviews('1.0.0', '99.0.0'));

    expect($gatekeeper->allowsEnabling())->toBeTrue()
        ->and($gatekeeper->hasErrors())->toBeFalse();
});

test('a missing plugin refuses to enable', function () {
    $gatekeeper = new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://example.org/',
            'untested_version' => '2.0.0',
        ],
    ]);

    expect($gatekeeper->allowsEnabling())->toBeFalse();
});

test('a plugin below the supported version refuses to enable', function () {
    expect((new Gatekeeper(dependencyOnSiteReviews('99.0.0', '100.0.0')))->allowsEnabling())->toBeFalse();
});

test('a dependency that does not say what it needs is ignored rather than guessed at', function () {
    // Every one of the four values is required (DependencyDefaults). An addon that
    // declares half a dependency has told the Gatekeeper nothing it can act on, and
    // blocking on it would be blocking on nothing.
    $gatekeeper = new Gatekeeper([
        'some-plugin/some-plugin.php' => ['name' => 'Some Plugin'], // no versions, no uri
    ]);

    expect($gatekeeper->dependencies)->toBe([]);
    expect($gatekeeper->allows())->toBeTrue();
});

test('only the first thing wrong with a plugin is reported', function () {
    // allows() stops at the first failure per plugin — being told a plugin is both
    // missing AND out of date is being told nothing useful.
    $gatekeeper = new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '99.0.0', // would also fail, if it got that far
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://example.org/',
            'untested_version' => '100.0.0',
        ],
    ]);
    $gatekeeper->allows();

    expect($gatekeeper->errors['not-a-plugin/not-a-plugin.php']['error'])
        ->toBe(Gatekeeper::ERROR_NOT_INSTALLED);
});

/*
 * A plugin whose folder was renamed, which somebody may do to switch it off while debugging.
 * The fixture is staged in the container's plugin directory and removed in finally.
 */

const RENAMED_FIXTURE = 'glsr-gatekeeper-renamed/glsr-gatekeeper-fixture.php';

function stageRenamedFixture(string $name): void
{
    $dir = WP_PLUGIN_DIR.'/'.dirname(RENAMED_FIXTURE);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents(WP_PLUGIN_DIR.'/'.RENAMED_FIXTURE, implode("\n", [
        '<?php',
        '/**',
        " * Plugin Name: {$name}",
        ' * Version: 1.0.0',
        ' */',
    ]));
    wp_cache_delete('plugins', 'plugins'); // get_plugins() keeps its answer for the request
}

function unstageRenamedFixture(): void
{
    @unlink(WP_PLUGIN_DIR.'/'.RENAMED_FIXTURE);
    @rmdir(WP_PLUGIN_DIR.'/'.dirname(RENAMED_FIXTURE));
    wp_cache_delete('plugins', 'plugins');
}

/**
 * A dependency on the fixture at the folder it was installed in.
 */
function dependencyOnFixture(): array
{
    return [
        'glsr-gatekeeper-fixture/glsr-gatekeeper-fixture.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Gatekeeper Fixture',
            'plugin_uri' => 'https://example.org/',
            'untested_version' => '2.0.0',
        ],
    ];
}

test('a plugin whose folder was renamed is found there and asked to be activated', function () {
    stageRenamedFixture('Gatekeeper Fixture');
    try {
        $gatekeeper = new Gatekeeper(dependencyOnFixture());

        // So the notice offers to activate the renamed copy, not to install a second one.
        expect($gatekeeper->allows())->toBeFalse()
            ->and(array_keys($gatekeeper->errors))->toBe([RENAMED_FIXTURE])
            ->and($gatekeeper->errors[RENAMED_FIXTURE]['error'])->toBe(Gatekeeper::ERROR_NOT_ACTIVATED);
    } finally {
        unstageRenamedFixture();
    }
});

test('an active plugin whose folder was renamed is let through without reading every plugin', function () {
    stageRenamedFixture('Gatekeeper Fixture');
    $activate = fn ($plugins) => array_merge((array) $plugins, [RENAMED_FIXTURE]);
    add_filter('option_active_plugins', $activate);
    try {
        $gatekeeper = new Gatekeeper(dependencyOnFixture());

        expect($gatekeeper->allows())->toBeTrue()
            ->and(wp_cache_get('plugins', 'plugins'))->toBeFalse(); // get_plugins() never ran
    } finally {
        remove_filter('option_active_plugins', $activate);
        unstageRenamedFixture();
    }
});

test('another plugin with the same file name is not taken for it', function () {
    stageRenamedFixture('Some Other Plugin');
    try {
        $gatekeeper = new Gatekeeper(dependencyOnFixture());
        $gatekeeper->allows();

        expect($gatekeeper->errors['glsr-gatekeeper-fixture/glsr-gatekeeper-fixture.php']['error'])
            ->toBe(Gatekeeper::ERROR_NOT_INSTALLED);
    } finally {
        unstageRenamedFixture();
    }
});

/*
 * The integrations that check their plugin on admin_init, while they are enabled.
 * None of the four plugins is installed in this env, so each check stores a notice.
 */

dataset('gated integrations', [
    'ProfilePress' => [ProfilePressController::class, 'profilepress'],
    'SureCart' => [SureCartController::class, 'surecart'],
    'Ultimate Member' => [UltimateMemberController::class, 'ultimatemember'],
    'WooCommerce' => [WooCommerceController::class, 'woocommerce'],
]);

test('an enabled integration checks its plugin on an admin page load', function (string $controller, string $key) {
    glsr(OptionManager::class)->set("settings.integrations.{$key}.enabled", 'yes');

    glsr($controller)->renderNotice();

    expect(get_transient(glsr()->prefix.'gatekeeper'))->not->toBeFalse();
})->with('gated integrations');

test('an enabled integration does not check its plugin during an ajax request', function (string $controller, string $key) {
    // A missing plugin costs a scan of every installed plugin, and admin_init also runs for the
    // Heartbeat and for a visitor's admin-ajax requests.
    glsr(OptionManager::class)->set("settings.integrations.{$key}.enabled", 'yes');
    add_filter('wp_doing_ajax', '__return_true');
    try {
        glsr($controller)->renderNotice();
    } finally {
        remove_filter('wp_doing_ajax', '__return_true');
    }

    expect(get_transient(glsr()->prefix.'gatekeeper'))->toBeFalse();
})->with('gated integrations');

/*
 * The multilingual integrations, which check their plugin while the Multilingual setting names them.
 * Neither plugin's files are in this env, so each check stores a notice.
 */

dataset('multilingual integrations', [
    'Polylang' => [PolylangController::class, 'polylang'],
    'WPML' => [WPMLController::class, 'wpml'],
]);

test('a chosen multilingual integration checks its plugin on an admin page load', function (string $controller, string $key) {
    glsr(OptionManager::class)->set('settings.general.multilingual', $key);

    glsr($controller)->renderNotice();

    expect(get_transient(glsr()->prefix.'gatekeeper'))->not->toBeFalse();
})->with('multilingual integrations');

test('a multilingual integration that is not chosen does not check its plugin', function (string $controller) {
    glsr(OptionManager::class)->set('settings.general.multilingual', '');

    glsr($controller)->renderNotice();

    expect(get_transient(glsr()->prefix.'gatekeeper'))->toBeFalse();
})->with('multilingual integrations');

test('a chosen multilingual integration does not check its plugin during an ajax request', function (string $controller, string $key) {
    glsr(OptionManager::class)->set('settings.general.multilingual', $key);
    add_filter('wp_doing_ajax', '__return_true');
    try {
        glsr($controller)->renderNotice();
    } finally {
        remove_filter('wp_doing_ajax', '__return_true');
    }

    expect(get_transient(glsr()->prefix.'gatekeeper'))->toBeFalse();
})->with('multilingual integrations');

/*
 * And what the person is shown.
 */

test('the notice tells the person what to do about it, and offers to do it', function () {
    // The reason for the buttons: somebody who has just installed an addon that will not
    // start should be one click from starting it, not reading a paragraph and going
    // looking for the Plugins screen.
    set_current_screen('plugins');
    (new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://wordpress.org/plugins/not-a-plugin/',
            'untested_version' => '2.0.0',
        ],
    ]))->allows();

    $notice = renderNotice(new GatekeeperNotice());

    expect($notice)->toContain('Not A Plugin')
        ->toContain('https://wordpress.org/plugins/not-a-plugin/')
        ->toContain('Install Not A Plugin')     // the button, for an admin who may install
        ->toContain('button button-primary')
        ->toContain('action=install-plugin&#038;plugin=not-a-plugin&#038;') // the slug WordPress installs
        ->toContain('_wpnonce')                 // and it is nonced, because it acts
        ->toContain('trigger=notice');          // which is how NoticeController knows it is ours
});

test('a plugin from outside wordpress.org is linked to its page with no Install button', function () {
    // WordPress can only install a plugin from wordpress.org, so a plugin sold elsewhere,
    // such as WPML, has nothing to install from.
    set_current_screen('plugins');
    (new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://example.org/not-a-plugin',
            'untested_version' => '2.0.0',
        ],
    ]))->allows();

    expect(renderNotice(new GatekeeperNotice()))
        ->toContain('https://example.org/not-a-plugin')
        ->not->toContain('Install Not A Plugin');
});

test('the notice is not shown to somebody who cannot do anything about it', function () {
    set_current_screen('plugins');
    (new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://wordpress.org/plugins/not-a-plugin/',
            'untested_version' => '2.0.0',
        ],
    ]))->allows();
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $notice = renderNotice(new GatekeeperNotice());

    // the message is still drawn — but there is no button, because pressing it would
    // only tell them they are not allowed
    expect($notice)->toContain('Not A Plugin')
        ->not->toContain('button button-primary');
});

test('the notice is shown once and then forgotten', function () {
    // The transient is DELETED as the notice loads. It is set again on the next boot if
    // the addon is still blocked, so a problem that has been fixed stops being
    // announced without anybody having to dismiss anything.
    set_current_screen('plugins');
    (new Gatekeeper(dependencyOnSiteReviews('99.0.0', '100.0.0')))->allows();
    expect(get_transient(glsr()->prefix.'gatekeeper'))->not->toBeFalse();

    new GatekeeperNotice();

    expect(get_transient(glsr()->prefix.'gatekeeper'))->toBeFalse();
});

test('an untested version draws a warning, a blocked plugin draws an error', function () {
    // The colour is the tier: an untested version does not stop anything, so it must
    // not dress like something that did.
    set_current_screen('plugins');

    (new Gatekeeper(dependencyOnSiteReviews('1.0.0', '1.0.1')))->allows(); // untested only
    expect(renderNotice(new GatekeeperNotice()))
        ->toContain('notice-warning')
        ->toContain('has not been tested with');

    (new Gatekeeper(dependencyOnSiteReviews('99.0.0', '100.0.0')))->allows(); // below the floor
    expect(renderNotice(new GatekeeperNotice()))
        ->toContain('notice-error')
        ->toContain('requires the latest version');
});

test('there is no notice when nothing is wrong', function () {
    // Nothing set the transient, so the notice never hooks itself onto admin_notices and
    // WordPress never asks it to draw anything.
    set_current_screen('plugins');

    $notice = new GatekeeperNotice();

    expect(has_action('admin_notices', [$notice, 'render']))->toBeFalse();
    expect(renderNotice($notice))->toBe('');
});
