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
        $this->assertArrayHasKey('forms', $options['settings']);
        $this->assertArrayHasKey('reviews', $options['settings']);
        $this->assertArrayHasKey('schema', $options['settings']);
    }

    public function test_database_key()
    {
        $this->assertEquals(OptionManager::databaseKey(), 'site_reviews_v6');
    }

    public function test_database_keys()
    {
        $this->assertEquals(OptionManager::databaseKeys(), [
            6 => "site_reviews_v6",
            5 => "site_reviews_v5",
            4 => "site_reviews_v4",
            3 => "site_reviews_v3",
            2 => "geminilabs_site_reviews-v2",
            1 => "geminilabs_site_reviews_settings",
        ]);
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

    public function test_get_array()
    {
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $this->assertEquals($options->getArray($path), ['no']);
        $this->assertEquals($options->getArray($path, ['yes']), ['no']);
        $this->assertEquals($options->getArray('xyz', ['fallback']), ['fallback']);
    }

    public function test_get_bool()
    {
        $options = glsr(OptionManager::class);
        $path = 'settings.general.require.approval';
        $this->assertFalse($options->getBool($path));
        $this->assertFalse($options->getBool($path, 'yes'));
        $options->set($path, 'yes');
        $this->assertTrue($options->getBool($path));
    }

    public function test_get_int()
    {
        $options = glsr(OptionManager::class);
        $path = 'settings.reviews.excerpts_length';
        $this->assertEquals($options->getInt($path), 55);
        $options->set($path, '50');
        $this->assertEquals($options->getInt($path), 50);
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

    public function test_wp()
    {
        $options = glsr(OptionManager::class);
        $this->assertEquals($options->wp('blog_charset'), 'UTF-8');
        $this->assertEquals($options->wp('blog_charset_x'), '');
        $this->assertEquals($options->wp('blog_charset_x', 'xyz'), 'xyz');
        $this->assertEquals($options->wp('blog_charset_x', 'xyz', 'bool'), false);
    }
}
