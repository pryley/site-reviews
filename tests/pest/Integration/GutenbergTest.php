<?php

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The block editor integration.
 *
 * No third party here — Gutenberg is core — so this integration registers on every site, and every
 * callback is reachable through the core filter it hooks. Each test fires the real filter rather than
 * calling the controller, which also proves the hook is registered.
 *
 * The one non-filter thing is registerBlocks(), on init. That already ran at boot, so the assertion
 * is on its result: the four blocks are in WordPress's registry.
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

test('registerBlocks registers the metadata collection and all four blocks', function () {
    // Boot registered them before coverage was collecting, so the assertion above
    // proves the RESULT; this proves the method. Unregister first — a duplicate
    // registration is a _doing_it_wrong(), not an overwrite.
    $registry = WP_Block_Type_Registry::get_instance();
    $blocks = ['site-reviews/review', 'site-reviews/reviews', 'site-reviews/form', 'site-reviews/summary'];
    foreach ($blocks as $block) {
        $registry->unregister($block);
    }

    glsr(\GeminiLabs\SiteReviews\Integrations\Gutenberg\Controller::class)->registerBlocks();

    foreach ($blocks as $block) {
        expect($registry->is_registered($block))->toBeTrue();
    }
});

test('the editor loads the public assets', function () {
    glsr(\GeminiLabs\SiteReviews\Integrations\Gutenberg\Controller::class)->enqueueBlockEditorAssets();

    expect(wp_script_is(glsr()->id, 'enqueued'))->toBeTrue()
        ->and(wp_style_is(glsr()->id, 'enqueued'))->toBeTrue();
});

test('a block with no styling of its own still renders wrapped', function () {
    // do_blocks() is the production path: it stages the block-supports context
    // that render() reads through WP_Block_Supports.
    $html = do_blocks('<!-- wp:site-reviews/form /-->');

    expect($html)->toStartWith('<div')
        ->and($html)->toContain('glsr-form');
});

test('an addon block that styles nothing inherits empty classes and styles', function () {
    // Every block this plugin ships overrides blockClasses()/blockStyles(); the
    // base defaults are the contract for ADDON blocks that do not.
    $block = new class extends \GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\Block {
        public static function shortcodeClass(): string
        {
            return \GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode::class;
        }
    };
    $fn = fn () => [$this->blockClasses([]), $this->blockStyles([])];

    expect($fn->bindTo($block, \GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\Block::class)())
        ->toBe([[], []]);
});

test('the summary block turns its color and alignment attributes into classes and custom properties', function () {
    $html = do_blocks('<!-- wp:site-reviews/summary {"style_align":"left","style_bar_color":"vivid-red","style_rating_color_custom":"#ffb900"} /-->');

    expect($html)->toContain('items-justified-left')
        ->and($html)->toContain('has-bar-color')
        ->and($html)->toContain('has-rating-color')
        ->and($html)->toContain('--glsr-bar-bg:var(--wp--preset--color--vivid-red);')
        ->and($html)->toContain('--glsr-summary-star-bg:#ffb900;')
        ->and($html)->toContain('--glsr-summary-align:start;');
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
