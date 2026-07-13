<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Install;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Activation: the first thing that ever happens on a site, and the only thing that MUST work.
 *
 * Everything else in the plugin can degrade. This cannot: a review is a post with rows in six
 * custom tables, and if those tables are not there, nothing the plugin does afterwards can work
 * and nothing it does can tell the person why.
 *
 * install() has to be safe to run over and over, because WordPress runs it on every activation —
 * and people deactivate and reactivate a plugin as the first thing they try when something is
 * wrong. So it creates tables that may already exist, adds constraints that may already be there,
 * and resets capabilities that may already be correct, and does all of it without touching the
 * reviews already on the site. That last part is the assertion worth having: somebody with four
 * thousand reviews toggling the plugin off and on must not lose them.
 *
 * EVERY TEST HERE COMMITS. It is all DDL — CREATE TABLE, ALTER TABLE, DROP TABLE — and MySQL
 * commits the open transaction on each of them, so the rollback the rest of the suite relies on
 * cannot undo any of it. Hence commitsTransaction(), and hence the finally block in the drop test:
 * an assertion that failed between the DROP and the rebuild would take the database out from under
 * every test that ran afterwards.
 */

beforeEach(function () {
    commitsTransaction(); // all of this is DDL
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

/*
 * THE db_version OPTION IS NOT AN ORDINARY OPTION, and this file is the only place that deletes it.
 *
 * migration.php registers this, unconditionally, for the life of every request:
 *
 *   function glsr_migration_5_9_db_version_1_1(array $values) {
 *       if (version_compare(glsr(Database::class)->version(), '1.1', '<')) {
 *           unset($values['terms']);
 *       }
 *       return $values;
 *   }
 *   add_filter('site-reviews/defaults/rating', 'glsr_migration_5_9_db_version_1_1');
 *
 * It is right to exist: a database older than 1.1 has no `terms` column, and sending one would be
 * an error on every review. But Database::version() reads the option LIVE, and
 * version_compare('', '1.1', '<') is TRUE — so with the option missing, the plugin believes it is
 * on a pre-1.1 schema no matter what the schema actually is. RatingDefaults then loses `terms`,
 * the INSERT omits the column, and MySQL's `terms tinyint(1) NOT NULL DEFAULT '1'` fills it in.
 * Every review is recorded as having ACCEPTED THE TERMS, and nothing anywhere says so.
 *
 * These tests commit, so a deleted db_version would stay deleted for the rest of the process and
 * fabricate a consent record on every review created after it. Hence this afterEach.
 */
afterEach(function () {
    if (empty(get_option(glsr()->prefix.'db_version'))) {
        glsr(Install::class)->run();
    }
});

function tableNames(): array
{
    return protectedMethod(Install::class, 'tables')->invoke(glsr(Install::class));
}

/**
 * What MySQL says each of the plugin's tables actually is — the schema as built, not as declared.
 *
 * @return array<string, string> tablename => CREATE TABLE statement
 */
function tableSchemas(): array
{
    global $wpdb;
    $schemas = [];
    foreach (tableNames() as $table) {
        $row = $wpdb->get_row("SHOW CREATE TABLE {$table}", ARRAY_N);
        // AUTO_INCREMENT=n is the row counter, not the schema. A rebuilt table starts it at 1
        // again, and that is not a difference anybody could care about.
        $schemas[$table] = preg_replace('/ AUTO_INCREMENT=\d+/', '', (string) ($row[1] ?? ''));
    }

    return $schemas;
}

/*
 * Activating.
 */

test('activating the plugin creates every table it needs', function () {
    glsr(Install::class)->run();

    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
    expect(tableNames())->not->toBeEmpty();
});

test('activating twice is not different from activating once', function () {
    // Which is what happens every time somebody deactivates and reactivates the plugin to see if
    // that fixes it — and they always do.
    glsr(Install::class)->run();
    $before = get_option(glsr()->prefix.'db_version');

    glsr(Install::class)->run();

    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
    expect(get_option(glsr()->prefix.'db_version'))->toBe($before);
});

test('reactivating does not touch the reviews already on the site', function () {
    // The assertion this whole file exists for. CREATE TABLE runs against tables that are already
    // there and full of somebody's reviews, and the reviews have to still be there afterwards.
    $review = createReview(['content' => 'Four thousand of these.', 'rating' => 4]);

    glsr(Install::class)->run();

    $reloaded = glsr_get_review($review->ID);
    expect($reloaded->isValid())->toBeTrue()
        ->and($reloaded->content)->toBe('Four thousand of these.')
        ->and($reloaded->rating)->toBe(4);
});

test('activating gives the roles their capabilities back', function () {
    // The other half of "reactivate and see". Capabilities live on the role, and another plugin —
    // or a botched migration — can strip them; reactivating is the documented way to get them back.
    glsr(Role::class)->hardResetAll();
    glsr(Install::class)->run();

    $capabilities = (array) get_role('administrator')->capabilities;
    $ours = array_filter(
        $capabilities,
        fn ($granted, $cap) => $granted && str_contains($cap, glsr()->post_type),
        ARRAY_FILTER_USE_BOTH
    );

    expect($ours)->not->toBeEmpty();
});

test('the database version is recorded, so that the migrations know where to start', function () {
    // An install with no db_version is indistinguishable from one that predates the custom tables,
    // and the migrations would try to build a schema that is already there.
    delete_option(glsr()->prefix.'db_version');

    glsr(Install::class)->run();

    expect(get_option(glsr()->prefix.'db_version'))->toBe(Application::DB_VERSION);
});

/*
 * Deactivating.
 */

test('deactivating drops the foreign constraints, and keeps the data', function () {
    // The constraints are dropped so that WordPress can delete posts without the plugin's tables
    // refusing — but a deactivation is not an uninstall, and somebody who deactivates the plugin
    // to test a theme conflict expects their reviews to be there when they turn it back on.
    $review = createReview();

    glsr(Install::class)->deactivate(false);

    expect(get_option(glsr()->prefix.'activated'))->toBeFalse();
    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
    expect(glsr_get_review($review->ID)->isValid())->toBeTrue();

    glsr(Install::class)->run(); // and reactivating puts the constraints back
});

test('deactivating tells anybody who was listening', function () {
    $fired = new ArrayObject();
    add_action('site-reviews/deactivated', fn () => $fired->append(true));

    glsr(Install::class)->deactivate(false);

    expect($fired)->toHaveCount(1);

    glsr(Install::class)->run();
});

/*
 * Uninstalling.
 */

test('dropping the tables really drops them, and the plugin can rebuild from nothing', function () {
    // This is uninstall, and it is run THE WAY WORDPRESS RUNS IT: deactivate first, then
    // uninstall. That order is not a formality — dropTables() issues a bare DROP TABLE and does
    // not touch the foreign keys, because by the time it runs, deactivate() has already dropped
    // them. Call it on its own and MySQL refuses ("Cannot delete or update a parent row"), the
    // ratings table survives with its rows while its children are rebuilt empty, and the schema
    // is left in a state that breaks every test that comes after.
    //
    // It is also the only test in the suite that can leave the database unrecoverable, so the
    // rebuild is in a finally and runs whether the assertions pass or not.
    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
    $before = tableSchemas();

    try {
        glsr(Install::class)->deactivate(false); // WordPress deactivates before it uninstalls
        glsr(Install::class)->dropTables();

        expect(glsr(Tables::class)->tablesExist())->toBeFalse();
        expect(get_option(glsr()->prefix.'db_version'))->toBeFalse();
    } finally {
        glsr(Install::class)->run();
    }

    // THE ASSERTION THIS FILE TURNED OUT TO NEED. A fresh install builds its tables from
    // structure(); an upgraded site gets there by running the migrations over an older schema.
    // Those two paths have to arrive at the SAME schema, and nothing else checks that they do —
    // if they diverge, every new site quietly runs on a schema no existing site has, and the
    // difference will not surface until somebody's reviews behave differently on it.
    expect(tableSchemas())->toBe($before);

    // …and a plugin activated onto a site with no tables at all builds them, which is the very
    // first thing that happens on every new install.
    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
    expect(glsr_get_reviews())->not->toBeNull(); // and it can be queried
});

/*
 * Multisite.
 */

test('a single-site activation never goes looking for other sites', function () {
    // sites() cannot even be CALLED here: get_sites() is declared in wp-includes/ms-blogs.php,
    // which WordPress only loads on a network, so calling it on a single site is a fatal.
    //
    // That is not a defect, it is the shape of the thing — every caller of sites() is behind
    // is_multisite() or is_plugin_active_for_network(), and this suite is a single site. So the
    // assertion available here is the one that matters on a single site: run() takes the OTHER
    // branch, installs once, and never touches the multisite path at all. If it ever did, this
    // test would not fail politely — it would be a fatal, which is precisely the point.
    expect(function_exists('get_sites'))->toBeFalse();
    expect(is_multisite())->toBeFalse();

    glsr(Install::class)->run();

    expect(glsr(Tables::class)->tablesExist())->toBeTrue();
});

/*
 * The 5.9 compatibility shim, and what it costs.
 */

test('a database that predates the terms column does not have terms sent to it', function () {
    // What the shim is FOR, and it is correct: a pre-1.1 database has no `terms` column, and an
    // INSERT naming one would fail on every single review.
    update_option(glsr()->prefix.'db_version', '1.0');

    expect(glsr(RatingDefaults::class)->defaults())->not->toHaveKey('terms');
});

test('a current database DOES have terms sent to it', function () {
    // The other side, and the one that bites. `terms` is a record of consent. If the column is not
    // sent, MySQL applies the schema default — which is DEFAULT '1' — and the review is stored as
    // having accepted terms it may never have been shown.
    //
    // Database::version() reads glsr_db_version live, and version_compare('', '1.1', '<') is TRUE.
    // So an EMPTY option is treated exactly like an ancient database, whatever the schema really
    // is: every review created while it is missing gets a consent record it never gave.
    update_option(glsr()->prefix.'db_version', Application::DB_VERSION);

    expect(glsr(RatingDefaults::class)->defaults())->toHaveKey('terms');
});

test('a review created on a current database stores the terms it was actually given', function () {
    // The end of the chain, asserted on the row rather than on the model — because the model would
    // report `true` from ReviewDefaults whether the column was written or not.
    global $wpdb;
    update_option(glsr()->prefix.'db_version', Application::DB_VERSION);

    $review = createReview();

    expect($wpdb->get_var($wpdb->prepare(
        "SELECT terms FROM {$wpdb->prefix}glsr_ratings WHERE review_id = %d", $review->ID
    )))->toBe('0');
});
