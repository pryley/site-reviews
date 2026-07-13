<?php

use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Notices\GatekeeperNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The gate an ADDON has to get through.
 *
 * Nothing to do with security, despite the name: a Gatekeeper is handed the plugins an
 * addon depends on, and decides whether the addon may boot. An addon whose parent
 * plugin is missing, switched off, or too old would fatal on somebody's site — so it
 * does not boot, and the reason is put in a transient for GatekeeperNotice to show on
 * the next admin page.
 *
 * The dependency used throughout is Site Reviews itself, because it is the one plugin
 * this process can be certain is installed AND active. Its version is read out of the
 * plugin header, so the tests move the goalposts (the minimum and untested versions)
 * around it rather than trying to install anything.
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
            'plugin_uri' => 'https://example.org/not-a-plugin',
            'untested_version' => '2.0.0',
        ],
    ]))->allows();

    $notice = renderNotice(new GatekeeperNotice());

    expect($notice)->toContain('Not A Plugin')
        ->toContain('https://example.org/not-a-plugin')
        ->toContain('Install Not A Plugin')     // the button, for an admin who may install
        ->toContain('button button-primary')
        ->toContain('_wpnonce')                 // and it is nonced, because it acts
        ->toContain('trigger=notice');          // which is how NoticeController knows it is ours
});

test('the notice is not shown to somebody who cannot do anything about it', function () {
    set_current_screen('plugins');
    (new Gatekeeper([
        'not-a-plugin/not-a-plugin.php' => [
            'minimum_version' => '1.0.0',
            'name' => 'Not A Plugin',
            'plugin_uri' => 'https://example.org/',
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

test('there is no notice when nothing is wrong', function () {
    // Nothing set the transient, so the notice never hooks itself onto admin_notices and
    // WordPress never asks it to draw anything.
    set_current_screen('plugins');

    $notice = new GatekeeperNotice();

    expect(has_action('admin_notices', [$notice, 'render']))->toBeFalse();
    expect(renderNotice($notice))->toBe('');
});
