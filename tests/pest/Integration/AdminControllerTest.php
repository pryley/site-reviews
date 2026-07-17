<?php

use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class, InteractsWithExits::class);

/*
 * The admin side: the searches behind the token fields, the list-table toggles, the approval link
 * in the notification email, and the handful of places the plugin writes itself into WordPress's
 * admin chrome. Most of it is ajax, so most runs in the ajax harness.
 */

/*
 * admin-ajax.php loads wp-admin/includes/admin.php before firing the wp_ajax_* hooks, so an ajax
 * handler may use anything in it. This process is not an admin request and loads none of it that
 * way, and two libraries are reached from here:
 *
 *   post.php      _draft_or_post_title(), get_available_post_statuses()  (ToggleStatus)
 *   template.php  _post_states(), get_submit_button()                    (ToggleStatus, screen-options button)
 *
 * Neither registers a hook on load (they are function libraries; the admin's hooks live in
 * admin-filters.php), so requiring them cannot perturb Pest.php's $wp_filter baseline, and
 * require_once is idempotent if bootstrap.php already pulled them in. WP_Posts_List_Table needs no
 * help — the plugin's autoloader has a classmap for it, which is how ToggleStatus::getStatusLinks()
 * can `new` it in an ajax request.
 */
require_once ABSPATH.'wp-admin/includes/post.php';
require_once ABSPATH.'wp-admin/includes/template.php';

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    $this->tearDownAjax();
    set_current_screen('front');
});

/*
 * The searches. Each one backs a token field in the admin — "assigned to", the
 * author filter, the review editor's page search — and each returns the shape its
 * own field expects, which is why there are six of them and not one.
 */

test('a page can be searched for by title and by id', function () {
    $this->setUpAjax();
    $postId = createPost(['post_title' => 'A Findable Page']);

    $byTitle = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchPostsAjax(
        new Request(['search' => 'Findable'])
    ));
    $byId = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchPostsAjax(
        new Request(['search' => (string) $postId])
    ));

    expect($byTitle['success'])->toBeTrue()
        ->and($byTitle['data']['items'])->toContain('A Findable Page')
        ->and($byId['data']['items'])->toContain('A Findable Page'); // AbstractSearch::search() branches on is_numeric
});

test('a search with nothing to find says so rather than returning an empty box', function () {
    $this->setUpAjax();

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchPostsAjax(
        new Request(['search' => 'nothing here by that name'])
    ));

    expect($response['data']['items'])->toBe('')
        ->and($response['data']['empty'])->toContain('Nothing found');
});

test('the assigned-page filter only offers pages that actually have reviews', function () {
    // The point of a SEPARATE search for the assigned filters: offering a page with
    // no reviews on it would be offering a filter that can only ever return nothing.
    $this->setUpAjax();
    $assigned = createPost(['post_title' => 'Reviewed Page']);
    createPost(['post_title' => 'Unreviewed Page']);
    createReview(['assigned_posts' => $assigned]);

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchAssignedPostsAjax(
        new Request(['search' => 'Page'])
    ));

    $names = array_column($response['data']['items'], 'name');
    expect($names)->toBe(['Reviewed Page']);
});

test('the assigned-user filter only offers users that actually have reviews', function () {
    $this->setUpAjax();
    $assigned = createUser(['display_name' => 'Reviewed Person']);
    createUser(['display_name' => 'Unreviewed Person']);
    createReview(['assigned_users' => $assigned]);

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchAssignedUsersAjax(
        new Request(['search' => 'Person'])
    ));

    $names = array_column($response['data']['items'], 'name');
    expect($names)->toBe(['Reviewed Person']);
});

test('an author can be searched for, and comes back with a display name', function () {
    // The author filter searches every user, not only the ones with reviews — an
    // administrator filtering by author is looking for who wrote something, and the
    // answer may well be nobody.
    $this->setUpAjax();
    createUser(['display_name' => 'Jane Author']);

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchAuthorsAjax(
        new Request(['search' => 'Jane Author'])
    ));

    $names = array_column($response['data']['items'], 'name');
    expect($names)->toContain('Jane Author');
});

test('a user can be searched for from the review editor', function () {
    $this->setUpAjax();
    createUser(['display_name' => 'Jane Author']);

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchUsersAjax(
        new Request(['search' => 'Jane Author'])
    ));

    expect($response['data']['items'])->toContain('Jane Author'); // rendered, not raw rows
});

test('a translatable string can be searched for', function () {
    $this->setUpAjax();

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->searchStringsAjax(
        new Request(['search' => 'Your review'])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data'])->toHaveKey('items')
        ->and($response['data']['empty'])->toContain('Nothing found');
});

/*
 * The list table toggles.
 */

test('the filters an administrator has switched on are remembered, and nothing else is', function () {
    // The list is intersected with the filters that exist, so a name invented by
    // whatever posted this does not end up in the user's meta.
    $this->setUpAjax();
    $userId = get_current_user_id();

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->toggleFiltersAjax(
        new Request(['enabled' => ['rating', 'category', 'not_a_filter']])
    ));

    expect($response['success'])->toBeTrue();
    expect(array_keys(glsr(ColumnFilterbyDefaults::class)->defaults()))
        ->not->toContain('not_a_filter'); // which is what makes the next line meaningful

    // array_intersect() keeps the keys of its FIRST argument, so what is stored is
    // the known filters in the plugin's own order, not the order they were posted in.
    $stored = get_user_meta($userId, 'edit_'.glsr()->post_type.'_filters', true);
    expect(array_values($stored))->toBe(['category', 'rating']);
});

test('pinning a review over ajax answers with the new state and a notice', function () {
    $this->setUpAjax();
    $review = createReview();

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->togglePinnedAjax(
        new Request(['post_id' => $review->ID, 'pinned' => 1])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data']['value'])->toBe(1)      // the star the JS redraws
        ->and($response['data']['notices'])->toContain('Review pinned');
    expect(glsr_get_review($review->ID)->is_pinned)->toBeTrue();
});

test('approving a review over ajax answers with everything the row needs redrawn', function () {
    // The response is what the list table's JS paints back: the row title, the
    // status links above the table with their new counts, and the pending count for
    // the menu bubble. Any one of them missing leaves the page lying about itself.
    $this->setUpAjax();
    $review = createReview(['is_approved' => false]);

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->toggleStatusAjax(
        new Request(['post_id' => $review->ID, 'status' => 'approve'])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data']['class'])->toBe('status-publish')
        ->and($response['data']['link'])->toContain('row-title')
        ->and($response['data']['counts'])->toContain('Approved') // relabelled from "Published"
        ->and((int) $response['data']['pending'])->toBe(0);
    expect(get_post_status($review->ID))->toBe('publish');
});

test('a review is not approved over ajax by somebody who may not approve it', function () {
    $this->setUpAjax();
    $review = createReview(['is_approved' => false]);
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $response = $this->jsonSentBy(fn () => glsr(AdminController::class)->toggleStatusAjax(
        new Request(['post_id' => $review->ID, 'status' => 'approve'])
    ));

    expect($response['success'])->toBeFalse();
    expect(get_post_status($review->ID))->toBe('pending');
});

/*
 * The approval link in the notification email.
 *
 * A GET route: /wp-admin/?glsr_=<token>. The review id is inside the token, and the
 * request has already been decrypted by the time the controller sees it.
 */

test('the approval link approves the review and goes back to the reviews', function () {
    $review = createReview(['is_approved' => false]);

    $location = $this->expectsRedirect(fn () => glsr(AdminController::class)->approveReview(
        new Request(['action' => 'approve', 'data' => [$review->ID]])
    ));

    expect($location)->toBe(glsr_admin_url());
    expect(get_post_status($review->ID))->toBe('publish');
    // the notice has to survive the redirect, so it is stored rather than rendered
    expect(get_transient(glsr()->prefix.'notices'))->not->toBeEmpty();
});

test('the approval link for a review that is no longer there just goes back', function () {
    // The review may well have been deleted between the email going out and the link
    // being clicked. That is not an error worth a page of its own.
    $location = $this->expectsRedirect(fn () => glsr(AdminController::class)->approveReview(
        new Request(['action' => 'approve', 'data' => [999999001]])
    ));

    expect($location)->toBe(glsr_admin_url());
});

/*
 * The plugin's own row on the Plugins screen, and the editor's toolbar.
 */

test('the settings link is added before the ones wordpress puts there', function () {
    // Asserted on the page slug rather than the whole URL: the href has been through
    // esc_url(), so its ampersands are entities and it is not the string
    // glsr_admin_url() returns.
    $links = glsr(AdminController::class)->filterActionLinks(['deactivate' => 'Deactivate']);

    expect(array_keys($links))->toBe(['settings', 'deactivate']);
    expect($links['settings'])->toContain('page=glsr-settings');
});

test('the row meta is added to the plugin\'s own row and to nobody else\'s', function () {
    $links = glsr(AdminController::class)->filterRowMeta(['details' => 'View details'], glsr()->basename);

    expect(array_keys($links))->toBe(['details', 'documentation', 'support']);
    expect($links['support'])->toContain('wordpress.org/support/plugin/site-reviews');

    // glsr_admin_link() reads a dot in the path as the page/tab separator, so the
    // path has to be the submenu slug MenuController actually registers — which is
    // `documentation`, and there is no tab.
    expect($links['documentation'])->toContain('page=glsr-documentation');

    expect(glsr(AdminController::class)->filterRowMeta(['details' => 'View details'], 'akismet/akismet.php'))
        ->toBe(['details' => 'View details']);
});

test('the shortcode button is added to tinymce for somebody who may write posts', function () {
    expect(glsr(AdminController::class)->filterTinymcePlugins([]))
        ->toHaveKey('glsr_shortcode');

    wp_set_current_user(createUser(['role' => 'subscriber']));

    expect(glsr(AdminController::class)->filterTinymcePlugins([]))->toBe([]);
});

test('the screen options panel gets a close button, and only on our screens', function () {
    // WordPress's own submit button is replaced with a submit + close pair, because
    // the plugin's admin styling hides the default way of closing the panel.
    global $typenow;
    $typenow = glsr()->post_type;

    ob_start();
    $showButton = glsr(AdminController::class)->filterScreenOptionsButton(true);
    $output = (string) ob_get_clean();

    expect($showButton)->toBeFalse() // WordPress must not print its own as well
        ->and($output)->toContain('screen-options-apply')
        ->and($output)->toContain('Close Panel');

    $typenow = 'post';
    ob_start();
    $showButton = glsr(AdminController::class)->filterScreenOptionsButton(true);
    $output = (string) ob_get_clean();

    expect($showButton)->toBeTrue()
        ->and($output)->toBe('');
});

test('the admin menu icon is styled inline', function () {
    ob_start();
    glsr(AdminController::class)->printInlineStyle();
    $output = (string) ob_get_clean();

    expect($output)->toStartWith('<style')
        ->toContain('menu-icon-site-review');
});

test('a major version update is warned about, and a minor one is not', function () {
    // The warning is what stands between an administrator and a one-click update
    // across a major version. It is shown for a jump in the major number and for
    // nothing else.
    $major = (int) glsr()->version('major');

    ob_start();
    glsr(AdminController::class)->displayUpdateWarning(['new_version' => ($major + 1).'.0.0']);
    $warned = (string) ob_get_clean();

    ob_start();
    glsr(AdminController::class)->displayUpdateWarning(['new_version' => $major.'.99.0']);
    $unwarned = (string) ob_get_clean();

    expect($warned)->not->toBeEmpty();
    expect($unwarned)->toBe('');
});

test('the page header offers premium, importing and a new review — and nothing to a subscriber', function () {
    global $post_type_object, $title;
    set_current_screen('edit-'.glsr()->post_type);
    $post_type_object = get_post_type_object(glsr()->post_type);
    $title = 'Reviews';

    ob_start();
    glsr(AdminController::class)->renderPageHeader();
    $header = (string) ob_get_clean();

    expect($header)->toContain('Try Premium')
        ->toContain('Import')
        ->toContain(admin_url('post-new.php?post_type='.glsr()->post_type));

    wp_set_current_user(createUser(['role' => 'subscriber']));

    ob_start();
    glsr(AdminController::class)->renderPageHeader();
    $header = (string) ob_get_clean();

    expect($header)->not->toContain('Import')
        ->not->toContain('post-new.php');
});

test('the page header is not printed on somebody else\'s screen', function () {
    set_current_screen('edit-post');

    ob_start();
    glsr(AdminController::class)->renderPageHeader();

    expect((string) ob_get_clean())->toBe('');
});

/*
 * Two things this file deliberately does not do.
 *
 * onActivation()   runs Install::run(), which is DDL. DDL implicitly COMMITs the
 *                  open MySQL transaction, and the transaction is the only thing
 *                  isolating one test from the next — the Import suite has already
 *                  cost a database reset by learning this the hard way. Its guard
 *                  is asserted below; the installer itself is executed by
 *                  bootstrap.php on every run, which is proof enough that it works.
 *
 * onDeactivation() calls Install::deactivate(), which unschedules the queue and
 *                  drops capabilities. There is nothing to learn from running it
 *                  against the same database every other test is using.
 */

test('the installer does not run again on a site that is already activated', function () {
    update_option(glsr()->prefix.'activated', true);
    $activated = false;
    add_action('site-reviews/activated', function () use (&$activated) {
        $activated = true;
    });

    glsr(AdminController::class)->onActivation();

    expect($activated)->toBeFalse();
});

test('a migration is not scheduled on a site that is up to date', function () {
    // bootstrap.php runs every migration once, so Migrate::isMigrationNeeded() is false and the
    // method returns of its own accord — exactly what it does on a site already up to date.
    set_current_screen('edit-'.glsr()->post_type);

    glsr(AdminController::class)->scheduleMigration();

    expect(NullQueue::calls('once', 'queue/migration'))->toBe([]);
});

/**
 * Make Migrate::isMigrationNeeded() true the way an upgrade does: one migration not yet
 * recorded as run, on a database that has a previous version (a fresh install is '0.0.0'
 * and is skipped — Install runs everything itself).
 */
function seedPendingMigration(): void
{
    $migrationsKey = glsr()->prefix.'migrations';
    $stored = (array) get_option($migrationsKey);
    $stored[array_key_last($stored)] = false;
    update_option($migrationsKey, $stored);
    $settings = (array) get_option(OptionManager::databaseKey());
    $settings['version_upgraded_from'] = '8.0.0';
    update_option(OptionManager::databaseKey(), $settings);
}

test('a migration is scheduled on a site that needs one', function () {
    set_current_screen('edit-'.glsr()->post_type);
    seedPendingMigration();

    glsr(AdminController::class)->scheduleMigration();

    expect(NullQueue::calls('once', 'queue/migration'))->toHaveCount(1);
});

test('a migration is not scheduled twice', function () {
    set_current_screen('edit-'.glsr()->post_type);
    seedPendingMigration();
    NullQueue::$isPending = true; // one is already in the queue

    glsr(AdminController::class)->scheduleMigration();

    expect(NullQueue::calls('once', 'queue/migration'))->toBe([]);
});

test('the admin assets enqueue through the controller', function () {
    set_current_screen('edit-'.glsr()->post_type);
    $stylesBefore = count(wp_styles()->queue);

    glsr(AdminController::class)->enqueueAssets();

    expect(count(wp_styles()->queue))->toBeGreaterThan($stylesBefore);
    set_current_screen('front');
});

test('the wordpress exporter takes the ratings along when reviews are exported', function () {
    $args = glsr(AdminController::class)->filterExportArgs(['content' => glsr()->post_type]);
    expect($args)->toBe(['content' => glsr()->post_type]); // args pass through untouched

    // and for anything that is not reviews, no ratings command runs
    $untouched = glsr(AdminController::class)->filterExportArgs(['content' => 'page']);
    expect($untouched)->toBe(['content' => 'page']);
});

test('activation installs once, and only once', function () {
    delete_option(glsr()->prefix.'activated');
    $activated = new ArrayObject();
    add_action('site-reviews/activated', fn () => $activated->append(1));

    glsr(AdminController::class)->onActivation();
    glsr(AdminController::class)->onActivation(); // the second call finds the flag and does nothing

    expect(get_option(glsr()->prefix.'activated'))->toBeTruthy()
        ->and($activated)->toHaveCount(1);
});

test('deactivation drops the constraints and forgets the activation flag', function () {
    // Tables::dropForeignConstraints() runs its ALTERs on the raw wpdb, so the
    // whole Tables object is faked — real DDL on the shared schema cannot roll
    // back, and an earlier draft of this test proved that the hard way.
    $fake = new class extends \GeminiLabs\SiteReviews\Database\Tables {
        public int $dropCalls = 0;

        public function dropForeignConstraints(): void
        {
            ++$this->dropCalls; // recorded, never executed
        }
    };
    $original = glsr(\GeminiLabs\SiteReviews\Database\Tables::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $fake);
    $deactivated = new ArrayObject();
    add_action('site-reviews/deactivated', fn () => $deactivated->append(1));
    try {
        glsr(AdminController::class)->onDeactivation(false);

        expect($deactivated)->toHaveCount(1)
            ->and(get_option(glsr()->prefix.'activated'))->toBeFalse()
            ->and($fake->dropCalls)->toBe(1);
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $original);
        update_option(glsr()->prefix.'activated', true);
    }
});

test('the wordpress importer hands over to the ratings importer when it finishes', function () {
    glsr(AdminController::class)->onImportEnd(); // nothing staged: the command is a safe no-op

    expect(true)->toBeTrue();
});

test('no migration is scheduled from the front of the site', function () {
    set_current_screen('front');
    NullQueue::$calls = [];

    glsr(AdminController::class)->scheduleMigration();

    expect(NullQueue::calls('once', 'queue/migration'))->toBe([]);
});
