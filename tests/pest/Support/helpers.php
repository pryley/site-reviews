<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

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
 */
function resetPluginState(): void
{
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    $_SERVER['SERVER_NAME'] = '';
    $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = referer();
    $defaults = Arr::unflatten(glsr()->defaults());
    glsr(Migrate::class)->runAll();
    glsr(OptionManager::class)->replace($defaults);
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

function emptyMailbox(): void
{
    mailbox()->exchangeArray([]);
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
