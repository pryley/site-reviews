<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TestDatabase extends WP_UnitTestCase
{
    use Setup;

    public function test_database_key()
    {
        $this->assertEquals(OptionManager::databaseKey(), 'site_reviews_v5');
    }

    public function _test_delete_option()
    {
        glsr(OptionManager::class)->set('settings.new', 'yes');
        $this->assertEquals(glsr(OptionManager::class)->get('settings.new'), 'yes');
        glsr(OptionManager::class)->delete('settings.new');
        $this->assertEquals(glsr(OptionManager::class)->get('settings.new'), '');
    }

    public function _test_get_option()
    {
        glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');
        $this->assertEquals(
            glsr(OptionManager::class)->get('settings.general.require.approval'),
            'yes'
        );
        $this->assertEquals(
            glsr(OptionManager::class)->get('settings.general.require.approval', 'no'),
            'yes'
        );
    }

    public function _test_get_options()
    {
        $options = glsr(OptionManager::class)->all();
        $this->assertArrayHasKey('settings', $options);
        $this->assertArrayHasKey('version', $options);
        $this->assertArrayHasKey('version_upgraded_from', $options);
        $this->assertArrayHasKey('general', $options['settings']);
        $this->assertArrayHasKey('reviews', $options['settings']);
        $this->assertArrayHasKey('schema', $options['settings']);
        $this->assertArrayHasKey('submissions', $options['settings']);
    }

    public function _test_set_option()
    {
        glsr(OptionManager::class)->set('settings.general.require.approval', 'no');
        $this->assertEquals(
            glsr(OptionManager::class)->get('settings.general.require.approval'),
            'no'
        );
        glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');
        $this->assertEquals(
            glsr(OptionManager::class)->get('settings.general.require.approval', 'no'),
            'yes'
        );
    }

    // public function test_create_review()
    // {}

    // public function test_delete_review()
    // {}

    // public function test_get_review()
    // {}

    // public function test_get_review_count()
    // {}

    // public function test_get_review_id()
    // {}

    // public function test_get_review_ids()
    // {}

    // public function test_get_review_meta()
    // {}

    // public function test_get_reviews()
    // {}

    // public function test_get_reviews_meta()
    // {}

    // public function test_get_review_types()
    // {}

    // public function test_get_terms()
    // {}

    // public function test_normalize_meta()
    // {}

    // public function test_normalize_meta_key()
    // {}

    // public function test_normalize_terms()
    // {}

    // public function test_revert_review()
    // {}

    // public function test_set_defaults()
    // {}

    // public function test_set_terms()
    // {}
}
