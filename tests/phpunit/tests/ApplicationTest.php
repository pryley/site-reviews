<?php

namespace GeminiLabs\SiteReviews\Tests;

use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class ApplicationTest extends WP_UnitTestCase
{
    public function test_path()
    {
        $this->assertEquals(
            glsr()->path(glsr()->path('tests/assets/test.svg')),
            glsr()->path('tests/assets/test.svg')
        );
    }
}
