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
        $this->assertTrue(in_array('INNER JOIN wptests_glsr_assigned_posts AS apt ON r.ID = apt.rating_id', $clauses));
        $this->assertTrue(in_array('INNER JOIN wptests_glsr_assigned_terms AS att ON r.ID = att.rating_id', $clauses));
        $this->assertTrue(in_array('INNER JOIN wptests_glsr_assigned_users AS aut ON r.ID = aut.rating_id', $clauses));
        $this->assertTrue(in_array('INNER JOIN wptests_posts AS p ON r.review_id = p.ID', $clauses));
    }

    public function _test_sql_order_by()
    {
        $query = glsr(Query::class);
        $query->setArgs(['orderby' => 'rand']);
        $this->assertEquals($query->sqlOrderBy(), 'ORDER BY RAND()');
        $query->setArgs(['orderby' => 'xxx']);
        $this->assertEquals($query->sqlOrderBy(), '');
    }
}
