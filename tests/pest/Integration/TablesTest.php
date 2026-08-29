<?php

use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDdlFreeDatabase;

/*
 * The custom-table machinery. Two things are deliberately faked here:
 *
 *   - SQLite. wp-env is MySQL, so the sqlite branches are driven by setting the
 *     public $engine property (Tables) — what wp-sqlite-db sites hit for real.
 *   - DDL. Anything that would really ALTER a shared table goes through a fake
 *     Database whose dbQuery() reports success without running SQL (Support/
 *     helpers.php, shared with the migration tests). The per-test transaction
 *     cannot roll DDL back (MaintenanceTest says why at length).
 */

beforeEach(fn () => resetPluginState());

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
    withDdlFreeDatabase(function ($fake) {
        glsr(TableAssignedPosts::class)->dropForeignConstraints();
        glsr(TableAssignedTerms::class)->dropForeignConstraints();
        glsr(TableAssignedUsers::class)->dropForeignConstraints();
        glsr(\GeminiLabs\SiteReviews\Database\Tables\TableRatings::class)->dropForeignConstraints();
        glsr(\GeminiLabs\SiteReviews\Database\Tables\TableStats::class)->dropForeignConstraints();

        expect($fake->queries)->toHaveCount(8); // the six assignment keys + ratings + stats
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

    // A restore can drop a table while the cached engine answer survives and
    // still says innodb; the ALTER must not be attempted against a table that
    // a real query says is gone.
    $cachedInnodb = new class extends Tables {
        public function isInnodb(string $table): bool
        {
            return true; // the stale cache
        }

        public function tableExists(string $table): bool
        {
            return false; // reality
        }
    };
    glsr()->alias(Tables::class, $cachedInnodb);
    try {
        withDdlFreeDatabase(function ($fake) use ($table) {
            expect($table->addForeignConstraint('post_id', $table->table('posts'), 'ID'))->toBeFalse()
                ->and($fake->queries)->toBe([]);
        });
    } finally {
        glsr()->alias(Tables::class, $originalTables);
    }
});

test('creating the tables invalidates the cached engine answers', function () {
    // A restore can change a table's engine under the cache, and the cache
    // never expires — a wrong answer here silently skips the constraints.
    $tablename = glsr(Tables::class)->table('ratings');
    $option = sprintf('%sengine_%s', glsr()->prefix, $tablename);
    update_option($option, 'myisam');

    $tables = new class extends Tables {
        public function tables(): array
        {
            return []; // nothing to create; the invalidation is the test
        }
    };
    $tables->tables = ['ratings' => $tablename]; // tables() emptied the map too
    $tables->createTables();

    expect(get_option($option))->toBeFalse();
});

test('a constraint on another table does not count as existing', function () {
    // Constraint names are schema-global in MySQL, but existence must be
    // answered per table: a name that is only taken elsewhere means the ADD
    // will fail loudly with a duplicate-name error — the honest outcome —
    // while a schema-wide match here would skip the ADD and leave the table
    // silently unconstrained. The ratings constraint is real in wp-env; the
    // assigned_posts table does not have it.
    $ratings = glsr(\GeminiLabs\SiteReviews\Database\Tables\TableRatings::class);
    $assignedPosts = glsr(TableAssignedPosts::class);
    $constraint = $ratings->foreignConstraint('review_id'); // glsr_ratings_review_id_foreign

    expect($ratings->foreignConstraintExists($constraint, $ratings->table('posts')))->toBeTrue()
        ->and($assignedPosts->foreignConstraintExists($constraint, $ratings->table('posts')))->toBeFalse();
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
    withDdlFreeDatabase(function ($fake) {
        glsr(TableTmp::class)->addForeignConstraints();
        glsr(TableTmp::class)->dropForeignConstraints();
        glsr(TableTmp::class)->removeInvalidRows();

        expect($fake->queries)->toBe([]);
    });
});

// NOTE (ceiling): AbstractTable::foreignConstraint()'s multisite suffix (line 99) is gated on
// is_multisite(), which is a constant in wp-env; the branch is untestable here and left uncovered.

test('the unshipped fields table knows its structure and its constraints', function () {
    // TableFields is written but not yet in Tables::tables() (see the @todo there):
    // no table exists, so everything runs against the fake Database.
    withDdlFreeDatabase(function ($fake) {
        $table = glsr(\GeminiLabs\SiteReviews\Database\Tables\TableFields::class);

        expect($table->structure())->toContain('CREATE TABLE')
            ->and($table->structure())->toContain('field_name varchar(255)')
            ->and($table->structure())->toContain('field_name(191)'); // utf8mb4 index limit

        $table->addForeignConstraints(); // purges invalid rows, then adds the key
        expect(implode(' ', $fake->queries))->toContain('ADD CONSTRAINT')
            ->and(implode(' ', $fake->queries))->toContain('DELETE t');

        // with no table there is no constraint, so dropping is refused before any SQL
        $before = count($fake->queries);
        $table->dropForeignConstraints();
        expect($fake->queries)->toHaveCount($before);

        $table->removeInvalidRows();
        expect(end($fake->queries))->toContain('r.ID IS NULL');
    });
});
