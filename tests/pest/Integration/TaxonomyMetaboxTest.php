<?php

use GeminiLabs\SiteReviews\Metaboxes\TaxonomyMetabox;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxBuilder;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxField;

use function GeminiLabs\SiteReviews\Tests\createPostAndGet;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The categories box on the review editor, and the builder the metaboxes are drawn with.
 *
 * This box replaces the one WordPress would draw, because the review categories are a taxonomy of
 * their own and WordPress's default box would let someone create terms inline, which is not wanted.
 *
 * register() is deliberately empty: register_taxonomy() puts the box on the screen, and the class
 * exists to supply the RENDERING. What matters is the guard at the top of render() — WordPress calls
 * render callbacks with whatever post is being edited, and the same callback can be reached on a
 * non-review screen, so a box that did not check would print the category list onto someone's page editor.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen(glsr()->post_type);
});

afterEach(function () {
    set_current_screen('front');
});

function renderedTaxonomyMetabox(WP_Post $post): string
{
    ob_start();
    glsr(TaxonomyMetabox::class)->render($post);

    return (string) ob_get_clean();
}

test('the categories box is drawn for a review', function () {
    $review = createReview();

    $rendered = renderedTaxonomyMetabox(get_post($review->ID));

    expect($rendered)->not->toBeEmpty()
        ->and($rendered)->toContain(glsr()->taxonomy);
});

test('and is drawn for nothing else', function () {
    // The guard. A metabox callback is handed whatever post is on the screen, and this one would
    // otherwise print the review-category checklist into an ordinary post's editor.
    $rendered = renderedTaxonomyMetabox(createPostAndGet(['post_type' => 'post']));

    expect($rendered)->toBe('');
});

test('registering it does nothing, because register_taxonomy already did', function () {
    // It is empty on purpose, and the emptiness is load-bearing: the box is registered by
    // register_taxonomy(), and a second registration here would put a duplicate box on the screen.
    $review = createReview();
    $before = $GLOBALS['wp_meta_boxes'] ?? [];

    glsr(TaxonomyMetabox::class)->register(get_post($review->ID));

    expect($GLOBALS['wp_meta_boxes'] ?? [])->toBe($before);
});

/*
 * The builder.
 */

test('the metabox builder builds metabox fields, not ordinary ones', function () {
    // One line, and it is the line that decides how every field in every metabox is laid out — a
    // MetaboxField wraps itself in the table row markup the admin's metaboxes expect, and an
    // ordinary Field does not.
    $field = glsr(MetaboxBuilder::class)->field([
        'name' => 'a_field',
        'type' => 'text',
    ]);

    expect($field)->toBeInstanceOf(MetaboxField::class);
});
