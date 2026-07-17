<?php

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The custom-table machinery. Two things are deliberately faked here:
 *
 *   - SQLite. wp-env is MySQL, so the sqlite branches are driven by setting the
 *     public $engine property (Tables) — what wp-sqlite-db sites hit for real.
 *   - DDL. Anything that would really ALTER a shared table goes through a fake
 *     Database whose dbQuery() reports success without running SQL. The per-test
 *     transaction cannot roll DDL back (MaintenanceTest says why at length).
 */

beforeEach(fn () => resetPluginState());

function withFakeDatabase(callable $callback)
{
    $fake = new class extends Database {
        public array $queries = [];

        public function dbQuery(string $sql)
        {
            $this->queries[] = $sql;
            return true;
        }
    };
    $original = glsr(Database::class);
    glsr()->alias(Database::class, $fake);
    try {
        return $callback($fake);
    } finally {
        glsr()->alias(Database::class, $original);
    }
}

/*
 * Tables: the engine answers.
 */

test('on sqlite there are no engines, no constraints, and columns are listed instead of matched', function () {
    $tables = new Tables();
    $tables->engine = 'sqlite';

    expect($tables->isSqlite())->toBeTrue()
        ->and($tables->tableEngine('ratings'))->toBe('')
        ->and($tables->tableEngines())->toBe([]);

    // SHOW COLUMNS ... LIKE is a MySQLism; sqlite sites list the columns and search them
    expect($tables->columnExists('ratings', 'rating'))->toBeTrue()
        ->and($tables->columnExists('ratings', 'no_such_column'))->toBeFalse();

    $tables->dropForeignConstraints(); // returns before touching INFORMATION_SCHEMA
    expect(glsr(Tables::class)->tableExists('ratings'))->toBeTrue(); // and nothing was dropped
});

test('the engine of a table that does not exist is a logged warning, not a guess', function () {
    expect(glsr(Tables::class)->tableEngine('glsr_no_such_table_xyz'))->toBe('');
});

test('an uncached engine is read from INFORMATION_SCHEMA once, then from the option', function () {
    $tablename = glsr(Tables::class)->table('ratings');
    $option = sprintf('%sengine_%s', glsr()->prefix, $tablename);
    delete_option($option);

    expect(glsr(Tables::class)->tableEngine('ratings'))->toBe('innodb') // queried, lowercased...
        ->and(get_option($option))->toBe('innodb'); // ...and cached for next time
});

/*
 * AbstractTable: the constraint machinery, against a fake Database.
 */

test('dropping the assignment constraints issues one DROP FOREIGN KEY per real constraint', function () {
    // The constraints exist for real in wp-env, so foreignConstraintExists() answers from
    // INFORMATION_SCHEMA; only the ALTER itself is faked.
    withFakeDatabase(function ($fake) {
        glsr(TableAssignedPosts::class)->dropForeignConstraints();
        glsr(TableAssignedTerms::class)->dropForeignConstraints();
        glsr(TableAssignedUsers::class)->dropForeignConstraints();

        expect($fake->queries)->toHaveCount(6); // rating_id + post/term/user id, per table
        foreach ($fake->queries as $sql) {
            expect($sql)->toContain('DROP FOREIGN KEY');
        }
    });
});

test('a constraint is only added or dropped when its preconditions hold', function () {
    $table = glsr(TableAssignedPosts::class);

    // dropping a constraint that does not exist is a no-op
    expect($table->dropForeignConstraint('no_such_column', $table->table('ratings')))->toBeFalse();

    // a constraint can never reference a table that is missing or not InnoDB
    expect($table->foreignConstraintExists('anything', 'glsr_no_such_table_xyz'))->toBeFalse();

    $fakeTables = new class extends Tables {
        public function isInnodb(string $table): bool
        {
            return false; // stand in for a MyISAM wp_posts
        }
    };
    $originalTables = glsr(Tables::class);
    glsr()->alias(Tables::class, $fakeTables);
    try {
        expect($table->addForeignConstraint('post_id', $table->table('posts'), 'ID'))->toBeFalse()
            ->and($table->foreignConstraintExists('anything', $table->table('posts')))->toBeFalse();
    } finally {
        glsr()->alias(Tables::class, $originalTables);
    }
});

test('a table object knows its own name, prefixed and not', function () {
    $table = glsr(TableAssignedPosts::class);

    expect($table->name())->toBe('assigned_posts')
        ->and($table->name(true))->toBe(glsr()->prefix.'assigned_posts');
});

test('emptying a table that does not exist is refused', function () {
    $missing = new class extends TableTmp {
        public string $name = 'no_such_table_xyz';
    };

    expect($missing->exists())->toBeFalse()
        ->and($missing->empty())->toBeFalse()
        ->and($missing->drop())->toBeFalse();
});

test('the tmp table has no constraints and no invalid rows to remove', function () {
    // Its three no-op methods are the CONTRACT: import must never cascade or validate.
    withFakeDatabase(function ($fake) {
        glsr(TableTmp::class)->addForeignConstraints();
        glsr(TableTmp::class)->dropForeignConstraints();
        glsr(TableTmp::class)->removeInvalidRows();

        expect($fake->queries)->toBe([]);
    });
});

// NOTE (ceiling): AbstractTable::foreignConstraint()'s multisite suffix (line 99) is gated on
// is_multisite(), which is a constant in wp-env; the branch is untestable here and left uncovered.
