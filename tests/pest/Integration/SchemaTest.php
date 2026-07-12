<?php

use GeminiLabs\SiteReviews\Modules\Schema;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The JSON-LD schema. A shortcode with schema=true stores its schema
 * (SiteReviewsShortcode), and PublicController::renderSchema prints whatever was
 * stored on wp_footer — but only when no SEO plugin has been nominated to own
 * the schema (the schema.integration.plugin setting).
 */

beforeEach(function () {
    resetPluginState();
    // Stored schemas live on the Application's storage, which is a singleton for
    // the whole process — they would otherwise leak from one test into the next.
    glsr()->discard('schemas');
    // Core still hooks the_block_template_skip_link on wp_footer, but only as a
    // back-compat sentinel: get_the_block_template_html() checks has_action() on
    // it to detect a plugin having unhooked the skip link. The function itself is
    // deprecated since 6.4, so actually FIRING wp_footer — which is what proves
    // the plugin's own schema hook — invokes it and trips the deprecation. That
    // is core's business, not the plugin's, so it is unhooked here.
    remove_action('wp_footer', 'the_block_template_skip_link');
});

function renderFooter(): string
{
    ob_start();
    do_action('wp_footer');
    return (string) ob_get_clean();
}

test('prints no schema when no shortcode asked for one', function () {
    createReview();
    do_shortcode('[site_reviews]');
    expect(renderFooter())->not->toContain('application/ld+json');
});

test('prints the schema of a reviews shortcode', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);
    do_shortcode('[site_reviews schema=true]');
    $footer = renderFooter();
    expect($footer)->toContain('application/ld+json')
        ->toContain('class="site-reviews-schema"')
        ->toContain('aggregateRating');
});

test('prints the schema of a summary shortcode', function () {
    createReview(['rating' => 5]);
    do_shortcode('[site_reviews_summary schema=true]');
    expect(renderFooter())->toContain('application/ld+json');
});

test('stores and retrieves a schema', function () {
    expect(glsr(Schema::class)->exists())->toBeFalse();
    glsr(Schema::class)->store(['@type' => 'Thing']);
    expect(glsr(Schema::class)->exists())->toBeTrue();
    expect(glsr(Schema::class)->retrieve())->toContain(['@type' => 'Thing']);
});

test('leaves the schema to the seo plugin that owns it', function () {
    // renderSchema() stands down when an SEO integration is nominated.
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)
        ->set('settings.schema.integration.plugin', 'yoast');
    createReview();
    do_shortcode('[site_reviews schema=true]');
    expect(renderFooter())->not->toContain('application/ld+json');
});
