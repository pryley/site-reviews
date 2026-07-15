<?php

use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithExits::class);

/*
 * The repair tools, driven through the controller.
 *
 * These are reached in production as `site-reviews/route/admin/{action}` — the
 * hook Router::post() fires from a routed admin POST — and each handler simply
 * executes its command (AbstractController::execute). The non-ajax handlers are
 * used here because the ajax ones end in wp_send_json(), which dies.
 *
 * Each is asserted on the damage it repairs, not on its own success flag:
 * break the state, run the tool, prove the state is fixed.
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
        new Request(['proxy_http_header' => 'HTTP_X_FORWARDED_FOR', 'trusted_proxies' => '10.0.0.1'])
    );

    expect(get_option(glsr()->prefix.'ip_proxy'))
        ->toBeArray()
        ->toHaveKey('proxy_http_header');
});

test('the alt flag detects the ip address instead of configuring the proxy', function () {
    // "alt" switches the same route from saving a header to detecting the address from $_SERVER.
    // Whichever way it goes — a detected address, or the "unable to detect" warning if Whip rejects
    // the suite's loopback REMOTE_ADDR — it reports back and saves no proxy header.
    glsr(Notice::class)->clear();

    glsr(ToolsController::class)->ipAddressDetection(new Request(['alt' => 1]));

    expect(glsr(Notice::class)->get())->not->toBeEmpty();
});

test('migrating the plugin through the admin route re-runs the migrations', function () {
    // alt => 1 is the runAll path: it resets and re-runs every migration, which is deterministic
    // (the plain run() finds nothing pending, because bootstrap.php already migrated). runAll drops
    // the foreign constraints first — DDL, which commits the test's transaction.
    commitsTransaction();
    delete_option(glsr()->prefix.'last_migration_run');

    glsr(ToolsController::class)->migratePlugin(new Request(['alt' => 1]));

    expect((int) get_option(glsr()->prefix.'last_migration_run'))->toBeGreaterThan(0);
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

test('the non-ajax rollback refuses anyone who cannot update plugins', function () {
    // The success path renders an admin page (see ROADMAP / Rollback), but this guard — the one
    // that stops a lower role reaching update.php with a forged version — is reachable. It wp_die()s
    // before touching the nonce or the request.
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $message = $this->expectsWpDie(fn () => glsr(ToolsController::class)->rollbackPlugin());

    expect($message)->toContain('not allowed to rollback');
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
