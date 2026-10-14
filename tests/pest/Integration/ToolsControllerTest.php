<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Commands\DetectIpAddress;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;


/*
 * The repair tools, driven through the controller.
 *
 * Reached in production as `site-reviews/route/admin/{action}` (the hook Router::post() fires from a
 * routed admin POST), each handler just executes its command (AbstractController::execute). The
 * non-ajax handlers are used here because the ajax ones end in wp_send_json(), which dies.
 *
 * Each is asserted on the damage it repairs, not its success flag: break the state, run the tool,
 * prove the state is fixed.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

test('repairs the review capabilities of a role', function () {
    // RepairPermissions -> Role::resetAll() -> addCapabilities() for every role in
    // Role::roles(). The editor role is granted the review capabilities on install.
    $capability = 'edit_others_site-reviews';
    expect(get_role('editor')->has_cap($capability))->toBeTrue();

    get_role('editor')->remove_cap($capability);
    expect(get_role('editor')->has_cap($capability))->toBeFalse();

    glsr(ToolsController::class)->repairPermissions(new Request());

    expect(get_role('editor')->has_cap($capability))->toBeTrue();
});

test('refuses to repair permissions without the edit_users capability', function () {
    // RepairPermissions::handle() gates on glsr()->can('edit_users').
    $capability = 'edit_others_site-reviews';
    get_role('editor')->remove_cap($capability);
    wp_set_current_user(createUser(['role' => 'editor'])); // an editor cannot edit_users

    glsr(ToolsController::class)->repairPermissions(new Request());

    expect(get_role('editor')->has_cap($capability))->toBeFalse(); // not repaired
});

test('removes rating rows that no longer belong to a review', function () {
    // RepairReviewRelations -> TableRatings::removeInvalidRows(), which deletes every
    // rating row whose review_id is not a post of the review post type.
    $review = createReview();
    $postId = createPost(); // an ordinary post, not a review
    glsr(Database::class)->insert('ratings', [
        'review_id' => $postId, // the foreign key is satisfied: the post exists
        'rating' => 5,
    ]);
    $table = glsr(Tables::class)->table('ratings');
    $countRow = fn (int $id) => (int) glsr(Database::class)->dbGetVar(
        "SELECT COUNT(*) FROM {$table} WHERE review_id = {$id}"
    );
    expect($countRow($postId))->toBe(1);

    glsr(ToolsController::class)->repairReviewRelations();

    expect($countRow($postId))->toBe(0);        // the orphan is gone
    expect($countRow($review->ID))->toBe(1);    // the real review is untouched
});

/*
 * The counts are recalculated with raw SQL, so the assertions below deliberately
 * read them back through get_*_meta() WITHOUT clearing any cache: that is what a
 * plugin, a theme or the next request would do, and it is exactly what used to
 * return the stale value.
 */

test('recalculates the rating counts assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 5]);
    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_post_meta($postId, CountManager::META_REVIEWS);
    delete_post_meta($postId, CountManager::META_AVERAGE);
    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_post_meta($postId, CountManager::META_AVERAGE, true))->toBe(5.0);
});

test('recalculates the rating counts assigned to a category', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    createReview(['assigned_terms' => $termId, 'rating' => 4]);
    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_term_meta($termId, CountManager::META_REVIEWS);
    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_term_meta($termId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_term_meta($termId, CountManager::META_AVERAGE, true))->toBe(4.0);
});

test('recalculates the rating counts assigned to a user', function () {
    $userId = createUser();
    createReview(['assigned_users' => $userId, 'rating' => 3]);
    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBe('1');

    delete_user_meta($userId, CountManager::META_REVIEWS);
    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBeEmpty();

    glsr(ToolsController::class)->resetAssignedMeta();

    expect(get_user_meta($userId, CountManager::META_REVIEWS, true))->toBe('1');
    expect((float) get_user_meta($userId, CountManager::META_AVERAGE, true))->toBe(3.0);
});

/*
 * The other admin-POST handlers. These are the plain (non-ajax) twins of the tools on ToolsAjaxTest:
 * routed as `site-reviews/route/admin/{action}`, they end in a redirect rather than wp_send_json,
 * so — unlike their ajax siblings — they can be called directly and asserted on their effect.
 */

test('changing the console level through the admin route updates the option', function () {
    glsr(ToolsController::class)->changeConsoleLevel(new Request(['level' => Console::NOTICE]));

    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::NOTICE);
});

test('the ip proxy header is saved through the admin route', function () {
    glsr(ToolsController::class)->ipAddressDetection(
        new Request(['proxy_http_header' => 'x-forwarded-for', 'trusted_proxies' => '10.0.0.1'])
    );

    expect(get_option(glsr()->prefix.'ip_proxy')['proxy_http_header'])->toBe('x-forwarded-for');
});

test('every line of a multi-line trusted proxies list is saved', function () {
    // The Tools page posts the form through jQuery's serializeArray(), which sends the lines of a
    // textarea separated by CRLF.
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1\r\n10.0.0.2\r\n10.0.0.0/8\r\n2001:db8::1\r\n10.0.0.3",
    ]));

    expect(get_option(glsr()->prefix.'ip_proxy'))->toBe([
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1\n10.0.0.2\n10.0.0.0/8\n2001:db8::1\n10.0.0.3",
        'version' => glsr()->version,
    ]);
});

test('saving trusted proxies removes the lines that are not valid, and says how many', function () {
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1\r\n10.0.0.1/33\r\nnot-an-address\r\n\r\n10.0.0.0/8",
    ]));

    expect(get_option(glsr()->prefix.'ip_proxy')['trusted_proxies'])->toBe("10.0.0.1\n10.0.0.0/8")
        ->and(glsr(Notice::class)->get())
        ->toContain('The proxy HTTP header has been saved.')
        ->toContain('2 lines were removed from Trusted Proxies');
});

test('saving trusted proxies says nothing about removed lines when every line is valid', function () {
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1\r\n\r\n10.0.0.0/8\r\n",
    ]));

    expect(glsr(Notice::class)->get())->not->toContain('removed from Trusted Proxies');
});

test('the ip detection tool shows a saved trusted proxies list one range to a line', function () {
    // A list saved by 8.3.4 or earlier can hold a carriage return after a CIDR range.
    update_option(glsr()->prefix.'ip_proxy', [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.0/8\r\n10.0.0.3",
    ]);

    $html = glsr()->build('pages/tools/general/configure-ip-detection');

    expect($html)->toContain(">10.0.0.0/8\n10.0.0.3</textarea>");
});

test('the custom option saves the header name that was entered', function () {
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'custom_http_header' => 'X-Sucuri-ClientIP',
        'proxy_http_header' => 'custom',
        'trusted_proxies' => '10.0.0.1',
    ]));

    expect(get_option(glsr()->prefix.'ip_proxy'))->toBe([
        'proxy_http_header' => 'x-sucuri-clientip',
        'trusted_proxies' => '10.0.0.1',
        'version' => glsr()->version,
    ]);
});

test('the custom option saves no header when no name was entered', function () {
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'custom_http_header' => '',
        'proxy_http_header' => 'custom',
    ]));

    expect(get_option(glsr()->prefix.'ip_proxy')['proxy_http_header'])->toBe('');
});

test('a listed header is saved as selected, whatever the custom field holds', function () {
    // The custom field is hidden, not removed, when another option is selected, so the browser
    // still posts what was typed into it.
    glsr(ToolsController::class)->ipAddressDetection(new Request([
        'custom_http_header' => 'X-Sucuri-ClientIP',
        'proxy_http_header' => 'x-forwarded-for',
    ]));

    expect(get_option(glsr()->prefix.'ip_proxy')['proxy_http_header'])->toBe('x-forwarded-for');
});

test('the ip detection tool hides the custom header and trusted proxies while no header is selected', function () {
    $html = glsr()->build('pages/tools/general/configure-ip-detection');

    expect($html)
        ->toContain('<option value="" selected=\'selected\'>None</option>')
        ->toContain('<option value="custom">Custom</option>')
        ->toMatch('/<div class="hidden">\s*<label for="custom_http_header">/')
        ->toMatch('/<div class="full-width hidden">\s*<label for="trusted_proxies">/');
});

test('the ip detection tool shows trusted proxies, and not the custom header, for a listed header', function () {
    update_option(glsr()->prefix.'ip_proxy', [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    $html = glsr()->build('pages/tools/general/configure-ip-detection');

    expect($html)
        ->toContain('<option value="x-forwarded-for" selected=\'selected\'>X-Forwarded-For</option>')
        ->toContain('<option value="custom">Custom</option>')
        ->toContain('name="{{ id }}[custom_http_header]" value=""')
        ->toMatch('/<div class="hidden">\s*<label for="custom_http_header">/')
        ->toMatch('/<div class="full-width">\s*<label for="trusted_proxies">/');
});

test('the ip detection tool selects custom and shows the name for a saved header it does not list', function () {
    update_option(glsr()->prefix.'ip_proxy', [
        'proxy_http_header' => 'x-sucuri-clientip',
        'trusted_proxies' => '10.0.0.1',
    ]);

    $html = glsr()->build('pages/tools/general/configure-ip-detection');

    expect($html)
        ->toContain('<option value="custom" selected=\'selected\'>Custom</option>')
        ->toContain('name="{{ id }}[custom_http_header]" value="x-sucuri-clientip"')
        ->toMatch('/<div class="">\s*<label for="custom_http_header">/')
        ->toMatch('/<div class="full-width">\s*<label for="trusted_proxies">/');
});

test('the alt flag detects the ip address instead of configuring the proxy', function () {
    // "alt" switches the same route from saving a header to detecting the address from $_SERVER.
    // Whichever way it goes — a detected address, or the "unable to detect" warning if Whip rejects
    // the suite's loopback REMOTE_ADDR — it reports back and saves no proxy header.
    glsr(Notice::class)->clear();

    glsr(ToolsController::class)->ipAddressDetection(new Request(['alt' => 1]));

    expect(glsr(Notice::class)->get())->not->toBeEmpty();
});

test('ip detection warns plainly when no address can be worked out', function () {
    // With nothing for Whip to validate, clientIp() returns its "unknown" marker and the tool says
    // so rather than reporting a bogus address. REMOTE_ADDR is unset to force that, then restored.
    $original = $_SERVER['REMOTE_ADDR'] ?? null;
    unset($_SERVER['REMOTE_ADDR']);
    glsr(Notice::class)->clear();

    try {
        $command = new DetectIpAddress();
        $command->handle();

        expect(glsr(Notice::class)->get())->toContain('unable to detect an IP address');
    } finally {
        if (null !== $original) {
            $_SERVER['REMOTE_ADDR'] = $original;
        }
    }
});

/*
 * Why the test ignored the proxy header.
 *
 * The test request reaches the server the way a visitor does, so the address it came from is the
 * address the admin has to enter in Trusted Proxies. Each warning names it.
 */

/**
 * The notices of the Test IP Address Detection button. $server is merged over $_SERVER for the
 * call (a null value removes the key); $setting is the saved tool.
 */
function ipDetectionNotices(array $server, array $setting): string
{
    update_option(glsr()->prefix.'ip_proxy', wp_parse_args($setting, [
        'proxy_http_header' => '',
        'trusted_proxies' => '',
    ]));
    $original = $_SERVER;
    $_SERVER = array_filter(array_merge($_SERVER, $server), fn ($value) => !is_null($value));
    glsr(Notice::class)->clear();
    try {
        (new DetectIpAddress())->handle();
        return glsr(Notice::class)->get();
    } finally {
        $_SERVER = $original;
    }
}

test('the ip detection test says nothing more when the proxy header was used', function () {
    $notices = ipDetectionNotices([
        'HTTP_X_FORWARDED_FOR' => '203.0.113.5',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($notices)
        ->toContain('Your detected IP address is <code>203.0.113.5</code>')
        ->not->toContain('X-Forwarded-For');
});

test('the ip detection test says nothing more when no proxy header is selected', function () {
    $notices = ipDetectionNotices(['REMOTE_ADDR' => '203.0.113.9'], []);

    expect($notices)
        ->toContain('Your detected IP address is <code>203.0.113.9</code>')
        ->not->toContain('header');
});

test('the ip detection test says the header is ignored while no trusted proxies are entered', function () {
    $notices = ipDetectionNotices([
        'HTTP_X_FORWARDED_FOR' => '203.0.113.5',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
    ]);

    expect($notices)
        ->toContain('Your detected IP address is <code>10.0.0.1</code>')
        ->toContain('The <code>X-Forwarded-For</code> header is ignored because Trusted Proxies is empty. This request came from <code>10.0.0.1</code>.');
});

test('the ip detection test says the header is ignored when the request did not come from a trusted proxy', function () {
    $notices = ipDetectionNotices([
        'HTTP_X_FORWARDED_FOR' => '203.0.113.5',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.2',
    ]);

    expect($notices)
        ->toContain('Your detected IP address is <code>10.0.0.1</code>')
        ->toContain('The <code>X-Forwarded-For</code> header is ignored because this request came from <code>10.0.0.1</code> which is not in Trusted Proxies.')
        ->not->toContain('was not sent');
});

test('the ip detection test says when the selected header was not sent', function () {
    $notices = ipDetectionNotices([
        'HTTP_X_FORWARDED_FOR' => null,
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($notices)
        ->toContain('Your detected IP address is <code>10.0.0.1</code>')
        ->toContain('The <code>X-Forwarded-For</code> header was not sent with this request which came from <code>10.0.0.1</code>.')
        ->not->toContain('is ignored');
});

test('the ip detection test gives both reasons when both apply', function () {
    $notices = ipDetectionNotices([
        'HTTP_X_FORWARDED_FOR' => null,
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.2',
    ]);

    expect($notices)
        ->toContain('header was not sent with this request')
        ->toContain('which is not in Trusted Proxies');
});

test('the ip detection test names a custom header as it was saved', function () {
    $notices = ipDetectionNotices(['REMOTE_ADDR' => '10.0.0.1'], [
        'proxy_http_header' => 'x-sucuri-clientip',
    ]);

    expect($notices)->toContain('The <code>x-sucuri-clientip</code> header was not sent');
});

test('migrating the plugin through the admin route re-runs the migrations', function () {
    // alt => 1 is the runAll path: it resets and re-runs every migration, which is deterministic
    // (the plain run() finds nothing pending, because bootstrap.php already migrated). runAll drops
    // the foreign constraints first — DDL, which commits the test's transaction.
    commitsTransaction();
    delete_option(glsr()->prefix.'last_migration_run');

    glsr(ToolsController::class)->migratePlugin(new Request(['alt' => 1]));

    // The ladder stamps 1.4 (Migrate_7_1_0) before Migrate_8_0_0 overwrites
    // it, so the final value proves the tail re-stamp without seeding a
    // stale version — a seed written before the commit point would survive
    // the teardown rollback and poison later tests.
    // The runAll branch says so. The plain run() branch has its own notice.
    expect((int) get_option(glsr()->prefix.'last_migration_run'))->toBeGreaterThan(0)
        ->and(get_option(glsr()->prefix.'db_version'))->toBe(Application::DB_VERSION)
        ->and(glsr(Notice::class)->get())->toContain('All plugin migrations have been run successfully');
});

test('exporting reviews refuses without permission', function () {
    // ExportReviews::handle() returns on a failed permission check BEFORE the CSV output + exit,
    // so the refusal is the one path through this tool a test can reach. hasPermission() only
    // bites on an admin screen.
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    glsr(ToolsController::class)->exportReviews(new Request());

    expect(glsr(Notice::class)->get())->toContain('permission');
    set_current_screen('front');
});

test('importing settings refuses without permission', function () {
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    glsr(ToolsController::class)->importSettings();

    expect(glsr(Notice::class)->get())->toContain('permission');
    set_current_screen('front');
});

test('the non-ajax fetch-console is an intentional no-op', function () {
    // The reload is only ever done over ajax; the routed admin action exists so the route resolves
    // but prints nothing. Pinned so it stays a no-op and nobody wires output into it.
    ob_start();
    glsr(ToolsController::class)->fetchConsole();

    expect(ob_get_clean())->toBe('');
});

/*
 * The forged "update" that a rollback actually rides on.
 *
 * A rollback does not download anything itself — it drops a version number in a transient and lets
 * the normal plugin-update machinery do the work. filterUpdatePluginsTransient is where that
 * happens: while the transient is set, it forges an entry in site_transient_update_plugins pointing
 * WordPress at the wordpress.org zip for the OLDER version, so "update this plugin" installs it.
 */

test('while a rollback is pending, the plugin advertises the old version as its update', function () {
    set_transient(glsr()->prefix.'rollback_version', '8.0.0', MINUTE_IN_SECONDS);
    $value = (object) ['response' => []];

    $filtered = glsr(ToolsController::class)->filterUpdatePluginsTransient($value);

    $update = $filtered->response[glsr()->basename];
    expect($update->new_version)->toBe('8.0.0')
        ->and($update->slug)->toBe(glsr()->id)
        ->and($update->package)->toBe('https://downloads.wordpress.org/plugin/'.glsr()->id.'.8.0.0.zip');
});

test('with no rollback pending, the update transient is handed back untouched', function () {
    delete_transient(glsr()->prefix.'rollback_version');
    $value = (object) ['response' => ['unrelated/unrelated.php' => 'kept']];

    $filtered = glsr(ToolsController::class)->filterUpdatePluginsTransient($value);

    expect($filtered->response)->toBe(['unrelated/unrelated.php' => 'kept']);
});

/*
 * The downloads. Each ends the request through glsr_exit() after echoing raw
 * bytes; capturesDownload() catches the shadow's throw and hands the bytes back.
 */

test('the console download is the console text, and needs the cap', function () {
    glsr_log()->error('a console line to download');

    $bytes = \GeminiLabs\SiteReviews\Tests\capturesDownload(
        fn () => glsr(ToolsController::class)->downloadConsole()
    );

    expect($bytes)->toContain('a console line to download');

    // without the capability the download path is never entered
    wp_set_current_user(createUser(['role' => 'subscriber']));
    set_current_screen('site-reviews_page_site-reviews-tools');
    glsr(ToolsController::class)->downloadConsole();
    expect(glsr(Notice::class)->get())->toContain('permission');
});

test('the system info download is the report', function () {
    $bytes = \GeminiLabs\SiteReviews\Tests\capturesDownload(
        fn () => glsr(ToolsController::class)->downloadSystemInfo()
    );

    expect($bytes)->toContain('[PLUGIN]')
        ->and($bytes)->toContain('Console Level');
});

test('the settings export is the settings as json', function () {
    $bytes = \GeminiLabs\SiteReviews\Tests\capturesDownload(
        fn () => glsr(ToolsController::class)->exportSettings()
    );

    expect((array) json_decode($bytes, true))->toHaveKey('settings');
});

test('the csv template downloads through its route', function () {
    $bytes = \GeminiLabs\SiteReviews\Tests\capturesDownload(
        fn () => glsr(ToolsController::class)->downloadCsvTemplate()
    );

    expect($bytes)->toContain('date')
        ->and($bytes)->toContain('rating');
});

test('the review export is a csv of the reviews', function () {
    createReview(['content' => 'An exportable review body.', 'rating' => 4]);

    $bytes = \GeminiLabs\SiteReviews\Tests\capturesDownload(
        fn () => glsr(ToolsController::class)->exportReviews(new Request())
    );

    expect($bytes)->toContain('An exportable review body.');
});

