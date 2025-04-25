<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helpers\Svg;

/**
 * Test case for the Plugin.
 *
 * @group plugin
 */
class SvgTest extends \WP_UnitTestCase
{
    public function testContents()
    {
        $this->assertEquals(Svg::contents('xxx'), '');
        $this->assertEquals(Svg::contents('tests/assets/test.svg.txt'), '');
        $this->assertEquals(Svg::contents(glsr()->path('tests/assets/test.svg.txt')), '');
        $this->assertEquals(Svg::contents(glsr()->path('tests/assets/test.svg')),
            '<svg xmlns="http://www.w3.org/2000/svg"></svg>'
        );
        $this->assertEquals(Svg::contents('tests/assets/test.svg'),
            '<svg xmlns="http://www.w3.org/2000/svg"></svg>'
        );
    }

    public function testEncoded()
    {
        $this->assertEquals(Svg::encoded('xxx'), '');
        $this->assertEquals(Svg::encoded('tests/assets/test.svg.txt'), '');
        $this->assertEquals(Svg::encoded(glsr()->path('tests/assets/test.svg.txt')), '');
        $this->assertEquals(Svg::encoded(glsr()->path('tests/assets/test.svg')),
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjwvc3ZnPg=='
        );
        $this->assertEquals(Svg::encoded('tests/assets/test.svg'),
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjwvc3ZnPg=='
        );
    }

    public function testFilePath()
    {
        $this->assertEquals(Svg::filePath('xxx'), '');
        $this->assertEquals(Svg::filePath('tests/assets/test.svg.txt'), '');
        $this->assertEquals(Svg::filePath(glsr()->path('tests/assets/test.svg.txt')), '');
        $this->assertEquals(Svg::filePath(glsr()->path('tests/assets/test.svg')),
            glsr()->path('tests/assets/test.svg')
        );
        $this->assertEquals(Svg::filePath('tests/assets/test.svg'),
            glsr()->path('tests/assets/test.svg')
        );
    }

    public function testGet()
    {
        $this->assertEquals(Svg::get('xxx'), '');
        $this->assertEquals(Svg::get('tests/assets/test.svg.txt'), '');
        $this->assertEquals(Svg::get(glsr()->path('tests/assets/test.svg.txt')), '');
        $this->assertEquals(Svg::get(glsr()->path('tests/assets/test.svg')),
            '<svg style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg"></svg>'
        );
        $this->assertEquals(Svg::get('tests/assets/test.svg'),
            '<svg style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg"></svg>'
        );
        $this->assertEquals(
            Svg::get('tests/assets/test.svg', [
                'fill' => 'currentColor',
                'height' => 20,
                'style' => 'color: red;',
                'width' => 20,
            ]),
            '<svg fill="currentColor" height="20" style="pointer-events: none; color: red;" width="20" xmlns="http://www.w3.org/2000/svg"></svg>'
        );
    }

    public function testUrl()
    {
        $this->assertEquals(Svg::url('xxx'), '');
        $this->assertEquals(Svg::url('tests/assets/test.svg.txt'), '');
        $this->assertEquals(Svg::url(glsr()->path('tests/assets/test.svg.txt')), '');
        $this->assertEquals(Svg::url(glsr()->path('tests/assets/test.svg')),
            glsr()->url('tests/assets/test.svg')
        );
        $this->assertEquals(Svg::url('tests/assets/test.svg'),
            glsr()->url('tests/assets/test.svg')
        );
    }
}
