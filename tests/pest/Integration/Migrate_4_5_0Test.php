<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_4_5_0;
use GeminiLabs\SiteReviews\Notices\AbstractNotice;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * v4 -> v4.5. Four unrelated jobs and a cleanup: the review meta keys gain the
 * underscore that makes them protected, the roles are rebuilt, the v3 settings
 * are carried onto the v4 key with the settings v4.5 added, and every trace of
 * Rebusify — a service the plugin stopped talking to — is removed.
 */

beforeEach(fn () => resetPluginState());

test('the review meta keys become protected, and nobody else\'s do', function () {
    $review = createPost(['post_type' => glsr()->post_type]);
    $post = createPost();
    foreach ([$review, $post] as $postId) {
        add_post_meta($postId, 'author', 'Jane Doe');
        add_post_meta($postId, 'rating', '5');
        add_post_meta($postId, 'unrelated', 'kept');
    }

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    // The rename is one UPDATE, so the meta cache still holds the old keys.
    wp_cache_delete($review, 'post_meta');
    wp_cache_delete($post, 'post_meta');
    expect(get_post_meta($review, '_author', true))->toBe('Jane Doe')
        ->and(get_post_meta($review, '_rating', true))->toBe('5')
        ->and(get_post_meta($review, 'author', true))->toBe('') // renamed, not copied
        ->and(get_post_meta($review, 'unrelated', true))->toBe('kept')
        ->and(get_post_meta($post, 'author', true))->toBe('Jane Doe'); // not a review
});

test('the roles are rebuilt', function () {
    get_role('administrator')->remove_cap(glsr(Role::class)->capability('edit_posts'));
    expect(get_role('administrator')->has_cap(glsr(Role::class)->capability('edit_posts')))->toBeFalse();

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    expect(get_role('administrator')->has_cap(glsr(Role::class)->capability('edit_posts')))->toBeTrue();
});

test('the v3 settings become the v4 settings when there are none yet', function () {
    update_option(OptionManager::databaseKey(3), [
        'settings' => ['general' => ['support' => ['polylang' => 'yes']]],
    ]);

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(4));
    expect(Arr::get($settings, 'settings.general.support.polylang'))->toBe('yes')
        // the multilingual setting that replaced the polylang one, seeded from it
        ->and(Arr::get($settings, 'settings.general.multilingual'))->toBe('polylang');
});

test('the settings v4.5 adds are seeded empty, and the ones already set are kept', function () {
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(4), [
        'settings' => [
            'general' => ['multilingual' => 'wpml'],
            'submissions' => ['limit' => 'ip_address'],
        ],
    ]);

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    $settings = get_option(OptionManager::databaseKey(4));
    expect(Arr::get($settings, 'settings.general.multilingual'))->toBe('wpml')
        ->and(Arr::get($settings, 'settings.submissions.limit'))->toBe('ip_address')
        ->and(Arr::get($settings, 'settings.reviews.name.format'))->toBe('')
        ->and(Arr::get($settings, 'settings.submissions.limit_whitelist.email'))->toBe('');
});

test('the rebusify settings are dropped', function () {
    update_option(OptionManager::databaseKey(3), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(4), [
        'settings' => [
            'general' => [
                'rebusify' => 'yes',
                'rebusify_email' => 'someone@example.org',
                'rebusify_serial' => 'ABC-123',
                'style' => 'default',
            ],
        ],
    ]);

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(4))['settings']['general'])
        ->toHaveKey('style')
        ->and(get_option(OptionManager::databaseKey(4))['settings']['general'])
        ->not->toHaveKeys(['rebusify', 'rebusify_email', 'rebusify_serial']);
});

test('a site with no v3 settings gains no v4 settings', function () {
    expect((new Migrate_4_5_0())->run())->toBeTrue();

    expect(get_option(OptionManager::databaseKey(4)))->toBeFalse();
});

test('the dismissed rebusify notice is forgotten, for every user who dismissed it', function () {
    $dismisser = createUser();
    $other = createUser();
    update_user_meta($dismisser, AbstractNotice::USER_META_KEY, ['rebusify' => time(), 'welcome' => 123]);
    update_user_meta($other, AbstractNotice::USER_META_KEY, ['welcome' => 123]);

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    expect(get_user_meta($dismisser, AbstractNotice::USER_META_KEY, true))->toBe(['welcome' => 123])
        ->and(get_user_meta($other, AbstractNotice::USER_META_KEY, true))->toBe(['welcome' => 123]);
});

test('the sessions, the rebusify option and the cloudflare cache are cleaned up', function () {
    global $wpdb;
    $wpdb->insert($wpdb->options, [
        'option_name' => '_glsr_session_abc123',
        'option_value' => 'whatever',
        'autoload' => 'no',
    ]);
    update_option('_glsr_rebusify', ['serial' => 'ABC-123']);
    set_transient(glsr()->id.'_cloudflare_ips', ['1.2.3.4']);

    expect((new Migrate_4_5_0())->run())->toBeTrue();

    expect(get_option('_glsr_session_abc123'))->toBeFalse()
        ->and(get_option('_glsr_rebusify'))->toBeFalse()
        ->and(get_transient(glsr()->id.'_cloudflare_ips'))->toBeFalse();
});
