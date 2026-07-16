<?php

uses()->group('plugin');

test('path', function () {
    // The exact base first — idempotence alone would pass with a wrong base, since a wrong
    // base is idempotent too.
    expect(glsr()->path('tests/assets/test.svg'))
        ->toBe(plugin_dir_path(glsr()->file).'tests/assets/test.svg');
    // And a path already inside the plugin is not prefixed twice.
    expect(glsr()->path(glsr()->path('tests/assets/test.svg')))->toBe(glsr()->path('tests/assets/test.svg'));
});
