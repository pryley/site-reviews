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
        $this->assertEquals(OptionManager::databaseKey(), 'site_reviews_v5');
    }

    public function test_delete()
    {
        glsr(OptionManager::class)->set('settings.new', 'yes');
        $this->assertEquals(glsr(OptionManager::class)->get('settings.new'), 'yes');
        glsr(OptionManager::class)->delete('settings.new');
        $this->assertEquals(glsr(OptionManager::class)->get('settings.new'), '');
    }

    public function test_get()
    {
        $path = 'settings.general.require.approval';
        $this->assertEquals(glsr(OptionManager::class)->get($path), 'no');
        $this->assertEquals(glsr(OptionManager::class)->get($path, 'yes'), 'no');
        $this->assertEquals(glsr(OptionManager::class)->get($path, 'yes', 'bool'), false);
        $this->assertEquals(glsr(OptionManager::class)->get('xyz', 'fallback'), 'fallback');
    }

    public function test_get_wp()
    {
        $this->assertEquals(glsr(OptionManager::class)->getWP('blog_charset'), 'UTF-8');
        $this->assertEquals(glsr(OptionManager::class)->getWP('blog_charsets'), '');
        $this->assertEquals(glsr(OptionManager::class)->getWP('blog_charsets', 'xyz'), 'xyz');
        $this->assertEquals(glsr(OptionManager::class)->getWP('blog_charsets', 'xyz', 'bool'), false);
    }

    public function test_is_recaptcha_enabled()
    {
        $path = 'settings.submissions.recaptcha.integration';
        $value = glsr(OptionManager::class)->get($path);
        $this->assertFalse(glsr(OptionManager::class)->isRecaptchaEnabled());
        glsr(OptionManager::class)->set($path, 'all');
        $this->assertTrue(glsr(OptionManager::class)->isRecaptchaEnabled());
        glsr(OptionManager::class)->set($path, 'guest');
        $this->assertTrue(glsr(OptionManager::class)->isRecaptchaEnabled());
        wp_set_current_user(self::factory()->user->create());
        $this->assertFalse(glsr(OptionManager::class)->isRecaptchaEnabled());
        glsr(OptionManager::class)->set($path, $value);
    }

    public function test_set()
    {
        $path = 'settings.general.require.approval';
        $value = glsr(OptionManager::class)->get($path);
        $this->assertEquals(glsr(OptionManager::class)->get($path), 'no');
        glsr(OptionManager::class)->set($path, 'yes');
        $this->assertEquals(glsr(OptionManager::class)->get($path), 'yes');
        glsr(OptionManager::class)->set($path, $value);
    }
}
