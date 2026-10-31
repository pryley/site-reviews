<?php

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Rendered output shows its square brackets as text. Every surface here goes through the passes a
 * page can apply to it, and PROBE-RAN in the result means a shortcode ran.
 */

/**
 * @return array<string, string>
 */
function renderedSurfaces(): array
{
    return [
        'block' => do_shortcode(do_blocks('<!-- wp:site-reviews/reviews /-->')),
        'shortcode' => do_shortcode(do_shortcode('[site_reviews]')),
    ];
}

beforeEach(function () {
    resetPluginState();
    add_shortcode('pr', fn () => 'PROBE-RAN');
    add_shortcode('probe', fn () => 'PROBE-RAN');
});

afterEach(function () {
    remove_shortcode('pr');
    remove_shortcode('probe');
});

dataset('bracket forms', [
    'across lines' => ["[probe\nx=1]"],
    'after a heart' => ['<3 [probe]>'],
    'after a less-than sign' => ['< [probe]>'],
    'after a tab' => ["<\t[probe]>"],
    'as doubly encoded entities' => ['&amp;#91;&amp;#91;probe&amp;#93;&amp;#93;'],
    'as entities' => ['&#91;probe&#93;'],
    'between words' => ['x < [probe]> y'],
    'enclosing' => ['[probe]x[/probe]'],
    'escaped' => ['[[probe x=1]]'],
    'in a spaced script element' => ['< script>[probe]</script>'],
    'in a style element' => ['<style>[probe]</style>'],
    'nested' => ['[pr[probe]obe x=1]'],
    'plain' => ['[probe x=1]'],
]);

test('a title shows its brackets as text', function (string $text) {
    createReview(['title' => $text]);
    foreach (renderedSurfaces() as $surface => $html) {
        expect($html)->not->toContain('PROBE-RAN', $surface);
    }
})->with('bracket forms');

test('content shows its brackets as text', function (string $text) {
    createReview(['content' => $text]);
    foreach (renderedSurfaces() as $surface => $html) {
        expect($html)->not->toContain('PROBE-RAN', $surface);
    }
})->with('bracket forms');

test('a stored title shows its brackets as text', function (string $text) {
    global $wpdb;
    $review = createReview();
    $wpdb->update($wpdb->posts, ['post_title' => $text], ['ID' => $review->ID]);
    clean_post_cache($review->ID);
    foreach (renderedSurfaces() as $surface => $html) {
        expect($html)->not->toContain('PROBE-RAN', $surface);
    }
})->with('bracket forms');

test('a custom field shows its brackets as text', function (string $text) {
    add_filter('site-reviews/review/build/context', function (array $context, $review) {
        $context['title'] = (string) $review->custom->nickname;
        return $context;
    }, 10, 2);
    createReview(['custom_nickname' => $text]);
    foreach (renderedSurfaces() as $surface => $html) {
        expect($html)->not->toContain('PROBE-RAN', $surface);
    }
    remove_all_filters('site-reviews/review/build/context');
})->with('bracket forms');

test('a review built directly shows its brackets as text', function (string $text) {
    $review = createReview(['title' => $text]);
    expect(do_shortcode(do_shortcode((string) $review->build())))->not->toContain('PROBE-RAN');
})->with('bracket forms');

test('a template shows its brackets as text', function () {
    $shortcode = new class extends SiteReviewsSummaryShortcode {
        public function buildTemplate(): string
        {
            return '<span>[probe]</span><a href="/?a=[probe]">Link</a>';
        }

        public function defaults(): GeminiLabs\SiteReviews\Defaults\DefaultsAbstract
        {
            return glsr(GeminiLabs\SiteReviews\Defaults\SiteReviewsSummaryDefaults::class);
        }

        public function tag(): string
        {
            return 'site_reviews_summary';
        }
    };
    expect(do_shortcode(do_shortcode($shortcode->build())))->not->toContain('PROBE-RAN');
});

test('a shortcode in a field label shows as text', function () {
    add_filter('site-reviews/review-form/config', function (array $config) {
        $config['title']['label'] = '[probe]';
        return $config;
    });
    $html = do_shortcode(do_blocks('<!-- wp:site-reviews/form /-->'));
    remove_all_filters('site-reviews/review-form/config');
    expect($html)->not->toContain('PROBE-RAN')
        ->toContain('&#091;probe&#093;');
});

test('a widget block shows its brackets as text', function () {
    createReview(['title' => '[probe]']);
    $html = apply_filters('widget_block_content', '<!-- wp:site-reviews/reviews /-->', [], null);
    expect($html)->toContain('&#091;probe&#093;')
        ->not->toContain('PROBE-RAN');
});

test('a post in a query loop shows its brackets as text', function () {
    createReview(['title' => '[probe]']);
    $postId = createPost(['post_content' => '[site_reviews]', 'post_status' => 'publish']);
    $query = sprintf('{"query":{"include":[%d],"perPage":1,"postType":"post"}}', $postId);
    $html = apply_filters('the_content', sprintf(
        '<!-- wp:query %s --><!-- wp:post-template --><!-- wp:post-content /--><!-- /wp:post-template --><!-- /wp:query -->',
        $query
    ));
    expect($html)->toContain('&#091;probe&#093;')
        ->not->toContain('PROBE-RAN');
});

test('a title keeps its brackets for the reader and in the database', function () {
    $review = createReview(['title' => 'Love the [gallery] page']);
    foreach (renderedSurfaces() as $surface => $html) {
        expect($html)->toContain('Love the &#091;gallery&#093; page', $surface);
    }
    expect(get_post($review->ID)->post_title)->toBe('Love the [gallery] page');
});

test('text without a registered shortcode displays as written', function () {
    createReview(['title' => 'A [1] title']);
    foreach (renderedSurfaces() as $surface => $html) {
        expect(html_entity_decode($html))->toContain('A [1] title', $surface);
    }
});
