<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0\MigrateAvadaBuilder;
use GeminiLabs\SiteReviews\Modules\Console;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Avada's builder stores a shortcode per element in the post content, and the
 * assignment attributes used to be a pair: `assigned_posts="custom"` plus the
 * ids in `assigned_posts_custom`. This folds the pair back into one attribute.
 *
 * The stored content IS the third party's data here, so a fixture is a real
 * fixture — nothing is read back from Avada itself.
 */

beforeEach(fn () => resetPluginState());

test('the custom ids replace the custom keyword', function () {
    $post = createPost(['post_content' => avadaContent('assigned_posts="custom" assigned_posts_custom="12,13"')]);

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeTrue();

    expect(avadaPostContent($post))->toContain('assigned_posts="12,13"')
        ->not->toContain('assigned_posts_custom');
});

test('the custom ids join the ones already chosen', function () {
    $post = createPost(['post_content' => avadaContent('assigned_users="5,custom" assigned_users_custom="12"')]);

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeTrue();

    expect(avadaPostContent($post))->toContain('assigned_users="12,5"');
});

test('an element with no ids at all loses the pair without gaining anything', function () {
    $post = createPost(['post_content' => avadaContent('assigned_posts="custom" assigned_posts_custom=""')]);

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeTrue();

    expect(avadaPostContent($post))->toContain('assigned_posts=""');
});

test('content the pattern does not match is not saved again', function () {
    // The query is broad — it finds any Avada content mentioning a custom
    // attribute — so the regex decides, and a post it does not match is left
    // untouched rather than resaved.
    $content = avadaContent('assigned_posts_custom="12" assigned_posts="custom"'); // the wrong way round
    $post = createPost(['post_content' => $content]);

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeFalse();

    expect(avadaPostContent($post))->toBe($content);
});

test('a site with no avada content at all migrates nothing', function () {
    createPost(['post_content' => 'assigned_posts_custom="12"']); // no fusion container

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeFalse();
});

test('a post that cannot be saved is logged and the rest carry on', function () {
    $post = createPost(['post_content' => avadaContent('assigned_posts="custom" assigned_posts_custom="12"')]);
    add_filter('wp_insert_post_empty_content', '__return_true');

    expect(glsr(MigrateAvadaBuilder::class)->run())->toBeFalse();

    expect(glsr(Console::class)->get())->toContain("Failed to migrate Fusion Builder Post {$post}");
});

test('a site without avada is not migrated', function () {
    // tests/stubs/fusion-builder.php defines FUSION_BUILDER_VERSION when the suite boots and
    // a constant cannot be undefined, so the armed defined() shadow stands in for its absence.
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('defined');
    try {
        expect(glsr(MigrateAvadaBuilder::class)->run())->toBeFalse();
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
});

function avadaContent(string $attributes): string
{
    return "[fusion_builder_container][fusion_builder_row][site_reviews {$attributes}][/fusion_builder_row][/fusion_builder_container]";
}

function avadaPostContent(int $postId): string
{
    clean_post_cache($postId);
    return get_post($postId)->post_content;
}
