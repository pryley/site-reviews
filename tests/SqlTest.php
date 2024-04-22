<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\Query;
use WP_Ajax_UnitTestCase;

class SqlTest extends WP_Ajax_UnitTestCase
{
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
        $this->assertCount(4, $clauses);
        $this->assertTrue(in_array('INNER JOIN table|assigned_posts AS apt ON apt.rating_id = r.ID', $clauses));
        $this->assertTrue(in_array('INNER JOIN table|assigned_terms AS att ON att.rating_id = r.ID', $clauses));
        $this->assertTrue(in_array('INNER JOIN table|assigned_users AS aut ON aut.rating_id = r.ID', $clauses));
        $this->assertTrue(in_array('INNER JOIN table|posts AS p ON p.ID = r.review_id', $clauses));
    }

    public function _test_sql_order_by()
    {
        $query = glsr(Query::class);
        $query->setArgs(['orderby' => 'random']);
        $this->assertEquals($query->sqlOrderBy(), 'ORDER BY RAND()');
        $query->setArgs(['orderby' => 'xxx']);
        $this->assertEquals($query->sqlOrderBy(), '');
    }
}
