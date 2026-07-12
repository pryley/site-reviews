<?php

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The block editor integration.
 *
 * There is no third party here — Gutenberg is core — so this integration
 * registers on every site, and every one of its callbacks is reachable through
 * the core filter it is hooked to. Each test therefore fires the real filter
 * rather than calling the controller, which also proves the hook is registered at
 * all.
 *
 * The one thing the integration does that is not a filter is registerBlocks(), on
 * init. That already ran during the boot, so the assertion is on its result: the
 * four blocks are in WordPress's registry.
 */

beforeEach(function () {
    resetPluginState();
});

test('the four blocks are registered', function () {
    $registry = WP_Block_Type_Registry::get_instance();

    expect($registry->is_registered('site-reviews/review'))->toBeTrue()
        ->and($registry->is_registered('site-reviews/reviews'))->toBeTrue()
        ->and($registry->is_registered('site-reviews/form'))->toBeTrue()
        ->and($registry->is_registered('site-reviews/summary'))->toBeTrue();
});

test('the plugin gets its own block category', function () {
    $categories = apply_filters('block_categories_all', [
        ['slug' => 'text', 'title' => 'Text'],
    ]);

    expect($categories)->toContain([
        'slug' => glsr()->id,
        'title' => glsr()->name,
    ]);
});

test('the block editor is switched off for the review post type', function () {
    // A review is edited with the plugin's own metaboxes, not with blocks. The
    // prefix test is deliberate: the addons register post types beneath the same
    // prefix and they are all excluded.
    expect(apply_filters('use_block_editor_for_post_type', true, glsr()->post_type))->toBeFalse()
        ->and(apply_filters('use_block_editor_for_post_type', true, glsr()->post_type.'-alert'))->toBeFalse()
        ->and(apply_filters('use_block_editor_for_post_type', true, 'post'))->toBeTrue()
        ->and(apply_filters('use_block_editor_for_post_type', false, 'post'))->toBeFalse(); // it never turns it ON
});

test('no block at all is allowed inside a review', function () {
    // filterAllowedBlockTypes reads the post type off the editor context and
    // returns an empty allow-list for a review, which is what stops the block
    // inserter appearing on a screen that has no block editor.
    $review = createReview();
    $context = new WP_Block_Editor_Context(['post' => get_post($review->ID)]);

    expect(apply_filters('allowed_block_types_all', true, $context))->toBe([]);
});

test('an ordinary post keeps whatever blocks it was allowed', function () {
    $context = new WP_Block_Editor_Context(['post' => get_post(createPost())]);

    expect(apply_filters('allowed_block_types_all', true, $context))->toBeTrue();
    expect(apply_filters('allowed_block_types_all', ['core/paragraph'], $context))
        ->toBe(['core/paragraph']);
});

test('a block editor context with no post is left alone', function () {
    // The context of a widget or a site-editor screen carries no post, so
    // Arr::get() returns '' and the allow-list is passed through untouched.
    expect(apply_filters('allowed_block_types_all', true, new WP_Block_Editor_Context()))
        ->toBeTrue();
});

test('the review blocks get the classname their stylesheet expects', function () {
    // Core would generate wp-block-site-reviews-review from the block name; the
    // plugin's CSS is written against the shorter one.
    expect(apply_filters('block_default_classname', 'wp-block-site-reviews-review', 'site-reviews/review'))
        ->toBe('wp-block-site-review')
        ->and(apply_filters('block_default_classname', 'wp-block-site-reviews-reviews', 'site-reviews/reviews'))
        ->toBe('wp-block-site-reviews');
});

test('every other block keeps its generated classname', function () {
    expect(apply_filters('block_default_classname', 'wp-block-paragraph', 'core/paragraph'))
        ->toBe('wp-block-paragraph');

    // The form and summary blocks are NOT rewritten — only the two above are.
    expect(apply_filters('block_default_classname', 'wp-block-site-reviews-form', 'site-reviews/form'))
        ->toBe('wp-block-site-reviews-form');
});

test('the legacy widgets are hidden from the legacy widget block', function () {
    // The blocks above replace them, so offering both would be offering the same
    // thing twice.
    //
    // WooCommerce hides its own legacy widgets on this same filter, so the
    // assertion is on the four this plugin owns rather than on the whole list.
    $widgets = apply_filters('widget_types_to_hide_from_legacy_widget_block', ['some_other_widget']);

    expect($widgets)->toContain('some_other_widget')
        ->toContain('glsr_site-review')
        ->toContain('glsr_site-reviews')
        ->toContain('glsr_site-reviews-form')
        ->toContain('glsr_site-reviews-summary');
});
