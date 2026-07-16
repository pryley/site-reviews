<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

/*
 * The pieces of a WordPress test harness the suite needs: content factories and
 * a plugin-settings baseline. The factories mirror WP_UnitTest_Factory — same
 * defaults (sequenced title/name/login, published posts, post_tag terms) and
 * return values (an ID from create(), the object from *AndGet()) — and
 * everything they insert rolls back with the per-test transaction (Pest.php).
 */

/**
 * A known-good baseline of plugin settings, applied by Pest.php to the files
 * that need one. Migrations run once in bootstrap.php, not here.
 */
function resetPluginState(): void
{
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    $_SERVER['SERVER_NAME'] = '';
    $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = referer();
    $defaults = Arr::unflatten(glsr()->defaults());
    glsr(OptionManager::class)->replace($defaults);
    // The session is a plain array on the Application singleton (plugin/Session.php) — not a
    // transient or option — so the per-test transaction cannot touch it. It matters: a failed
    // validation writes `form_invalid`, and ValidatorAbstract::validate() then SKIPS validation
    // entirely, so one failing submission leaves every validation test after it passing blind.
    glsr()->sessionClear();
}

/**
 * Declares that this test WILL commit the per-test transaction, expected rather than a bug.
 *
 * Two things end a transaction: DDL (MySQL commits implicitly on CREATE/ALTER/DROP TABLE —
 * TableTmp is created and dropped by every CSV import) and an explicit START TRANSACTION
 * (Database::beginTransaction() on InnoDB, which finishTransaction() then COMMITs). Pest.php
 * catches an undeclared commit with a sentinel row and fails the test, since the damage lands
 * elsewhere: the leaked user breaks the NEXT run with "Sorry, that username already exists".
 *
 * Thirteen call sites commit legitimately (the README maps them; the grep is the authority):
 * the Import suite (no import without TableTmp's DDL), the tests reaching Migrate::runAll()
 * (ExportImportTest, ToolsControllerTest's alt re-run, CliTest) — where MigrateReviews wraps
 * each pass in a real START TRANSACTION/COMMIT and Migrate_6_2_1 may run DDL to repair a
 * PRIMARY index — and the TRUNCATE/repair tools (MaintenanceTest, PrivacyCommandsTest,
 * ToolsAjaxTest's remove-location, InstallTest). A declared test is purged afterwards
 * (purgeCommittedRows()) instead of failed; the declaration is per-test and Pest.php clears it.
 */
function commitsTransaction(): void
{
    commitWasDeclared(true);
}

/**
 * The Application's STORAGE as it is once the plugin has booted — the state every real request,
 * and so every test, begins in. The Storage trait (plugin/Storage.php) is an Arguments object on
 * the singleton, not a table/option/hook, so nothing else in teardown touches it, yet twenty
 * registers write to it and several leak dangerously between tests:
 *
 *   paged_handle        NormalizePaginationArgs reads `page` from it on every review query, so a
 *                       stale value makes every shortcode/block/widget think it is on page 2 —
 *                       they print "There are no reviews yet", in other files.
 *   review_types        the type dropdown appears once a site has more than one review kind, so a
 *                       test adding `google` makes it appear for every test after.
 *   glsr_create_review  ReviewManager::postStatus() reads it to tell a form submission from an
 *                       import; leaked, it is WP_IMPORTING all over again.
 *
 * Snapshotted once after boot and restored between tests, rather than a hand-kept register list
 * that would always be one bug behind the plugin.
 */
function snapshotStorage(): void
{
    storageSnapshot(glsr()->storage()->getArrayCopy());
}

/**
 * `settings` is the one register with an owner other than this. OptionManager memoises it and
 * re-reads it in resetGlobalState(), which runs BEFORE this — restoring the boot-time copy would
 * undo that refresh and hand every test the process-start settings, not what its own
 * resetPluginState() left. (Found by NoticeTest, which reads version_upgraded_from.) So it is
 * left as OptionManager put it and everything else is restored around it.
 */
function restoreStorage(): void
{
    $storage = glsr()->storage();
    $settings = $storage->get('settings');
    $storage->exchangeArray(storageSnapshot());
    if (!is_null($settings)) {
        $storage->set('settings', $settings);
    }
}

/**
 * @param array|null $set
 */
function storageSnapshot(?array $set = null): array
{
    static $snapshot = [];
    if (null !== $set) {
        $snapshot = $set;
    }

    return $snapshot;
}

/**
 * Declares that this test WILL cause WP_IMPORTING to be defined.
 *
 * define() cannot be undone, and the plugin reads WP_IMPORTING in eighteen places to mean "this
 * review did not come from a form". Once defined, every later test in the process gets no avatar,
 * no verification email, no recalculated counts, no cache flush, and is_pinned / is_verified /
 * ip_address stop being protected. Only the Import suite may do it, and phpunit.xml declares that
 * suite LAST; Pest.php fails any undeclared test that defines the constant. Declared rather than
 * detected because Pest compiles each file into an eval()'d class, leaving no reliable path.
 */
function definesWpImporting(): void
{
    wpImportingWasDeclared(true);
}

/**
 * The flag definesWpImporting() sets, which Pest.php reads and then clears. Not for use in a
 * test: a test says definesWpImporting(), and says it plainly.
 */
function wpImportingWasDeclared(?bool $set = null): bool
{
    static $declared = false;
    if (null !== $set) {
        $declared = $set;
    }

    return $declared;
}

/**
 * The flag commitsTransaction() sets, which Pest.php reads and then clears. Not for use in a
 * test: a test says commitsTransaction(), and says it plainly.
 */
function commitWasDeclared(?bool $set = null): bool
{
    static $declared = false;
    if (null !== $set) {
        $declared = $set;
    }
    return $declared;
}

/**
 * Undoes by hand what the rollback could not, for a test that ran DDL. Only rows written BEFORE
 * the DDL are permanent — autocommit is off, so MySQL opens a fresh transaction at the implicit
 * commit and everything after rolls back. In practice that is the user a beforeEach created
 * (user_login is unique; review posts are not). Reviews go too, because they are counted; the
 * three assignment tables and the ratings table cascade from the review posts.
 */
function purgeCommittedRows(): void
{
    global $wpdb;
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type
    ));
    $wpdb->query(
        "DELETE pm FROM {$wpdb->postmeta} pm
         LEFT JOIN {$wpdb->posts} p ON (p.ID = pm.post_id)
         WHERE p.ID IS NULL"
    );
    $wpdb->query("DELETE FROM {$wpdb->users} WHERE ID > 1"); // 1 is the wp-env admin
    $wpdb->query(
        "DELETE um FROM {$wpdb->usermeta} um
         LEFT JOIN {$wpdb->users} u ON (u.ID = um.user_id)
         WHERE u.ID IS NULL"
    );

    // The plugin's TRANSIENTS are options too, so they commit like anything else. Learned the
    // hard way: two tests committed (TableStats::empty() runs TRUNCATE) before anyone declared
    // it, and what stuck was Api's cached response for an IP — every geolocation test afterwards
    // was served that cached success instead of making a request, including the one asserting a
    // FAILED lookup stores nothing. Transients are disposable, so all of the plugin's go; the
    // settings, migration record and db version are options, not transients, and stay.
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '\\_transient\\_glsr\\_%'
            OR option_name LIKE '\\_transient\\_timeout\\_glsr\\_%'
            OR option_name LIKE '\\_site\\_transient\\_glsr\\_%'
            OR option_name LIKE '\\_site\\_transient\\_timeout\\_glsr\\_%'
            OR option_name = 'glsr_api_transients'"
    );

    // The MIGRATION RECORD, the subtle casualty of a committed migration run. A test that
    // reaches Migrate::runAll() (CliTest, the Tools re-run) commits its DDL, but the FINAL
    // updateMigrationStatus() write lands in the post-DDL transaction the harness rolls
    // back — so the committed database ends the test with glsr_migrations MISSING, and every
    // later test that so much as touches runMigrations() finds all of them pending and
    // re-runs them: DDL, an undeclared commit, and the tripwire fires two files away. The
    // schema itself is current — the DDL committed — so the honest repair is the record
    // bootstrap's own runAll() writes: every migration in the tree, marked run. Raw SQL like
    // everything else here: the object cache still holds the pre-rollback copy at this point
    // (it is flushed AFTER the purge), so get_option()/update_option() would no-op.
    $migrations = (new \ReflectionProperty(\GeminiLabs\SiteReviews\Modules\Migrate::class, 'migrations'))
        ->getValue(glsr(\GeminiLabs\SiteReviews\Modules\Migrate::class)); // from the filesystem, cache-free
    $wpdb->query($wpdb->prepare(
        "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
         VALUES (%s, %s, 'auto') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
        glsr()->prefix.'migrations',
        serialize(array_fill_keys($migrations, true))
    ));
}

/**
 * Releases the router's parallel-request lock. Router::isValidMutexRequest() sets a transient
 * keyed by a hash of the client IP for five seconds and refuses a second `submit-review` while it
 * is there. Every test request comes from the same IP within the same second, so a test that
 * submits twice looks like the attack it guards against; releasing the lock between requests
 * stands in for the time that would have passed. A test exercising the mutex just skips this —
 * see RouterTest.
 */
function releaseMutexLock(): void
{
    delete_transient(mutexLock());
}

/**
 * The transient Router::isValidMutexRequest() locks with, derived the same way. Hashed, so a
 * visitor's IP never lands in the options table.
 */
function mutexLock(): string
{
    return Str::prefix(Str::hash(Helper::clientIp(), 13), glsr()->prefix);
}

/**
 * The referer every request is pinned to.
 */
function referer(): string
{
    return '/index.php';
}

/**
 * A per-run sequence so generated titles, names and logins are unique within a test run.
 */
function sequence(): int
{
    static $sequence = 0;
    return ++$sequence;
}

function createPost(array $args = []): int
{
    $number = sequence();
    $postId = wp_insert_post(array_merge([
        'post_content' => "Post content {$number}",
        'post_excerpt' => "Post excerpt {$number}",
        'post_status' => 'publish',
        'post_title' => "Post title {$number}",
        'post_type' => 'post',
    ], $args), true);
    if (is_wp_error($postId)) {
        throw new \RuntimeException('Test post not created: '.$postId->get_error_message());
    }
    return (int) $postId;
}

function createPostAndGet(array $args = []): \WP_Post
{
    return get_post(createPost($args));
}

/**
 * @return int[]
 */
function createPosts(int $count, array $args = []): array
{
    return array_map(fn () => createPost($args), range(1, $count));
}

function createTerm(array $args = []): int
{
    $number = sequence();
    $args = array_merge([
        'description' => "Term description {$number}",
        'name' => "Term {$number}",
        'taxonomy' => 'post_tag',
    ], $args);
    $name = $args['name'];
    $taxonomy = $args['taxonomy'];
    unset($args['name'], $args['taxonomy']);
    $term = wp_insert_term($name, $taxonomy, $args);
    if (is_wp_error($term)) {
        throw new \RuntimeException('Test term not created: '.$term->get_error_message());
    }
    return (int) $term['term_id'];
}

/**
 * @return int[]
 */
function createTerms(int $count, array $args = []): array
{
    return array_map(fn () => createTerm($args), range(1, $count));
}

function createUser(array $args = []): int
{
    $number = sequence();
    $userId = wp_insert_user(array_merge([
        'user_email' => "user_{$number}@example.org",
        'user_login' => "User {$number}",
        'user_pass' => 'password',
    ], $args));
    if (is_wp_error($userId)) {
        throw new \RuntimeException('Test user not created: '.$userId->get_error_message());
    }
    return (int) $userId;
}

function createUserAndGet(array $args = []): \WP_User
{
    return get_userdata(createUser($args));
}

/**
 * @return int[]
 */
function createUsers(int $count, array $args = []): array
{
    return array_map(fn () => createUser($args), range(1, $count));
}

/**
 * Snapshot of the hook globals, taken before each test and restored after, so a filter a test
 * adds cannot leak into the next — without it, `add_filter('site-reviews/validators', …)` in one
 * test changes the outcome of every test after it.
 */
function hookBackup(): \ArrayObject
{
    static $backup;
    return $backup ??= new \ArrayObject([]);
}

/**
 * The hook globals from wp-includes/plugin.php:
 *
 *     WP_Hook[] $wp_filter          the callbacks, one WP_Hook per hook
 *     int[]     $wp_actions         did_action counts
 *     int[]     $wp_filters         did_filter counts (6.1+)
 *     string[]  $wp_current_filter  the hook stack
 *
 * $wp_filter holds objects, so each is cloned rather than copied by reference; the rest are plain
 * arrays. Only globals that are set are touched.
 */
const HOOK_GLOBALS = ['wp_actions', 'wp_filters', 'wp_current_filter'];

function backupHooks(): void
{
    $backup = ['wp_filter' => []];
    foreach (HOOK_GLOBALS as $key) {
        if (isset($GLOBALS[$key])) {
            $backup[$key] = $GLOBALS[$key];
        }
    }
    foreach ($GLOBALS['wp_filter'] as $hookName => $hook) {
        $backup['wp_filter'][$hookName] = clone $hook;
    }
    hookBackup()->exchangeArray($backup);
}

function restoreHooks(): void
{
    $backup = hookBackup()->getArrayCopy();
    if (empty($backup)) {
        return;
    }
    foreach (HOOK_GLOBALS as $key) {
        if (array_key_exists($key, $backup)) {
            $GLOBALS[$key] = $backup[$key];
        }
    }
    $GLOBALS['wp_filter'] = [];
    foreach ($backup['wp_filter'] as $hookName => $hook) {
        $GLOBALS['wp_filter'][$hookName] = clone $hook;
    }
}

/**
 * The rest of the per-request state reset between tests: the logged-in user and the request
 * superglobals.
 */
function resetRequestState(): void
{
    wp_set_current_user(0);
    $_GET = [];
    $_POST = [];
    $_REQUEST = [];
    $_FILES = [];
}

/**
 * Everything a database transaction CANNOT roll back: globals, the DI container, an in-memory
 * session, static properties. A test that runs after one which left the right state behind would
 * otherwise pass for the wrong reason — `make test:random` shuffled the order and 39 tests fell
 * over, one asserting the review form renders only because the previous test had logged someone
 * in. So the floor is swept between tests; a test wanting a user, an admin screen, a filter, a
 * licence or a language must say so. If a test passes alone but fails under `make test:random`,
 * what it leaned on is missing from here.
 */
function resetGlobalState(): void
{
    // WordPress globals. None of these are options, so none of them roll back.
    $GLOBALS['pagenow'] = 'index.php';
    $GLOBALS['mode'] = 'list';
    $GLOBALS['wp_query'] = new \WP_Query();
    $GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
    $GLOBALS['wp_scripts'] = null;   // rebuilt on demand by wp_scripts()
    $GLOBALS['wp_styles'] = null;
    $GLOBALS['wp_meta_boxes'] = [];
    unset($GLOBALS['post'], $GLOBALS['avail_post_stati']);
    // Roles: WP_Roles::add_cap() mutates $wp_roles in memory as well as the option. The rollback
    // restores the option; the global keeps the modified role until dropped.
    unset($GLOBALS['wp_roles']);
    if (function_exists('set_current_screen')) {
        set_current_screen('front');
    }

    // The plugin's in-memory state on the Application SINGLETON. The session is dangerous: a
    // failed validation writes `form_invalid` and ValidatorAbstract::validate() then SKIPS
    // validation, leaving every validation test after it green without validating.
    glsr()->sessionClear();
    glsr(\GeminiLabs\SiteReviews\Modules\Notice::class)->clear();
    // The console is a FILE (Console.php appends to it), so the transaction cannot roll it
    // back; without this, a "was it logged" assertion can be satisfied by an earlier test's
    // residue. clear() truncates the file, and Console re-reads it on every construction.
    glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->clear();

    // THE SETTINGS, the subtlest. OptionManager keeps them in memory and replace() short-circuits
    // when update_option() reports "no change" — which it does when the value equals what is
    // already stored. After a test changes a setting and the rollback restores the default, the
    // next resetPluginState() writes the default, update_option() sees no change, and the
    // in-memory copy is left holding the PREVIOUS test's settings. (Harmless in production, where
    // the store cannot disagree with the database.) So re-read the settings from the rolled-back
    // database unconditionally; flush the cache first or get_option() returns the pre-rollback value.
    \GeminiLabs\SiteReviews\Database\OptionManager::flushSettingsCache();
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->reset();

    // The container. A binding made in a test outlives it, so the suite's own bindings are put
    // back and anything else a test bound is overwritten.
    glsr()->bind(\GeminiLabs\SiteReviews\Modules\Queue::class, NullQueue::class, true);
    glsr()->bind(\GeminiLabs\SiteReviews\License::class, \GeminiLabs\SiteReviews\License::class, true);

    // The Application's whole STORAGE, back to a freshly booted request. See snapshotStorage().
    restoreStorage();

    // Application::$settings is the settings CONFIG (fields, not values), memoised per request;
    // Application::license() appends a licence field per licensed addon, so a test registering one
    // would leave that field for every test after. $defaults is built from $settings and memoised
    // beside it. Empty both so settings() rebuilds from config/settings.php.
    foreach (['defaults', 'settings'] as $memo) {
        (new \ReflectionProperty(glsr(), $memo))->setValue(glsr(), []);
    }

    // The fakes' own static state.
    NullQueue::$isPending = false;
    NullQueue::$calls = [];
    FakeLicense::$isPremium = false;
    PolylangFake::reset();
    if (class_exists('\Akismet')) {
        \Akismet::reset();
    }
    // Messages only. WP_CLI::$commands records a one-time registration at plugin load — clearing
    // it would be clearing evidence, not state.
    if (class_exists('\WP_CLI')) {
        \WP_CLI::reset();
    }
}

/**
 * The mail the suite has "sent". There is no mail transport in the test container, so wp_mail()
 * would return false; `pre_wp_mail` short-circuits it the same way and records what would have
 * gone out. Registered once in bootstrap.php, emptied before each test by Pest.php.
 */
function mailbox(): \ArrayObject
{
    static $mailbox;
    return $mailbox ??= new \ArrayObject([]);
}

function interceptMail(): void
{
    add_filter('pre_wp_mail', function ($shortCircuit, $atts) {
        mailbox()->append($atts);
        return true; // what wp_mail() returns on success
    }, 10, 2);
}

/**
 * @return array[] one entry per wp_mail() call: to, subject, message, headers…
 */
function sentMail(): array
{
    return mailbox()->getArrayCopy();
}

/**
 * The recipients of one email, as a list. array_values() matters: EmailDefaults::finalize() drops
 * empty `recipients` entries with Arr::removeEmptyValues(), which preserves keys — so a list with
 * a blank in the middle comes out with a hole. wp_mail() does not care; a list assertion would.
 *
 * @return string[]
 */
function sentTo(int $index = 0): array
{
    $mail = sentMail();

    return array_values((array) ($mail[$index]['to'] ?? []));
}

function emptyMailbox(): void
{
    mailbox()->exchangeArray([]);
}

/**
 * Nothing may leave the test container over HTTP. `pre_http_request` is WordPress's own
 * short-circuit and runs before the URL is validated, catching every request whatever the
 * transport. Registered LAST (priority 999), it speaks only when nothing else has: a test making
 * a request on purpose intercepts at the default priority with interceptHttp(), and this returns
 * whatever that returned. Otherwise a loud WP_Error — a test that reaches the network is slow,
 * flaky, and may be POSTing a fixture to a real webhook. Registered once in bootstrap.php.
 */
function blockHttpRequests(): void
{
    add_filter('pre_http_request', function ($response, $args, $url) {
        if (false !== $response) {
            return $response; // a test is intercepting this one on purpose
        }
        return new \WP_Error('http_request_failed', sprintf(
            'The test suite does not make HTTP requests, and something tried to reach %s. '.
            'If the code under test is meant to, intercept it with interceptHttp().',
            $url
        ));
    }, 999, 3);
}

/**
 * Every HTTP request the code under test makes, and a 200 in reply to each.
 *
 * @return \ArrayObject<int, array{url:string, args:array}>
 */
function interceptHttp(array $response = []): \ArrayObject
{
    $requests = new \ArrayObject();
    add_filter('pre_http_request', function ($pre, $args, $url) use ($requests, $response) {
        $requests->append(compact('args', 'url'));

        return array_replace([
            'body' => '',
            'cookies' => [],
            'filename' => null,
            'headers' => [],
            'http_response' => null,
            'response' => ['code' => 200, 'message' => 'OK'],
        ], $response);
    }, 10, 3);

    return $requests;
}

/**
 * The body of an intercepted request, decoded.
 */
function sentJson(\ArrayObject $requests, int $index = 0): array
{
    $body = $requests[$index]['args']['body'] ?? '';

    return json_decode((string) $body, true) ?? [];
}

/**
 * An approved review, created through the plugin's own public API so the ratings/assigned tables
 * are populated exactly as in production.
 */
function createReview(array $values = []): \GeminiLabs\SiteReviews\Review
{
    $number = sequence();
    $review = glsr_create_review(array_replace([
        'content' => "This is the content of test review {$number}.",
        'email' => "reviewer_{$number}@example.org",
        'is_approved' => true,
        'name' => "Reviewer {$number}",
        'rating' => 5,
        'title' => "Test review {$number}",
    ], $values));
    if (!$review instanceof \GeminiLabs\SiteReviews\Review) {
        throw new \RuntimeException('Test review not created.');
    }
    return $review;
}

/**
 * @return \GeminiLabs\SiteReviews\Review[]
 */
function createReviews(int $count, array $values = []): array
{
    return array_map(fn () => createReview($values), range(1, $count));
}

/**
 * Invoke a protected or private method on an instance.
 */
function protectedMethod(string $className, string $method): \ReflectionMethod
{
    $reflection = new \ReflectionMethod($className, $method);
    $reflection->setAccessible(true);
    return $reflection;
}

/**
 * An instance whose constructor never ran — for classes whose constructor has side effects the
 * test does not want.
 */
function unconstructed(string $className): object
{
    return (new \ReflectionClass($className))->newInstanceWithoutConstructor();
}
