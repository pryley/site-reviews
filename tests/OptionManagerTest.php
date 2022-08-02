<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class OptionManagerTest extends WP_UnitTestCase
{
    use Setup;

    public function test_all()
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

    public function test_database_key()
    {
        $this->assertEquals(OptionManager::databaseKey(), 'site_reviews_v6');
    }

    public function test_delete()
    {
        $options = glsr(OptionManager::class);
        $options->set('settings.new', 'yes');
        $this->assertEquals($options->get('settings.new'), 'yes');
        $options->delete('settings.new');
        $this->assertEquals($options->get('settings.new'), '');
    }

    public function test_get()
    {
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $this->assertEquals($options->get($path), 'no');
        $this->assertEquals($options->get($path, 'yes'), 'no');
        $this->assertEquals($options->get($path, 'yes', 'bool'), false);
        $this->assertEquals($options->get('xyz', 'fallback'), 'fallback');
    }

    public function test_get_wp()
    {
        $options = glsr(OptionManager::class);
        $this->assertEquals($options->getWP('blog_charset'), 'UTF-8');
        $this->assertEquals($options->getWP('blog_charsets'), '');
        $this->assertEquals($options->getWP('blog_charsets', 'xyz'), 'xyz');
        $this->assertEquals($options->getWP('blog_charsets', 'xyz', 'bool'), false);
    }

    public function test_set()
    {
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $value = $options->get($path);
        $this->assertEquals($options->get($path), 'no');
        $options->set($path, 'yes');
        $this->assertEquals($options->get($path), 'yes');
        $options->set($path, $value);
    }
}
