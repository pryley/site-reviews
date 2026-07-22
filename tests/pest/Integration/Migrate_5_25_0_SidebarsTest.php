<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateSidebars;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The widgets were registered under `site-reviews_*` and are re-registered under
 * `glsr_*`, so everything that names a widget has to be rewritten: the active
 * sidebars, the copy of them each theme keeps in its theme mods, and the widget
 * settings themselves — whose own keys changed with the shortcode attributes.
 * The metabox order each user has saved is reset while we are in there.
 *
 * The sidebars_widgets fixtures carry WordPress's own array_version member,
 * because the real option always does and walking it as a list of widgets was
 * a TypeError.
 */

beforeEach(fn () => resetPluginState());

test('the widgets in the active sidebars are renamed', function () {
    update_option('sidebars_widgets', [
        'wp_inactive_widgets' => [],
        'sidebar-1' => ['site-reviews_site-reviews-2', 'text-4'],
        'array_version' => 3, // not a sidebar, and not an array
    ]);

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    expect(get_option('sidebars_widgets'))->toBe([
        'wp_inactive_widgets' => [],
        'sidebar-1' => ['glsr_site-reviews-2', 'text-4'],
        'array_version' => 3,
    ]);
});

test('sidebars with nobody else\'s widgets in them are not rewritten', function () {
    $sidebars = ['sidebar-1' => ['text-4'], 'array_version' => 3];
    update_option('sidebars_widgets', $sidebars);

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    expect(get_option('sidebars_widgets'))->toBe($sidebars);
});

test('the copy of the sidebars each theme keeps is renamed too', function () {
    // A theme mod remembers the widgets that were in its sidebars the last time
    // it was active, so a rename that skipped it would come back on a switch.
    update_option('theme_mods_twentysixteen', [
        'sidebars_widgets' => [
            'time' => 1600000000,
            'data' => ['sidebar-1' => ['site-reviews_site-reviews-form-3']],
        ],
    ]);
    update_option('theme_mods_twentyfifteen', [
        'sidebars_widgets' => ['time' => 1600000000, 'data' => ['sidebar-1' => ['text-4']]],
    ]);

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    expect(get_option('theme_mods_twentysixteen')['sidebars_widgets']['data'])
        ->toBe(['sidebar-1' => ['glsr_site-reviews-form-3']])
        ->and(get_option('theme_mods_twentyfifteen')['sidebars_widgets']['data'])
        ->toBe(['sidebar-1' => ['text-4']]); // untouched
});

test('the widget settings move to the new option name, with the new attribute names', function () {
    update_option('widget_site-reviews_site-reviews', [
        2 => [
            'assign_to' => '123',
            'category' => '45',
            'per_page' => 10,
            'title' => 'Our reviews',
            'user' => '6',
        ],
        '_multiwidget' => 1, // not a widget instance, and not an array
    ]);

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    // A renamed key is unset and appended, so the ones that keep their name
    // come first; only the names matter to the widget that reads them back.
    expect(get_option('widget_glsr_site-reviews'))->toBe([
        2 => [
            'title' => 'Our reviews',
            'assigned_posts' => '123',
            'assigned_terms' => '45',
            'display' => 10,
            'assigned_users' => '6',
        ],
        '_multiwidget' => 1,
    ])->and(get_option('widget_site-reviews_site-reviews'))->toBeFalse();
});

test('widget settings that are not an array are moved as they are', function () {
    update_option('widget_site-reviews_site-reviews-summary', 'not-an-array');

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    expect(get_option('widget_glsr_site-reviews-summary'))->toBe('not-an-array');
});

test('the saved metabox order is reset for every user who has one', function () {
    $postType = glsr()->post_type;
    $metaKey = "meta-box-order_{$postType}";
    $editor = createUser();
    $other = createUser();
    update_user_meta($editor, $metaKey, ['side' => 'stale', 'normal' => 'stale']);

    expect(glsr(MigrateSidebars::class)->run())->toBeTrue();

    expect(get_user_meta($editor, $metaKey, true))->toBe([
        'side' => "submitdiv,{$postType}-categorydiv,{$postType}-postsdiv,{$postType}-usersdiv,{$postType}-authordiv",
        'normal' => "{$postType}-responsediv,{$postType}-detailsdiv",
        'advanced' => '',
    ])->and(get_user_meta($other, $metaKey, true))->toBe(''); // nothing saved, nothing written
});
