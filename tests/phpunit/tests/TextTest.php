<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Text;
use WP_UnitTestCase;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class TextTest extends WP_UnitTestCase
{
    public function test_initials()
    {
        $this->assertEquals(Text::initials((string) null, ' '), '');
        $this->assertEquals(Text::initials('Steve', ' '), 'S');
        $this->assertEquals(Text::initials('Steve', '.'), 'S.');
        $this->assertEquals(Text::initials('Steve', '. '), 'S.');
        $this->assertEquals(Text::initials('Steve Jobs', ' '), 'S J');
        $this->assertEquals(Text::initials('Steve Jobs', '.'), 'S.J.');
        $this->assertEquals(Text::initials('Steve Jobs', '. '), 'S. J.');
        $this->assertEquals(Text::initials('Steve Paul Jobs', ' '), 'S P J');
        $this->assertEquals(Text::initials('Steve Paul Jobs', '.'), 'S.P.J.');
        $this->assertEquals(Text::initials('Steve Paul Jobs', '. '), 'S. P. J.');
    }

    public function test_name()
    {
        $this->assertEquals(Text::name('Steve'), 'Steve');
        $this->assertEquals(Text::name('Steve Jobs'), 'Steve Jobs');
        $this->assertEquals(Text::name('Steve Paul Jobs'), 'Steve Paul Jobs');
    }

    public function test_name_first()
    {
        $this->assertEquals(Text::name('Steve', 'first'), 'Steve');
        $this->assertEquals(Text::name('Steve Jobs', 'first'), 'Steve');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'first'), 'Steve');
    }

    public function test_name_first_initial()
    {
        $this->assertEquals(Text::name('Steve', 'first_initial'), 'S');
        $this->assertEquals(Text::name('Steve Jobs', 'first_initial'), 'S Jobs');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'first_initial'), 'S Jobs');
    }

    public function test_name_first_initial_period()
    {
        $this->assertEquals(Text::name('Steve', 'first_initial', 'period'), 'S.');
        $this->assertEquals(Text::name('Steve Jobs', 'first_initial', 'period'), 'S.Jobs');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'first_initial', 'period'), 'S.Jobs');
    }

    public function test_name_first_initial_period_space()
    {
        $this->assertEquals(Text::name('Steve', 'first_initial', 'period_space'), 'S.');
        $this->assertEquals(Text::name('Steve Jobs', 'first_initial', 'period_space'), 'S. Jobs');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'first_initial', 'period_space'), 'S. Jobs');
    }

    public function test_name_last_initial()
    {
        $this->assertEquals(Text::name('Steve', 'last_initial'), 'Steve');
        $this->assertEquals(Text::name('Steve Jobs', 'last_initial'), 'Steve J');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'last_initial'), 'Steve J');
    }

    public function test_name_last_initial_period()
    {
        $this->assertEquals(Text::name('Steve', 'last_initial', 'period'), 'Steve');
        $this->assertEquals(Text::name('Steve Jobs', 'last_initial', 'period'), 'Steve J.');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'last_initial', 'period'), 'Steve J.');
    }

    public function test_name_last_initial_period_space()
    {
        $this->assertEquals(Text::name('Steve', 'last_initial', 'period_space'), 'Steve');
        $this->assertEquals(Text::name('Steve Jobs', 'last_initial', 'period_space'), 'Steve J.');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'last_initial', 'period_space'), 'Steve J.');
    }

    public function test_name_initials()
    {
        $this->assertEquals(Text::name('Steve', 'initials'), 'S');
        $this->assertEquals(Text::name('Steve Jobs', 'initials'), 'S J');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'initials'), 'S P J');
    }

    public function test_name_initials_period()
    {
        $this->assertEquals(Text::name('Steve', 'initials', 'period'), 'S.');
        $this->assertEquals(Text::name('Steve Jobs', 'initials', 'period'), 'S.J.');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'initials', 'period'), 'S.P.J.');
    }

    public function test_name_initials_period_space()
    {
        $this->assertEquals(Text::name('Steve', 'initials', 'period_space'), 'S.');
        $this->assertEquals(Text::name('Steve Jobs', 'initials', 'period_space'), 'S. J.');
        $this->assertEquals(Text::name('Steve Paul Jobs', 'initials', 'period_space'), 'S. P. J.');
    }
}
