<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Sql;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use WP_Ajax_UnitTestCase;

class SqlTest extends WP_Ajax_UnitTestCase
{
    use Sql;

    public function test_esc_fields_for_insert()
    {
        $fields = ['field1', 'field2', 'field3'];
        $result = $this->escFieldsForInsert($fields);
        $this->assertSame("(`field1`,`field2`,`field3`)", $result);
    }

    public function test_esc_values_for_insert()
    {
        $values = ['value1', 'value2', "value'3"];
        $result = $this->escValuesForInsert($values);
        $this->assertSame("('value1','value2','value\\'3')", $result);
    }

    public function test_sql_join()
    {
        $query = glsr(Query::class);
        $postId = self::factory()->post->create();
        $termId = self::factory()->term->create(['taxonomy' => glsr()->taxonomy]);
        $userId = self::factory()->user->create();
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
        $this->assertCount(4, $clauses);
        $this->assertContains($invoke($query, 'assigned_posts'), $clauses);
        $this->assertContains($invoke($query, 'assigned_terms'), $clauses);
        $this->assertContains($invoke($query, 'assigned_users'), $clauses);
        $this->assertContains($invoke($query, 'posts'), $clauses);
    }

    public function test_sql_limit()
    {
        $query = glsr(Query::class);
        $query->setArgs(['per_page' => -1]);
        $this->assertSame($query->sqlLimit(), '');
        $query->setArgs(['per_page' => 0]);
        $this->assertSame($query->sqlLimit(), '');
        $query->setArgs(['per_page' => 1]);
        $this->assertSame($query->sqlLimit(), 'LIMIT 1');
    }

    public function test_sql_offset()
    {
        $query = glsr(Query::class);
        $query->setArgs(['offset' => 3]);
        $this->assertSame($query->sqlOffset(), 'OFFSET 3');
        $query->setArgs(['page' => 2, 'per_page' => -1]);
        $this->assertSame($query->sqlOffset(), '');
        $query->setArgs(['page' => 2, 'per_page' => 0]);
        $this->assertSame($query->sqlOffset(), '');
        $query->setArgs(['page' => 1, 'per_page' => 5]);
        $this->assertSame($query->sqlOffset(), '');
        $query->setArgs(['page' => 2, 'per_page' => 5]);
        $this->assertSame($query->sqlOffset(), 'OFFSET 5');
        $query->setArgs(['page' => 2, 'per_page' => 5, 'offset' => 3]);
        $this->assertSame($query->sqlOffset(), 'OFFSET 8');
    }

    public function test_sql_order_by()
    {
        $query = glsr(Query::class);
        $expected = [
            'author' => 'ORDER BY r.is_pinned DESC, p.post_author DESC',
            'comment_count' => 'ORDER BY r.is_pinned DESC, p.comment_count DESC',
            'date' => 'ORDER BY r.is_pinned DESC, p.post_date DESC',
            'date_gmt' => 'ORDER BY r.is_pinned DESC, p.post_date_gmt DESC',
            'id' => 'ORDER BY r.is_pinned DESC, p.ID DESC',
            'menu_order' => 'ORDER BY r.is_pinned DESC, p.menu_order DESC',
            'none' => '',
            'random' => 'ORDER BY RAND()',
            'rating' => 'ORDER BY r.is_pinned DESC, r.rating DESC',
        ];
        foreach ($expected as $key => $value) {
            $query->setArgs(['orderby' => $key]);
            $this->assertSame($query->sqlOrderBy(), $value);
        }
        $enums = glsr(ReviewsDefaults::class)->enums['orderby'];
        $this->assertEmpty(
            array_filter($enums, fn ($key) => !array_key_exists($key, $expected))
        );
    }

    public function test_sql_where()
    {
        $query = glsr(Query::class);
        $this->assertSame($query->sqlWhere(), "WHERE 1=1");
        $query->setArgs([]);
        $this->assertSame($query->sqlWhere(), "WHERE 1=1 AND r.is_approved = 1");
        $query->setArgs(['status' => 'all']);
        $this->assertSame($query->sqlWhere(), "WHERE 1=1 AND p.post_status IN ('pending','publish')");
    }
}
