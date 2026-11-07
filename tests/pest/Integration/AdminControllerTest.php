<?php

use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithExits::class);

/*
 * The admin side: the approval link in the notification email, and the handful of places the plugin
 * writes itself into WordPress's admin chrome. The searches and the list-table toggles are REST routes
 * (RestApiTest, RestReviewPartsApiTest).
 *
 * template.php holds get_submit_button(), which the screen-options button uses. It is a function
 * library and registers no hook on load, so requiring it cannot perturb Pest.php's $wp_filter baseline.
 */
require_once ABSPATH.'wp-admin/includes/template.php';

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front');
});

/*
 * The approval link in the notification email.
 *
 * A GET route: /wp-admin/?glsr_=<token>. The review id is inside the token, and the
 * request has already been decrypted by the time the controller sees it.
 */

test('the approval link approves the review and goes back to the reviews', function () {
    $review = createReview(['is_approved' => false]);

    $location = $this->expectsRedirectAndExit(fn () => glsr(AdminController::class)->approveReview(
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
    $location = $this->expectsRedirectAndExit(fn () => glsr(AdminController::class)->approveReview(
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
    // The cooldown is opened first, so the return being tested is the up-to-date one.
    set_current_screen('edit-'.glsr()->post_type);
    delete_option(glsr()->prefix.'last_migration_run');

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
    // an upgrade arrives with no recent run; bootstrap's just-ran timestamp
    // would otherwise hold the migration/cooldown closed
    delete_option(glsr()->prefix.'last_migration_run');
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
    // ExportImportTest covers what ImportRatings imports. Here the handover is what matters, and
    // the command's cleanup leaves a mark: Migrate::reset() deletes the stored migration list,
    // so every migration runs again over the imported rows.
    $migrationsKey = glsr()->prefix.'migrations';
    expect(get_option($migrationsKey))->not->toBeFalse(); // bootstrap ran the migrations

    glsr(AdminController::class)->onImportEnd(); // nothing staged, so nothing is imported

    expect(get_option($migrationsKey))->toBeFalse();
});

test('no migration is scheduled from the front of the site', function () {
    // A migration is pending, so the screen check is the only thing that stops the queueing.
    set_current_screen('front');
    seedPendingMigration();
    NullQueue::$calls = [];

    glsr(AdminController::class)->scheduleMigration();

    expect(NullQueue::calls('once', 'queue/migration'))->toBe([]);
});
