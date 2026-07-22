<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Migrations\Migrate_6_0_0;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Role;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\withDatabaseAnswering;
use function GeminiLabs\SiteReviews\Tests\withDdlFreeDatabase;
use function GeminiLabs\SiteReviews\Tests\withTablesAnswering;

/*
 * v6.0.0, the largest of the settings migrations: everything older than v5 is
 * thrown away, two addons' stored content is rewritten, the ratings table gains
 * two columns, the term capabilities are handed out, and `submissions` becomes
 * `forms`.
 *
 * wp-env's ratings table already has both columns, so the column paths run
 * against a Tables that says it does not and a Database that swallows the ALTER
 * — the real one is DDL, which the per-test transaction cannot roll back.
 */

beforeEach(fn () => resetPluginState());

test('the settings of every version older than v5 are thrown away', function () {
    foreach ([1, 2, 3, 4] as $version) {
        update_option(OptionManager::databaseKey($version), ['settings' => []]);
    }
    update_option(OptionManager::databaseKey(5), ['settings' => ['general' => []]]);

    expect((new Migrate_6_0_0())->run())->toBeTrue();

    foreach ([1, 2, 3, 4] as $version) {
        expect(get_option(OptionManager::databaseKey($version)))->toBeFalse();
    }
    expect(get_option(OptionManager::databaseKey(5)))->toBe(['settings' => ['general' => []]]);
});

test('the review filters block is renamed in published content, with the addon installed', function () {
    $published = createPost(['post_content' => 'before <!-- wp:site-reviews/filter {"id":"x"} --> after']);
    $draft = createPost([
        'post_status' => 'draft',
        'post_content' => '<!-- wp:site-reviews/filter -->',
    ]);

    withAddons(['site-reviews-filters'], fn () => (new Migrate_6_0_0())->migrateAddonBlocks());

    expect(postContent($published))->toContain('<!-- wp:site-reviews/filters {"id":"x"} -->')
        ->and(postContent($draft))->not->toContain('site-reviews/filters'); // only published content
});

test('nothing is rewritten without the review filters addon', function () {
    $post = createPost(['post_content' => '<!-- wp:site-reviews/filter {"id":"x"} -->']);

    (new Migrate_6_0_0())->migrateAddonBlocks();

    expect(postContent($post))->not->toContain('site-reviews/filters');
});

test('the review images are put back where attachments belong, with the addon installed', function () {
    $image = createPost([
        'post_name' => 'site-reviews-image-1',
        'post_status' => 'private',
        'post_type' => 'attachment',
    ]);
    $other = createPost([
        'post_name' => 'somebody-elses-image',
        'post_status' => 'private',
        'post_type' => 'attachment',
    ]);

    withAddons(['site-reviews-images'], fn () => (new Migrate_6_0_0())->migrateAddonReviewImages());

    expect(postStatus($image))->toBe('inherit')
        ->and(postStatus($other))->toBe('private');
});

test('nothing is moved without the review images addon', function () {
    $image = createPost([
        'post_name' => 'site-reviews-image-1',
        'post_status' => 'private',
        'post_type' => 'attachment',
    ]);

    (new Migrate_6_0_0())->migrateAddonReviewImages();

    expect(postStatus($image))->toBe('private');
});

test('a database that already has both columns is stamped with the version', function () {
    delete_option(glsr()->prefix.'db_version');

    (new Migrate_6_0_0())->migrateDatabase();

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.2');
});

test('the missing columns are added, each after the one it belongs behind', function () {
    delete_option(glsr()->prefix.'db_version');

    withTablesAnswering(['columnExists' => false], function () {
        withDdlFreeDatabase(function ($fake) {
            (new Migrate_6_0_0())->migrateDatabase();

            expect($fake->queries)->toHaveCount(2)
                ->and($fake->queries[0])->toContain('ADD COLUMN is_verified')
                ->and($fake->queries[0])->toContain('AFTER is_pinned')
                ->and($fake->queries[1])->toContain('ADD COLUMN score')
                ->and($fake->queries[1])->toContain('AFTER terms');
        });
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBe('1.2');
});

test('on sqlite the columns are added without saying where', function () {
    // SQLite's ALTER TABLE has no AFTER clause.
    withTablesAnswering(['columnExists' => false, 'isSqlite' => true], function () {
        withDdlFreeDatabase(function ($fake) {
            (new Migrate_6_0_0())->migrateDatabase();

            expect(implode(' ', $fake->queries))->not->toContain('AFTER');
        });
    });
});

test('a refused ALTER is logged and the database version is not stamped', function () {
    delete_option(glsr()->prefix.'db_version');

    withTablesAnswering(['columnExists' => false], function () {
        withDatabaseAnswering([], function () {
            (new Migrate_6_0_0())->migrateDatabase();
        }, $writesFail = true);
    });

    expect(get_option(glsr()->prefix.'db_version'))->toBeFalse()
        ->and(glsr(Console::class)->get())
        ->toContain('the [is_verified] column was not added')
        ->toContain('the [score] column was not added');
});

test('the term capabilities follow the ones wordpress already grants', function () {
    // The four roles the plugin names get everything from resetAll(); this pass
    // is about the roles it does NOT name — a membership plugin's, a site's own
    // — which are handed the term capabilities that match what they can already
    // do with posts and categories.
    add_role('glsr_test_writer', 'Writer', ['edit_posts' => true]);
    add_role('glsr_test_curator', 'Curator', ['manage_categories' => true]);
    $assign = glsr(Role::class)->capability('assign_terms');
    $manage = glsr(Role::class)->capability('manage_terms');
    try {
        (new Migrate_6_0_0())->migrateRoles();

        expect(get_role('glsr_test_writer')->has_cap($assign))->toBeTrue()
            ->and(get_role('glsr_test_writer')->has_cap($manage))->toBeFalse()
            ->and(get_role('glsr_test_curator')->has_cap($manage))->toBeTrue()
            ->and(get_role('glsr_test_curator')->has_cap($assign))->toBeFalse();
    } finally {
        remove_role('glsr_test_writer');
        remove_role('glsr_test_curator');
    }
});

test('a site with no v5 settings gains no v6 settings', function () {
    (new Migrate_6_0_0())->migrateSettings();

    expect(get_option(OptionManager::databaseKey(6)))->toBeFalse();
});

test('the submission settings become the form settings', function () {
    update_option(OptionManager::databaseKey(5), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(6), [
        'settings' => [
            'general' => ['style' => 'bootstrap_4'],
            'submissions' => ['required' => ['rating'], 'captcha' => ['integration' => 'hcaptcha']],
        ],
    ]);

    (new Migrate_6_0_0())->migrateSettings();

    $settings = get_option(OptionManager::databaseKey(6));
    expect(Arr::get($settings, 'settings.forms'))
        ->toBe(['required' => ['rating'], 'captcha' => ['integration' => 'hcaptcha']])
        ->and($settings['settings'])->not->toHaveKey('submissions')
        // the two bootstrap 4 styles became one bootstrap style
        ->and(Arr::get($settings, 'settings.general.style'))->toBe('bootstrap');
});

test('a style that was never a bootstrap 4 one is left as it is', function () {
    update_option(OptionManager::databaseKey(5), ['settings' => ['general' => []]]);
    update_option(OptionManager::databaseKey(6), [
        'settings' => ['general' => ['style' => 'twentysixteen']],
    ]);

    (new Migrate_6_0_0())->migrateSettings();

    expect(Arr::get(get_option(OptionManager::databaseKey(6)), 'settings.general.style'))
        ->toBe('twentysixteen');
});

/**
 * Runs the callback with the given addon slugs registered. The registry is a
 * property on the Application singleton that nothing in teardown clears, so it
 * is put back afterwards — the same reach AddonHookPrefixTest makes.
 */
function withAddons(array $slugs, callable $callback)
{
    $property = new ReflectionProperty(glsr(), 'addons');
    $addons = $property->getValue(glsr());
    $property->setValue(glsr(), array_merge($addons, array_fill_keys($slugs, true)));
    try {
        return $callback();
    } finally {
        $property->setValue(glsr(), $addons);
    }
}

function postContent(int $postId): string
{
    clean_post_cache($postId);
    return get_post($postId)->post_content;
}

function postStatus(int $postId): string
{
    clean_post_cache($postId);
    return get_post($postId)->post_status;
}
