<?php

use GeminiLabs\SiteReviews\Helpers\Svg;

uses()->group('plugin');

test('contents', function () {
    expect(Svg::contents('xxx'))->toEqual('');
    expect(Svg::contents('tests/assets/test.svg.txt'))->toEqual('');
    expect(Svg::contents(glsr()->path('tests/assets/test.svg.txt')))->toEqual('');
    expect(Svg::contents(glsr()->path('tests/assets/test.svg')))->toEqual('<svg xmlns="http://www.w3.org/2000/svg"></svg>');
    expect(Svg::contents('tests/assets/test.svg'))->toEqual('<svg xmlns="http://www.w3.org/2000/svg"></svg>');
});

test('encoded', function () {
    expect(Svg::encoded('xxx'))->toEqual('');
    expect(Svg::encoded('tests/assets/test.svg.txt'))->toEqual('');
    expect(Svg::encoded(glsr()->path('tests/assets/test.svg.txt')))->toEqual('');
    expect(Svg::encoded(glsr()->path('tests/assets/test.svg')))->toEqual('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjwvc3ZnPg==');
    expect(Svg::encoded('tests/assets/test.svg'))->toEqual('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjwvc3ZnPg==');
});

test('file path', function () {
    expect(Svg::filePath('xxx'))->toEqual('');
    expect(Svg::filePath('tests/assets/test.svg.txt'))->toEqual('');
    expect(Svg::filePath(glsr()->path('tests/assets/test.svg.txt')))->toEqual('');
    expect(Svg::filePath(glsr()->path('tests/assets/test.svg')))->toEqual(glsr()->path('tests/assets/test.svg'));
    expect(Svg::filePath('tests/assets/test.svg'))->toEqual(glsr()->path('tests/assets/test.svg'));
});

test('get', function () {
    expect(Svg::get('xxx'))->toEqual('');
    expect(Svg::get('tests/assets/test.svg.txt'))->toEqual('');
    expect(Svg::get(glsr()->path('tests/assets/test.svg.txt')))->toEqual('');
    expect(Svg::get(glsr()->path('tests/assets/test.svg')))->toEqual('<svg style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg"></svg>');
    expect(Svg::get('tests/assets/test.svg'))->toEqual('<svg style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg"></svg>');
    expect(Svg::get('tests/assets/test.svg', [
            'fill' => 'currentColor',
            'height' => 20,
            'style' => 'color: red;',
            'width' => 20,
        ]))->toEqual('<svg fill="currentColor" height="20" style="pointer-events: none; color: red;" width="20" xmlns="http://www.w3.org/2000/svg"></svg>');
});
