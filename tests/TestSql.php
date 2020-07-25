<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\Query;
use WP_Ajax_UnitTestCase;

class TestSql extends WP_Ajax_UnitTestCase
{
    public function test_sql_join()
    {
        $query = glsr(Query::class);
        $query->setArgs([
            'assigned_posts' => 1,
            'assigned_terms' => 1,
            'assigned_users' => 1,
        ]);
        $clauses = array_unique($query->clauses('join'));
        $this->assertCount(3, $clauses);
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
