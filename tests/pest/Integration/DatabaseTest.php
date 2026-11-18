<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The $wpdb wrapper: engine-specific transaction phrasing, IGNORE-flavoured inserts, error
 * logging, and the migration/version checks.
 *
 * wp-env runs MySQL on InnoDB, so the SQLite and MyISAM branches are driven through a fake
 * Tables swapped into the container. SQLite has two: the driver SQLite Database Integration 3
 * removed takes SQLite's syntax, the current one takes MySQL's. What the old driver's SQL does
 * on MySQL was PROBED, not assumed: "BEGIN TRANSACTION" and "INSERT OR IGNORE" are MySQL syntax errors (logged,
 * harmless), while "COMMIT" and "SET autocommit = 1" really commit — so the finish branches
 * live in a test that declares commitsTransaction().
 */

beforeEach(fn () => resetPluginState());

function suppressingDbErrors(callable $callback)
{
    // These tests run SQL that MUST fail; without this, $wpdb prints each failure to the
    // console. Suppression short-circuits print_error() only — $wpdb->last_error is set
    // before it, so the plugin's logErrors() path still sees every error.
    $suppressed = $GLOBALS['wpdb']->suppress_errors();
    try {
        return $callback();
    } finally {
        $GLOBALS['wpdb']->suppress_errors($suppressed);
    }
}

function withFakeTables(string $engine, callable $callback)
{
    $fake = new class($engine) extends Tables {
        private string $fakeEngine;

        public function __construct(string $engine)
        {
            parent::__construct();
            $this->fakeEngine = $engine;
        }

        public function isSqlite(): bool
        {
            return in_array($this->fakeEngine, ['sqlite', 'sqlite-legacy'], true);
        }

        public function isSqliteLegacy(): bool
        {
            return 'sqlite-legacy' === $this->fakeEngine;
        }

        public function isInnodb(string $table): bool
        {
            return 'innodb' === $this->fakeEngine;
        }

        public function isMyisam(string $table): bool
        {
            return 'myisam' === $this->fakeEngine;
        }
    };
    $original = glsr(Tables::class);
    glsr()->alias(Tables::class, $fake);
    try {
        return $callback();
    } finally {
        glsr()->alias(Tables::class, $original);
    }
}

test('a failed query is logged and returned as failure, not silence', function () {
    suppressingDbErrors(function () {
        expect(glsr(Database::class)->dbQuery('SELECT * FROM glsr_no_such_table_xyz'))->toBeFalse();
    });
});

test('the sqlite phrasing degrades on mysql to logged errors, not data changes', function () {
    // The branches themselves belong to SQLite sites on the driver that SQLite Database
    // Integration 3 removed; here they prove only that the wrapper takes them. The probed
    // MySQL responses are syntax errors.
    suppressingDbErrors(fn () => withFakeTables('sqlite-legacy', function () {
        $db = glsr(Database::class);
        $db->beginTransaction('ratings'); // BEGIN TRANSACTION; -> syntax error, logged
        expect($db->insert('ratings', ['review_id' => 999999001]))->toBeFalse(); // INSERT OR IGNORE
        expect($db->insertBulk('ratings', [['review_id' => 999999001]], ['review_id']))->toBeFalse();
        expect($db->dbSafeQuery('SELECT 1'))->toEqual(1); // sqlite skips the fk-checks dance
    }));
});

test('sqlite on the current driver is sent the statements mysql is', function () {
    // SQLite Database Integration 3 parses MySQL and nothing else. The statements are read from
    // wpdb's query filter and swapped for a harmless one: START TRANSACTION would commit the test's.
    $queries = [];
    $capture = function (string $query) use (&$queries) {
        $queries[] = preg_replace('/\s+/', ' ', trim($query));
        return 'SELECT 1';
    };
    add_filter('query', $capture);
    try {
        withFakeTables('sqlite', function () {
            glsr(Database::class)->beginTransaction('ratings');
            glsr(Database::class)->insert('ratings', ['review_id' => 999999001]);
            glsr(Database::class)->insertBulk('ratings', [['review_id' => 999999001]], ['review_id']);
        });
    } finally {
        remove_filter('query', $capture);
    }

    expect($queries[0])->toBe('START TRANSACTION;')
        ->and($queries[1])->toStartWith('INSERT IGNORE INTO')
        ->and($queries[2])->toStartWith('INSERT IGNORE INTO');
});

test('an insert, a bulk insert and a meta delete send the statement they always did, with the table name quoted', function () {
    // Every name and value goes to $wpdb->prepare() as a placeholder. Each statement is compared
    // with the one the escaping helpers used to build, for a row made to trip either of them up.
    global $wpdb;
    $row = [
        'avatar' => "it's",
        'email' => 'back\\slash',
        'ip_address' => '100% satisfied, %d and %i',
        'is_approved' => true,
        'is_pinned' => false,
        'name' => null,
        'rating' => 0,
        'review_id' => 999999001,
        'score' => 1.5,
        'url' => '',
    ];
    $queries = [];
    $capture = function (string $query) use (&$queries) {
        $queries[] = preg_replace('/\s+/', ' ', trim($query));
        return 'SELECT 1';
    };
    add_filter('query', $capture);
    try {
        glsr(Database::class)->insert('ratings', $row);
        glsr(Database::class)->insertBulk('ratings', [$row, array_merge($row, ['review_id' => 999999002])], array_keys($row));
        glsr(Database::class)->deleteMeta(['_one', "_it's", '_100% s']);
        glsr(Database::class)->deleteMeta('');
    } finally {
        remove_filter('query', $capture);
    }
    $ratings = glsr(Tables::class)->table('ratings');
    $fields = glsr(Query::class)->escFieldsForInsert(array_keys($row));
    $values = fn (array $values) => $wpdb->remove_placeholder_escape(glsr(Query::class)->escValuesForInsert($values));

    expect($queries[0])->toBe("INSERT IGNORE INTO `{$ratings}` {$fields} VALUES {$values($row)}")
        ->and($queries[1])->toBe("INSERT IGNORE INTO `{$ratings}` {$fields} VALUES {$values($row)},{$values(array_merge($row, ['review_id' => 999999002]))}")
        ->and($queries[2])->toBe("DELETE FROM `{$wpdb->postmeta}` WHERE meta_key IN {$values(['_one', "_it's", '_100% s'])}")
        ->and($queries[3])->toBe("DELETE FROM `{$wpdb->postmeta}` WHERE meta_key IN ('')")
        ->and($queries)->toHaveCount(4)
        ->and(glsr(Database::class)->insert('ratings', []))->toBeFalse(); // no statement at all
});

test('a value that holds a percent sign, a quote and a backslash is stored as it was given', function () {
    global $wpdb;
    $postId = createPost(); // the ratings table only takes the ID of a post
    $name = "100% satisfied, %d and %i, it's a back\\slash";

    expect(glsr(Database::class)->insert('ratings', ['name' => $name, 'rating' => 5, 'review_id' => $postId]))->toBe(1);

    $ratings = glsr(Tables::class)->table('ratings');
    expect($wpdb->get_var($wpdb->prepare("SELECT name FROM {$ratings} WHERE review_id = %d", $postId)))->toBe($name);
});

test('finishing a transaction commits, in every engine dialect', function () {
    // COMMIT (sqlite/innodb) and SET autocommit = 1 (myisam) both commit the test's own
    // transaction — declared, and Pest.php purges what stuck. The statements are read from
    // wpdb's query filter, which every query passes through.
    commitsTransaction();
    $queries = [];
    $capture = function (string $query) use (&$queries) {
        $queries[] = $query;
        return $query;
    };
    add_filter('query', $capture);
    try {
        withFakeTables('myisam', function () {
            glsr(Database::class)->beginTransaction('ratings');
            glsr(Database::class)->finishTransaction('ratings');
        });
        withFakeTables('sqlite', fn () => glsr(Database::class)->finishTransaction('ratings'));
        withFakeTables('innodb', fn () => glsr(Database::class)->finishTransaction('ratings'));
    } finally {
        remove_filter('query', $capture);
    }

    expect($queries)->toBe(['SET autocommit = 0;', 'SET autocommit = 1;', 'COMMIT;', 'COMMIT;']);
});

test('migration is needed when published reviews have no approved ratings', function () {
    // The signature of a site restored from a posts-only backup: review posts exist, the
    // custom ratings table does not agree.
    $review = createReview();

    expect(glsr(Database::class)->isMigrationNeeded())->toBeFalse(); // table agrees

    glsr(Database::class)->update('ratings', ['is_approved' => 0], ['review_id' => $review->ID]);
    expect(glsr(Database::class)->isMigrationNeeded())->toBeTrue(); // it no longer does
});

test('terms of a taxonomy that does not exist are an empty list, logged', function () {
    expect(glsr(Database::class)->terms(['taxonomy' => 'glsr_no_such_taxonomy']))->toBe([]);
});

test('a database version from the future is reset to trigger re-migration', function () {
    update_option(glsr()->prefix.'db_version', '99.0');

    expect(glsr(Database::class)->version())->toBe('1.0');
    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.0');
    expect(glsr(Database::class)->version())->not->toBe('99.0');
});
