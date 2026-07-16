<?php

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Sql;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;

// uses() puts the Sql trait on the test case, to reach escFieldsForInsert() and
// escValuesForInsert().
uses(Sql::class);

test('esc fields for insert', function () {
    $fields = ['field1', 'field2', 'field3'];
    $result = $this->escFieldsForInsert($fields);
    expect($result)->toBe("(`field1`,`field2`,`field3`)");
});

test('esc values for insert', function () {
    $values = ['value1', 'value2', "value'3"];
    $result = $this->escValuesForInsert($values);
    expect($result)->toBe("('value1','value2','value\\'3')");
});

test('sql join', function () {
    $query = glsr(Query::class);
    $postId = createPost();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();
    $query->setArgs([
        'assigned_posts' => $postId,
        'assigned_terms' => $termId,
        'assigned_users' => $userId,
    ]);
    $clauses = array_unique(array_filter($query->clauses('join')));
    $invoke = function (Query $obj, string $key) {
        $fn = fn () => $this->join($key, 'INNER JOIN');
        return $fn->bindTo($obj, $obj)();
    };
    expect($clauses)->toHaveCount(4);
    expect($clauses)->toContain($invoke($query, 'assigned_posts'));
    expect($clauses)->toContain($invoke($query, 'assigned_terms'));
    expect($clauses)->toContain($invoke($query, 'assigned_users'));
    expect($clauses)->toContain($invoke($query, 'posts'));
});

test('sql limit', function () {
    $query = glsr(Query::class);
    $query->setArgs(['per_page' => -1]);
    expect($query->sqlLimit())->toBe('');
    $query->setArgs(['per_page' => 0]);
    expect($query->sqlLimit())->toBe('');
    $query->setArgs(['per_page' => 1]);
    expect($query->sqlLimit())->toBe('LIMIT 1');
});

test('sql offset', function () {
    $query = glsr(Query::class);
    $query->setArgs(['offset' => 3]);
    expect($query->sqlOffset())->toBe('OFFSET 3');
    $query->setArgs(['page' => 2, 'per_page' => -1]);
    expect($query->sqlOffset())->toBe('');
    $query->setArgs(['page' => 2, 'per_page' => 0]);
    expect($query->sqlOffset())->toBe('');
    $query->setArgs(['page' => 1, 'per_page' => 5]);
    expect($query->sqlOffset())->toBe('');
    $query->setArgs(['page' => 2, 'per_page' => 5]);
    expect($query->sqlOffset())->toBe('OFFSET 5');
    $query->setArgs(['page' => 2, 'per_page' => 5, 'offset' => 3]);
    expect($query->sqlOffset())->toBe('OFFSET 8');
});

test('sql order by', function () {
    $query = glsr(Query::class);
    $expected = [
        'author' => 'ORDER BY r.is_pinned DESC, p.post_author DESC',
        'comment_count' => 'ORDER BY r.is_pinned DESC, p.comment_count DESC',
        'date' => 'ORDER BY r.is_pinned DESC, p.post_date DESC',
        'date_gmt' => 'ORDER BY r.is_pinned DESC, p.post_date_gmt DESC',
        'id' => 'ORDER BY r.is_pinned DESC, p.ID DESC',
        'menu_order' => 'ORDER BY r.is_pinned DESC, p.menu_order DESC',
        'none' => '',
        // Seeded per hour so paginated random results stay
        // consistent within the hour (see Sql::sqlOrderBy).
        'random' => sprintf('ORDER BY RAND(%d)', (int) floor(time() / HOUR_IN_SECONDS)),
        'rating' => 'ORDER BY r.is_pinned DESC, r.rating DESC',
    ];
    foreach ($expected as $key => $value) {
        $query->setArgs(['orderby' => $key]);
        expect($query->sqlOrderBy())->toBe($value);
    }
    $enums = glsr(ReviewsDefaults::class)->enums['orderby'];
    expect(array_filter($enums, fn ($key) => !array_key_exists($key, $expected)))->toBeEmpty();
});

test('sql where', function () {
    $query = glsr(Query::class);
    expect($query->sqlWhere())->toBe("WHERE 1=1");
    $query->setArgs([]);
    expect($query->sqlWhere())->toBe("WHERE 1=1 AND r.is_approved = 1");
    $query->setArgs(['status' => 'all']);
    expect($query->sqlWhere())->toBe("WHERE 1=1 AND p.post_status IN ('pending','publish')");
});
