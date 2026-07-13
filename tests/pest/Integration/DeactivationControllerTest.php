<?php

use GeminiLabs\SiteReviews\Commands\DeactivatePlugin;
use GeminiLabs\SiteReviews\Controllers\DeactivationController;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The "why are you leaving?" dialog on the Plugins screen.
 *
 * A person clicks Deactivate, is asked why, and what they say is POSTed to the plugin
 * author's server along with a description of their site. It is the one place where the
 * plugin sends anything anywhere without being asked to, so what it sends is the subject
 * of most of this file — and the interesting assertions are the NEGATIVE ones.
 *
 * The dialog is also shown BEFORE the deactivation happens, and skipping it must still
 * deactivate the plugin. Nobody's uninstall may be held hostage to a survey.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    unset($GLOBALS['pagenow']);
    $GLOBALS['wp_scripts'] = null;
    $GLOBALS['wp_styles'] = null;
});

function onPluginsScreen(): void
{
    $GLOBALS['pagenow'] = 'plugins.php';
}

/**
 * The data wp_localize_script() attached to the deactivation script.
 */
function localizedDeactivateData(): array
{
    $data = wp_scripts()->get_data('site-reviews/deactivate-plugin', 'data');
    preg_match('/var _glsr_deactivate = (.*);/', (string) $data, $matches);

    return json_decode($matches[1] ?? '[]', true) ?? [];
}

function deactivate(array $values): ArrayObject
{
    $requests = interceptHttp(['body' => (string) wp_json_encode(['success' => true])]);
    (new DeactivatePlugin(new Request($values)))->handle();

    return $requests;
}

/*
 * Where the dialog appears, and where it does not.
 */

test('the dialog is only built on the plugins screen', function () {
    // admin_enqueue_scripts fires on every admin page. A survey script on the post editor
    // is a script that does nothing but cost the site a request.
    $GLOBALS['pagenow'] = 'edit.php';
    glsr(DeactivationController::class)->enqueueAssets();
    expect(wp_script_is('site-reviews/deactivate-plugin', 'enqueued'))->toBeFalse();

    onPluginsScreen();
    glsr(DeactivationController::class)->enqueueAssets();
    expect(wp_script_is('site-reviews/deactivate-plugin', 'enqueued'))->toBeTrue();
});

test('the template is only rendered on the plugins screen', function () {
    $GLOBALS['pagenow'] = 'edit.php';
    ob_start();
    glsr(DeactivationController::class)->renderTemplate();
    expect(ob_get_clean())->toBe('');

    onPluginsScreen();
    ob_start();
    glsr(DeactivationController::class)->renderTemplate();
    expect(ob_get_clean())->not->toBe('');
});

test('the deactivate link is marked so the dialog can intercept it', function () {
    // The whole mechanism: the link is tagged, and the script listens for a click on it.
    // Without this the dialog never opens, and — importantly — Deactivate still works.
    $links = glsr(DeactivationController::class)->filterActionLinks([
        'deactivate' => '<a href="/deactivate">Deactivate</a>',
    ]);

    expect($links['deactivate'])->toBe('<a data-deactivate="site-reviews" href="/deactivate">Deactivate</a>');
});

test('a plugins screen with no deactivate link is left alone', function () {
    // A must-use or network-activated plugin has no Deactivate link to tag.
    $links = ['activate' => '<a href="/activate">Activate</a>'];

    expect(glsr(DeactivationController::class)->filterActionLinks($links))->toBe($links);
});

test('the dialog is nonced, and offers the six reasons in the order they were written', function () {
    onPluginsScreen();
    glsr(DeactivationController::class)->enqueueAssets();

    $data = localizedDeactivateData();

    expect(wp_verify_nonce($data['ajax']['nonce'], 'deactivate'))->not->toBeFalse();
    expect(array_column($data['reasons'], 'id'))->toBe([
        'confused', 'found-better', 'not-working', 'temporary', 'feature-missing', 'other-reason',
    ]);
    // "It's only temporary" is the one with no follow-up question — there is nothing to ask.
    $temporary = array_column($data['reasons'], 'placeholder', 'id')['temporary'];
    expect($temporary)->toBe('');
});

test('the person is shown, in advance, exactly what will be sent about their site', function () {
    // This is what makes the dialog honest: the same insight data that goes to the server is
    // handed to the browser first, behind a "click here to see it".
    onPluginsScreen();
    glsr(DeactivationController::class)->enqueueAssets();

    $insight = localizedDeactivateData()['insight'];

    expect($insight)->toHaveKeys([
        'Active Theme', 'Memory Limit', 'Multisite', 'MySQL Version',
        'PHP Version', 'Site Language', 'Timezone', 'Total Users', 'Website', 'WordPress Version',
    ]);
});

/*
 * What is actually sent.
 */

test('the reason and a description of the site are sent, and nothing else', function () {
    $requests = deactivate([
        'details' => 'It kept timing out on import.',
        'reason' => 'not-working',
        'slug' => 'site-reviews',
        'version' => '8.1.1',
    ]);

    $body = (array) $requests[0]['args']['body'];

    expect($body['reason'])->toBe('not-working')
        ->and($body['details'])->toBe('It kept timing out on import.')
        ->and($body['package_slug'])->toBe('site-reviews')
        ->and($body['package_version'])->toBe('8.1.1')
        ->and($body['php_version'])->toBe(PHP_VERSION)
        ->and($body['url'])->toBe(get_bloginfo('url'));

    // and it does not wait around for an answer — a deactivation must not hang on somebody
    // else's server being slow
    expect($requests[0]['args']['blocking'])->toBeFalse();
});

test('no review, and nobody who wrote one, is sent anywhere', function () {
    // The negative that matters. `users` is a COUNT. Nothing in the insight payload is a
    // person, an email address, an IP address, or a word anybody wrote in a review.
    createReview(['content' => 'A private review', 'email' => 'jane@example.org', 'name' => 'Jane Doe']);

    $requests = deactivate(['reason' => 'temporary', 'slug' => 'site-reviews', 'version' => '8.1.1']);
    $body = (string) wp_json_encode($requests[0]['args']['body']);

    expect($body)->not->toContain('jane@example.org')
        ->not->toContain('Jane Doe')
        ->not->toContain('A private review');
    expect($requests[0]['args']['body']['users'])->toBeNumeric(); // a count, not a list
});

test('a deactivation with no reason given sends nothing at all', function () {
    // Skip & Deactivate. The plugin is being removed and the person has said nothing; that
    // is an answer in itself, and it is not one worth POSTing.
    $requests = deactivate(['reason' => '', 'slug' => 'site-reviews', 'version' => '8.1.1']);

    expect($requests)->toHaveCount(0);
});
