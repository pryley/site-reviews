<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use Pryley\WpPluginTooling\Pest;

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
 * Delete the rows a declared test committed and did not delete.
 * The user a beforeEach created is the one that matters, because user_login is unique. Reviews go
 * too, because they are counted; the three assignment tables and the ratings table cascade from
 * the review posts.
 */
function purgeCommittedRows(): void
{
    global $wpdb;
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type
    ));
    $wpdb->query(
        "DELETE r FROM {$wpdb->posts} r
         LEFT JOIN {$wpdb->posts} p ON (p.ID = r.post_parent)
         WHERE r.post_type = 'revision' AND p.ID IS NULL"
    );
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

    // The plugin's transients are options, so they commit too. They are disposable; the settings,
    // the migration record and the db version stay.
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '\\_transient\\_glsr\\_%'
            OR option_name LIKE '\\_transient\\_timeout\\_glsr\\_%'
            OR option_name LIKE '\\_site\\_transient\\_glsr\\_%'
            OR option_name LIKE '\\_site\\_transient\\_timeout\\_glsr\\_%'
            OR option_name = 'glsr_api_transients'"
    );

    forgetTableEngines();

    // A declared test can leave a migration pending, and the next test to reach runMigrations()
    // would run it as DDL without declaring the commit. The schema is current, so every migration
    // is marked run. Raw SQL: the object cache still holds the old copy until the flush after the purge.
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
 * Delete the cached table engines, which Tables::tableEngine() writes again on demand.
 */
function forgetTableEngines(): void
{
    global $wpdb;
    $options = $wpdb->get_col($wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", glsr()->prefix.'engine\\_%'
    ));
    array_map('delete_option', $options);
}

/**
 * Runs a code path that streams a download — headers, raw bytes, then glsr_exit() — and
 * returns the bytes it wrote. The header() calls warn in a CLI whose output has started
 * (headers_sent() is true under the test runner), so the warnings are silenced for the
 * duration: they are the environment's, not the code's. Termination is REQUIRED: a path
 * that returns without reaching glsr_exit() fails, because a live download that did so
 * would corrupt its payload with whatever the page renders next.
 */
function capturesDownload(callable $callback): string
{
    $level = ob_get_level();
    set_error_handler(fn () => true);
    ob_start();
    try {
        $callback();
    } catch (GlsrExitException $e) {
        return ob_get_clean();
    } finally {
        restore_error_handler();
        while (ob_get_level() > $level) { // unwind after a failure, not the return above
            ob_end_clean();
        }
    }
    throw new \PHPUnit\Framework\AssertionFailedError(
        'Expected the download to end the request with glsr_exit(), and it did not.'
    );
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
    // A failure a test armed and did not disarm (see failable-functions.php).
    disarmFailingFunctions();

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
    glsr()->bind(\GeminiLabs\SiteReviews\Licensing\License::class, \GeminiLabs\SiteReviews\Licensing\License::class, true);

    // The Application's whole STORAGE, back to a freshly booted request. See snapshotStorage().
    restoreStorage();

    // Application::$settings is the settings CONFIG (fields, not values), memoised per request;
    // Application::license() appends a licence field per licensed addon, so a test registering one
    // would leave that field for every test after. $defaults is built from $settings and memoised
    // beside it. Empty both so settings() rebuilds from config/settings.php.
    foreach (['defaults', 'settings'] as $memo) {
        (new \ReflectionProperty(glsr(), $memo))->setValue(glsr(), []);
    }

    // Whip keeps its custom headers in a STATIC array shared by every instance, so a proxy header
    // one test adds would be read by every client IP lookup after it.
    $whipHeaders = new \ReflectionProperty(\GeminiLabs\Vectorface\Whip\Whip::class, 'headers');
    $whipHeaders->setValue(null, array_replace($whipHeaders->getValue(), [
        \GeminiLabs\Vectorface\Whip\Whip::CUSTOM_HEADERS => [],
    ]));

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
 * A REST request driven through rest_do_request() — the same path a real HTTP request
 * takes, permission callbacks and all. The caller boots its own WP_REST_Server first
 * (see RestApiTest's beforeEach).
 */
function restRequest(string $method, string $route, array $params = []): \WP_REST_Response
{
    $request = new \WP_REST_Request($method, $route);
    foreach ($params as $key => $value) {
        $request->set_param($key, $value);
    }
    return rest_do_request($request);
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
 * Removes addons from the Application's registry. The registry lives on the singleton and no
 * teardown clears it, so a test that registers an addon fixture must take it out again — under
 * `make test:random` the leak reaches tests that assert an unregistered addon is refused.
 */
function unregisterAddons(string ...$addonIds): void
{
    $property = new \ReflectionProperty(glsr(), 'addons');
    $property->setAccessible(true);
    $property->setValue(glsr(), array_diff_key($property->getValue(glsr()), array_flip($addonIds)));
}

/**
 * Runs the callback with $fake standing in for $abstract in the container, then puts the
 * container back the way it was.
 *
 * "Back the way it was" is the point. alias() writes the instance registry, and resolve()
 * hands a registered instance back on every later call. A class the plugin never binds as
 * shared (Query, for one) has NO entry there: it is constructed fresh on each glsr() call.
 * Restoring such a class by aliasing "the original" back does not restore anything; it pins
 * one instance for the rest of the process, and per-call state (Query::$args) starts leaking
 * between tests that never touched the fake. So the prior entry is snapshotted before the
 * swap, and the restore either puts it back or removes the key.
 */
function swapInstance(string $abstract, object $fake, callable $callback)
{
    $property = new \ReflectionProperty(glsr(), 'instances');
    $property->setAccessible(true);
    $instances = $property->getValue(glsr());
    $hadInstance = array_key_exists($abstract, $instances);
    $original = $instances[$abstract] ?? null;
    glsr()->alias($abstract, $fake);
    try {
        return $callback($fake);
    } finally {
        $instances = $property->getValue(glsr());
        if ($hadInstance) {
            $instances[$abstract] = $original;
        } else {
            unset($instances[$abstract]);
        }
        $property->setValue(glsr(), $instances);
    }
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

/**
 * Empties the plugin's own queue, whose rows the two queue suites count.
 */
function emptyQueue(): void
{
    foreach (as_get_scheduled_actions(['group' => glsr()->id, 'per_page' => -1, 'status' => ''], 'ids') as $id) {
        \ActionScheduler::store()->delete_action((string) $id);
    }
}

/**
 * The licence/update server, answering each edd_action differently — which a single canned
 * response cannot do, and activation needs: the check says "inactive", and the activation that
 * follows it says "valid". The captured real payloads live in tests/pest/fixtures/updater/.
 *
 * @param array<string, array> $responses keyed by edd_action
 *
 * @return \ArrayObject<int, string> every action that was asked for, in order
 */
function licenseServer(array $responses): \ArrayObject
{
    $asked = new \ArrayObject();
    add_filter('pre_http_request', function ($pre, $args, $url) use ($responses, $asked) {
        $action = (string) ($args['body']['edd_action'] ?? '');
        $asked->append($action);

        return [
            'body' => (string) wp_json_encode($responses[$action] ?? []),
            'cookies' => [],
            'filename' => null,
            'headers' => [],
            'http_response' => null,
            'response' => ['code' => 200, 'message' => 'OK'],
        ];
    }, 10, 3);

    return $asked;
}

/**
 * Runs the callback with $fake standing in for the plugin's Database, and puts the
 * container back afterwards. See swapInstance() for why the restore is not an alias.
 */
function withDatabase(\GeminiLabs\SiteReviews\Database $fake, callable $callback)
{
    return swapInstance(\GeminiLabs\SiteReviews\Database::class, $fake, $callback);
}

/**
 * A Database whose write methods report success without running any SQL, for the code paths
 * that would really ALTER or DROP a shared table. The per-test transaction cannot roll DDL
 * back (MySQL commits implicitly on it), so a test that ran the real thing would either leak
 * or have to declare commitsTransaction() and repair by hand.
 *
 * The reads are left alone: they are answered by the real database unless the fake is given
 * something to answer with (see withDatabaseAnswering()).
 */
function withDdlFreeDatabase(callable $callback)
{
    return withDatabase(new class extends \GeminiLabs\SiteReviews\Database {
        public array $queries = [];

        public function dbQuery(string $sql)
        {
            $this->queries[] = $sql;
            return true;
        }

        public function dbSafeQuery(string $sql)
        {
            $this->queries[] = $sql;
            return 1;
        }
    }, $callback);
}

/**
 * A DDL-free Database that also answers the reads from canned values, and can be told to
 * FAIL its writes — the branch a migration takes when the ALTER it needs is refused, which
 * is otherwise reachable only on a database the suite cannot produce.
 *
 * @param array<string, mixed> $answers keyed by method name: dbGetCol, dbGetResults, dbGetRow
 */
function withDatabaseAnswering(array $answers, callable $callback, bool $writesFail = false)
{
    $fake = new class extends \GeminiLabs\SiteReviews\Database {
        public array $answers = [];
        public array $queries = [];
        public bool $writesFail = false;

        public function dbGetCol(string $sql): array
        {
            return $this->answers['dbGetCol'] ?? parent::dbGetCol($sql);
        }

        public function dbGetResults(string $sql, string $output = 'OBJECT')
        {
            return $this->answers['dbGetResults'] ?? parent::dbGetResults($sql, $output);
        }

        public function dbGetRow(string $sql, string $output)
        {
            return $this->answers['dbGetRow'] ?? parent::dbGetRow($sql, $output);
        }

        public function dbQuery(string $sql)
        {
            $this->queries[] = $sql;
            return !$this->writesFail;
        }

        public function dbSafeQuery(string $sql)
        {
            $this->queries[] = $sql;
            return $this->writesFail ? false : 1;
        }
    };
    $fake->answers = $answers;
    $fake->writesFail = $writesFail;

    return withDatabase($fake, $callback);
}

/**
 * Runs the callback with a Tables that answers the questions the migrations ask it: whether a
 * column is already there, whether the site is on SQLite, whether a table is InnoDB. Anything
 * not answered is left to the real one.
 *
 * The migrations branch on all three, and none of the three can be arranged in wp-env: the
 * columns and indexes ARE there (the schema is current), the database is MySQL, and every
 * table is InnoDB.
 *
 * @param array{columnExists?: bool|\Closure, isInnodb?: bool|\Closure, isSqlite?: bool|\Closure} $answers
 */
function withTablesAnswering(array $answers, callable $callback)
{
    $fake = new class extends \GeminiLabs\SiteReviews\Database\Tables {
        public array $answers = [];

        public function columnExists(string $table, string $column): bool
        {
            return $this->answer('columnExists', fn () => parent::columnExists($table, $column));
        }

        public function isInnodb(string $table): bool
        {
            return $this->answer('isInnodb', fn () => parent::isInnodb($table));
        }

        public function isSqlite(): bool
        {
            return $this->answer('isSqlite', fn () => parent::isSqlite());
        }

        /**
         * An answer may be a closure, for the migrations that ask the same question twice and
         * mean different things by it — "is the column there?" before the ALTER and after it.
         */
        protected function answer(string $key, callable $unanswered): bool
        {
            if (!array_key_exists($key, $this->answers)) {
                return $unanswered();
            }
            $answer = $this->answers[$key];

            return $answer instanceof \Closure ? (bool) $answer() : (bool) $answer;
        }
    };
    $fake->answers = $answers;
    $original = glsr(\GeminiLabs\SiteReviews\Database\Tables::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $fake);
    try {
        return $callback($fake);
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $original);
    }
}

/*
 * The harness helpers come from pryley/wp-plugin-tooling. These keep the names the tests import.
 */

function blockHttpRequests(): void
{
    Pest\blockHttpRequests();
}

function commitsTransaction(): void
{
    Pest\commitsTransaction();
}

function definesWpImporting(): void
{
    Pest\definesWpImporting();
}

function emptyMailbox(): void
{
    Pest\emptyMailbox();
}

function interceptHttp(array $response = []): \ArrayObject
{
    return Pest\interceptHttp($response);
}

function interceptMail(): void
{
    Pest\interceptMail();
}

function resetRequestState(): void
{
    Pest\resetRequestState();
}

function restoreHooks(): void
{
    Pest\restoreHooks();
}

function sentJson(\ArrayObject $requests, int $index = 0): array
{
    return Pest\sentJson($requests, $index);
}

function sentMail(): array
{
    return Pest\sentMail();
}

function sentTo(int $index = 0): array
{
    return Pest\sentTo($index);
}

