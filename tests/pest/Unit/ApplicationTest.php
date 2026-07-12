<?php

uses()->group('plugin');

test('path', function () {
    expect(glsr()->path(glsr()->path('tests/assets/test.svg')))->toEqual(glsr()->path('tests/assets/test.svg'));
});
