<?php

use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The three things the Tools page will hand you as a file.
 *
 *   the settings      a JSON dump of every setting — including the paid addons' licence keys and the
 *                     secret keys for whichever CAPTCHA the site uses.
 *   the console       the plugin's error log: file paths, SQL fragments, failed-call arguments —
 *                     what people paste into public support threads.
 *   the system info   the site's PHP, MySQL, theme, plugin list and configuration.
 *
 * None is catastrophic alone, all of it is reconnaissance. So each has a capability check, and
 * AbstractController::download() has a SECOND (`can edit_others_posts`), the last line before the
 * headers go out.
 *
 * Only the refusals are tested, and not from laziness: download() ends in a bare `exit`, which no
 * PHP code can intercept (the suite catches wp_die()/wp_redirect() by throwing; `exit` takes the
 * process with it). The success path cannot be driven at all — which is why the refusals are worth
 * pinning down.
 */

beforeEach(function () {
    resetPluginState();
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
});

afterEach(function () {
    set_current_screen('front');
    glsr(Notice::class)->clear();
});

function tools(): ToolsController
{
    return glsr(ToolsController::class);
}

/**
 * Whatever the controller printed — which, for a refusal, must be nothing.
 */
function printedBy(callable $callback): string
{
    ob_start();
    $callback();

    return (string) ob_get_clean();
}

test('the settings export carries the licence keys and the captcha secrets', function () {
    // Establishing what is at stake before testing who may have it. This is the payload.
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-real-licence-key');
    glsr(OptionManager::class)->set('settings.forms.turnstile.secret', 'a-real-captcha-secret');

    $json = glsr(OptionManager::class)->json();

    expect($json)->toContain('a-real-licence-key')
        ->and($json)->toContain('a-real-captcha-secret');
});

test('a subscriber is not given the settings', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-real-licence-key');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $output = printedBy(fn () => tools()->exportSettings());

    expect($output)->toBe('') // nothing was sent, and nothing exited
        ->and($output)->not->toContain('a-real-licence-key');
    expect(glsr(Notice::class)->get())->toContain('do not have permission');
});

test('a subscriber is not given the console', function () {
    glsr_log()->error('SQL error in /var/www/html/wp-content/plugins/site-reviews/plugin/Database.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $output = printedBy(fn () => tools()->downloadConsole());

    expect($output)->toBe('')
        ->and($output)->not->toContain('/var/www/html');
    expect(glsr(Notice::class)->get())->toContain('do not have permission');
});

test('a subscriber is not given the system info', function () {
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $output = printedBy(fn () => tools()->downloadSystemInfo());

    expect($output)->toBe('');
    expect(glsr(Notice::class)->get())->toContain('do not have permission');
});

test('download refuses anybody who cannot edit other people\'s posts, whatever asked it to', function () {
    // The second guard, and the one that catches a caller that forgot the first. Every route
    // into a download goes through here, so this is the floor.
    wp_set_current_user(createUser(['role' => 'author'])); // may write, may not edit others'

    $output = printedBy(fn () => tools()->download('secrets.txt', 'a-real-licence-key'));

    expect($output)->toBe(''); // no headers, no body, and no exit
});
