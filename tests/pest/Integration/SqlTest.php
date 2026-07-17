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

test('a date given as a single day matches that calendar day', function () {
    // ReviewsDefaults::finalizeDate turns a parseable date string into year/month/day parts.
    $query = glsr(Query::class);
    $query->setArgs(['date' => '2024-06-15', 'status' => 'all']);

    expect($query->sqlWhere())->toContain(
        'AND ((YEAR(p.post_date) = 2024 AND MONTH(p.post_date) = 6 AND DAYOFMONTH(p.post_date) = 15))'
    );
});

test('a date range respects before, after, and inclusivity', function () {
    $query = glsr(Query::class);
    // sanitizeDate normalizes to a full Y-m-d H:i:s datetime
    $query->setArgs(['date' => ['before' => '2024-01-01'], 'status' => 'all']);
    expect($query->sqlWhere())->toContain("AND ((p.post_date < '2024-01-01 00:00:00'))");

    $query->setArgs(['date' => ['after' => '2023-01-01', 'before' => '2024-01-01', 'inclusive' => true], 'status' => 'all']);
    expect($query->sqlWhere())->toContain("AND ((p.post_date >= '2023-01-01 00:00:00') AND (p.post_date <= '2024-01-01 00:00:00'))");
});

test('include and exclude become IN and NOT IN on the review id', function () {
    $query = glsr(Query::class);
    $query->setArgs(['post__in' => [3, 7], 'post__not_in' => [4], 'status' => 'all']);

    $where = $query->sqlWhere();
    expect($where)->toContain('AND r.review_id IN (3,7)')
        ->and($where)->toContain('AND r.review_id NOT IN (4)');
});

test('the terms, verified and excluded-author flags each add their column test', function () {
    // user__not_in resolves each value against a real user (SanitizeUserIds) and drops the rest
    $userId = createUser();
    $query = glsr(Query::class);
    $query->setArgs(['terms' => 'true', 'verified' => 'true', 'user__not_in' => [$userId], 'status' => 'all']);

    $where = $query->sqlWhere();
    expect($where)->toContain('AND r.terms = 1')
        ->and($where)->toContain('AND r.is_verified = 1')
        ->and($where)->toContain("AND p.post_author NOT IN ({$userId})");

    // the join half of user__not_in: the authors live on the posts table
    $joins = $query->clauses('join');
    expect($joins)->toHaveKey('user__not_in')
        ->and($joins['user__not_in'])->toContain('JOIN')
        ->and($joins['user__not_in'])->toContain('AS p ON (p.ID = r.review_id)');
});

test('filtering by assigned post type joins through the posts table with the type pinned', function () {
    // assigned_posts_types passes post_type_exists, so only real types survive.
    $query = glsr(Query::class);
    $query->setArgs(['assigned_posts_types' => ['post', 'not_a_real_type'], 'status' => 'all']);

    expect($query->sqlWhere())->toContain('AND (apt.is_published = 1)');

    $joins = $query->clauses('join');
    expect($joins)->toHaveKey('assigned_posts_types')
        ->and($joins['assigned_posts_types'])->toContain('AS apt ON (apt.rating_id = r.ID)')
        ->and($joins['assigned_posts_types'])->toContain("AS pt ON (pt.ID = apt.post_id AND pt.post_type IN ('post'))")
        ->and($joins['assigned_posts_types'])->not->toContain('not_a_real_type');
});

test('an unrecognised join keyword falls back to INNER JOIN', function () {
    $query = glsr(Query::class);
    $invoke = function (Query $obj, string $join, string $keyword) {
        $fn = fn () => $this->join($join, $keyword);
        return $fn->bindTo($obj, $obj)();
    };

    expect($invoke($query, 'posts', 'BOGUS JOIN'))->toStartWith('INNER JOIN')
        ->and($invoke($query, 'posts', 'LEFT JOIN'))->toStartWith('LEFT JOIN');
});
