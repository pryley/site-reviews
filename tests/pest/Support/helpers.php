<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

/*
 * Replacements for the pieces of WordPress core's test framework the phpunit
 * suite leaned on. Core's framework is pinned to PHPUnit 9 and cannot be
 * loaded alongside Pest, so the two things the tests actually used from it —
 * the content factories and the Setup trait — are reimplemented here.
 *
 * The factories mirror WP_UnitTest_Factory: the same defaults (a sequenced
 * title/name/login, published posts, post_tag terms) and the same return
 * values (an ID from create(), the object from *AndGet()), so an ported
 * assertion means what it meant before. Everything they insert is rolled
 * back by the transaction in Pest.php.
 */

/**
 * The port of the Setup trait (tests/phpunit/tests/Setup.php): a known-good
 * baseline of plugin settings, applied by Pest.php to the files that used it.
 *
 * It used to run the migrations as well, and no longer does — they run once, in
 * bootstrap.php, which explains why. Nothing else here has changed: the settings
 * are still put back to their defaults before every test that asks for it.
 */
function resetPluginState(): void
{
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    $_SERVER['SERVER_NAME'] = '';
    $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = referer();
    $defaults = Arr::unflatten(glsr()->defaults());
    glsr(OptionManager::class)->replace($defaults);
}

/**
 * Declares that this test WILL commit the per-test transaction, and that this is expected
 * rather than a bug.
 *
 * A transaction cannot isolate a test that ends it, and two things end one:
 *
 *   DDL                 MySQL commits implicitly the moment it sees CREATE, ALTER or DROP
 *                       TABLE. TableTmp is created and dropped by every CSV import.
 *   START TRANSACTION   issued explicitly. Database::beginTransaction() does exactly that
 *                       on InnoDB, and finishTransaction() then COMMITs.
 *
 * Pest.php watches for it with a sentinel row and fails any test it catches, because the
 * damage never lands where it was done: the leaked user breaks the NEXT run, in another
 * file, with "Sorry, that username already exists".
 *
 * Four do it legitimately. The Import suite, which cannot import without TableTmp. And
 * the three that reach Migrate::runAll() — ImportSettings twice, MigratePlugin once —
 * which commit for BOTH reasons at once:
 *
 *   Migrate_5_25_0/MigrateReviews wraps each of its four passes in beginTransaction() /
 *   finishTransaction(), which on InnoDB is a literal START TRANSACTION and COMMIT.
 *
 *   Migrate_6_2_1::migrateDatabaseIndexes() rebuilds a PRIMARY index that Migrate_6_0_0
 *   got wrong on MariaDB, which means dropping the assignment tables' foreign constraints
 *   and adding them back. It only does so when the index actually needs repairing, but
 *   addForeignConstraints() still runs on every pass and can add DDL of its own.
 *
 * A test that declares this is purged after it — see purgeCommittedRows() — instead of
 * being failed. The declaration is per-test; Pest.php clears it.
 */
function commitsTransaction(): void
{
    commitWasDeclared(true);
}

/**
 * The flag commitsTransaction() sets, which Pest.php reads and then clears. Not for use
 * in a test: a test says commitsTransaction(), and says it plainly.
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
 * Undoes by hand what the rollback could not, for a test that ran DDL.
 *
 * Only the rows written BEFORE the DDL are permanent: autocommit is off, so MySQL opens
 * a fresh transaction the moment the implicit commit lands, and everything after it still
 * rolls back. In practice that means the user a beforeEach created, which is the one that
 * collides — user_login is unique and the review posts are not.
 *
 * Reviews are taken too, because they are counted. The three assignment tables and the
 * ratings table cascade from the review posts (ON DELETE CASCADE), so deleting the posts
 * empties all four.
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
}

/**
 * Lets go of the router's parallel-request lock.
 *
 * Router::isValidMutexRequest() puts a transient down, keyed by a hash of the client IP,
 * for five seconds, and refuses a second `submit-review` while it is there. Two submissions
 * arriving in the same TCP packet would otherwise both clear the duplicate check before
 * either had been written.
 *
 * Every request in a test comes from the same IP and they all arrive within the same
 * second, so a test that submits twice looks exactly like the attack. It is not one: it is
 * a person who submitted a review, and then submitted another one. Releasing the lock
 * between requests is what stands in for the time that would have passed between them.
 *
 * A test that means to exercise the mutex simply does not call this — see RouterTest.
 */
function releaseMutexLock(): void
{
    delete_transient(mutexLock());
}

/**
 * The transient Router::isValidMutexRequest() locks with, derived the same way it derives
 * it. Hashed, so it does not put a visitor's IP address in the options table.
 */
function mutexLock(): string
{
    return Str::prefix(Str::hash(Helper::clientIp(), 13), glsr()->prefix);
}

/**
 * The referer the Setup trait pinned every request to.
 */
function referer(): string
{
    return '/index.php';
}

/**
 * The sequence WP_UnitTest_Generator_Sequence provides to the factories, so
 * that generated titles, names and logins are unique within a test run.
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
 * WP_UnitTestCase snapshotted the hook globals before each test and restored them
 * after, so that a filter a test added could not leak into the next one. Without
 * it, `add_filter('site-reviews/validators', …)` in one test silently changes the
 * outcome of every test that runs after it.
 *
 * A port of WP_UnitTestCase_Base::_backup_hooks() / _restore_hooks().
 */
function hookBackup(): \ArrayObject
{
    static $backup;
    return $backup ??= new \ArrayObject([]);
}

/**
 * The hook globals, as declared by wp-includes/plugin.php:
 *
 *     WP_Hook[] $wp_filter          the callbacks, one WP_Hook object per hook
 *     int[]     $wp_actions         how many times each action fired (did_action)
 *     int[]     $wp_filters         how many times each filter applied (did_filter, 6.1+)
 *     string[]  $wp_current_filter  the hook stack
 *
 * $wp_filter is handled separately: its values are objects, so each one is
 * cloned rather than copied by reference. The rest are plain arrays. Only
 * globals that are actually set are touched.
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
 * The rest of the per-request global state WP_UnitTestCase reset between tests:
 * the logged-in user and the request superglobals.
 */
function resetRequestState(): void
{
    wp_set_current_user(0);
    $_GET = [];
    $_POST = [];
    $_REQUEST = [];
}

/**
 * The mail the suite has "sent". WordPress core's test framework swaps PHPMailer
 * for a MockPHPMailer; there is no mail transport in the test container, so
 * wp_mail() would simply return false. `pre_wp_mail` short-circuits it the same
 * way, and records what would have gone out.
 *
 * Registered once in bootstrap.php; emptied before each test by Pest.php.
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
 * The recipients of one of those emails, as a list.
 *
 * array_values() is not decoration. EmailDefaults::finalize() drops the empty entries
 * from `recipients` with Arr::removeEmptyValues(), which preserves the keys it does not
 * remove — so a list that had a blank in the middle of it comes out with a hole in it.
 * wp_mail() does not care; an assertion written as a list would.
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
 * Nothing may leave the test container over HTTP either.
 *
 * `pre_http_request` is WordPress's own short-circuit and it runs before the URL is
 * even validated, so it catches every request whatever the transport and whether or
 * not it is blocking (wp-includes/class-wp-http.php). This one is registered LAST
 * (priority 999) and only speaks when nothing else has: a test that means to make a
 * request intercepts it at the default priority with interceptHttp(), and this hands
 * back whatever that returned.
 *
 * Otherwise it is a WP_Error, and deliberately a loud one. A test that reaches the
 * network is a test that is slow, that fails when somebody's wifi does, and that may
 * be POSTing a fixture's contents to a real webhook — the notifications are one
 * wp_safe_remote_post() away from doing exactly that.
 *
 * Registered once in bootstrap.php, so it is part of the hook baseline Pest.php
 * snapshots and restores for every test.
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
 * An approved review, created through the plugin's own public API so that the
 * ratings/assigned tables are populated exactly as they are in production.
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
 * An instance whose constructor never ran — for classes whose constructor
 * has side effects the test does not want.
 */
function unconstructed(string $className): object
{
    return (new \ReflectionClass($className))->newInstanceWithoutConstructor();
}
