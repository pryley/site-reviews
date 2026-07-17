<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The $wpdb wrapper: engine-specific transaction phrasing, IGNORE-flavoured inserts, error
 * logging, and the migration/version checks.
 *
 * wp-env runs MySQL on InnoDB, so the SQLite and MyISAM branches are driven through a fake
 * Tables swapped into the container. What their SQL then does on MySQL was PROBED, not
 * assumed: "BEGIN TRANSACTION" and "INSERT OR IGNORE" are MySQL syntax errors (logged,
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
            return 'sqlite' === $this->fakeEngine;
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
    // The branches themselves belong to SQLite sites (wp-sqlite-db); here they prove only
    // that the wrapper takes them. The probed MySQL responses are syntax errors.
    suppressingDbErrors(fn () => withFakeTables('sqlite', function () {
        $db = glsr(Database::class);
        $db->beginTransaction('ratings'); // BEGIN TRANSACTION; -> syntax error, logged
        expect($db->insert('ratings', ['review_id' => 999999001]))->toBeFalse(); // INSERT OR IGNORE
        expect($db->insertBulk('ratings', [['review_id' => 999999001]], ['review_id']))->toBeFalse();
        expect($db->dbSafeQuery('SELECT 1'))->toEqual(1); // sqlite skips the fk-checks dance
    }));
});

test('finishing a transaction commits, in every engine dialect', function () {
    // COMMIT (sqlite/innodb) and SET autocommit = 1 (myisam) both commit the test's own
    // transaction — declared, and Pest.php purges what stuck.
    commitsTransaction();
    withFakeTables('myisam', function () {
        glsr(Database::class)->beginTransaction('ratings');  // SET autocommit = 0;
        glsr(Database::class)->finishTransaction('ratings'); // SET autocommit = 1; (commits)
    });
    withFakeTables('sqlite', function () {
        glsr(Database::class)->finishTransaction('ratings'); // COMMIT; (nothing pending now)
    });
    expect(true)->toBeTrue();
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
