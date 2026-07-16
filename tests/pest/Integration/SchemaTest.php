<?php

use GeminiLabs\SiteReviews\Modules\Schema;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The JSON-LD schema. A shortcode with schema=true stores its schema
 * (SiteReviewsShortcode), and PublicController::renderSchema prints whatever was
 * stored on wp_footer — but only when no SEO plugin has been nominated to own
 * the schema (the schema.integration.plugin setting).
 */

beforeEach(function () {
    resetPluginState();
    // Stored schemas live on the Application's storage, a process-wide singleton — they would
    // otherwise leak from one test into the next.
    glsr()->discard('schemas');
    // Core hooks the_block_template_skip_link on wp_footer as a back-compat sentinel
    // (get_the_block_template_html() checks has_action() to detect the skip link being unhooked).
    // The function is deprecated since 6.4, so firing wp_footer — which is how the plugin's schema
    // hook is proven — trips the deprecation. Core's business, not the plugin's, so unhook it here.
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

/*
 * Where the summary's name/description/url come from: the current page, unless a
 * per-page custom field or a plugin setting overrides it. The tests stand on a
 * hand-built query (resetRequestState() puts the globals back afterwards).
 */

function onSingularPage(int $postId): void
{
    global $post, $wp_query;
    $post = get_post($postId);
    $wp_query->queried_object = $post;
    $wp_query->queried_object_id = $postId;
    $wp_query->is_singular = true;
    setup_postdata($post);
}

function onArchivePage(callable $setup): void
{
    global $wp_query;
    $wp_query->is_archive = true;
    $setup($wp_query);
}

/**
 * A summary schema built from two 5-star ratings, so the emptiness gate passes.
 */
function builtSummary(): array
{
    return glsr(Schema::class)->buildSummary(['from' => 'test'], [5 => 2]);
}

test('the summary describes the current page', function () {
    onSingularPage(createPost([
        'post_excerpt' => 'A page about things.',
        'post_title' => 'The Reviewed Page',
    ]));

    $schema = builtSummary();

    expect($schema['@type'])->toBe('LocalBusiness') // the default type
        ->and($schema['name'])->toBe('The Reviewed Page')
        ->and($schema['description'])->toBe('A page about things.')
        ->and($schema['url'])->toBe(get_permalink());
    expect($schema['aggregateRating']['ratingValue'])->toEqual(5)
        ->and($schema['aggregateRating']['reviewCount'])->toEqual(2)
        ->and($schema['aggregateRating']['bestRating'])->toEqual(5);
});

test('a page with no excerpt is described by its trimmed content', function () {
    onSingularPage(createPost([
        'post_content' => 'The content stands in for the missing excerpt.',
        'post_excerpt' => '',
    ]));

    expect(builtSummary()['description'])->toBe('The content stands in for the missing excerpt.');
});

test('a custom field on the page beats the setting', function () {
    // The documented per-page override: a `schema_name` custom field.
    $postId = createPost(['post_title' => 'The page title']);
    update_post_meta($postId, 'schema_name', 'What the owner called it');
    onSingularPage($postId);

    expect(builtSummary()['name'])->toBe('What the owner called it');
});

test('a Product summary carries its offer and an @id', function () {
    // Google requires Product reviews to have an offer or an @id; the plugin builds the offer
    // from the price fields and derives an @id from the url when none was set.
    $postId = createPost(['post_title' => 'A Product Page']);
    update_post_meta($postId, 'schema_price', '99.00');
    update_post_meta($postId, 'schema_pricecurrency', 'EUR');
    onSingularPage($postId);
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)
        ->set('settings.schema.type.default', 'Product');

    $schema = builtSummary();

    expect($schema['@type'])->toBe('Product')
        ->and($schema['@id'])->toBe(get_permalink().'#product')
        ->and($schema['offers']['@type'])->toBe('AggregateOffer')
        ->and($schema['offers']['price'])->toBe('99.00')
        ->and($schema['offers']['priceCurrency'])->toBe('EUR');
});

test('an archive summary is named and addressed by its archive', function () {
    $termId = createTerm(['taxonomy' => 'category', 'name' => 'Reviewed Category']);
    onArchivePage(function ($wp_query) use ($termId) {
        $wp_query->is_category = true;
        $wp_query->queried_object = get_term($termId);
        $wp_query->queried_object_id = $termId;
    });

    $schema = builtSummary();

    expect($schema['name'])->toContain('Reviewed Category')
        ->and($schema['url'])->toBe(get_category_link($termId))
        ->and($schema['description'])->not->toBeEmpty(); // the term description
});

/*
 * The remaining archive flavours each resolve their own url. One test per flavour: the
 * query flags live on the wp_query global, which only the between-test teardown resets.
 */

test('a tag archive is addressed by its tag link', function () {
    $tagId = createTerm(['taxonomy' => 'post_tag']);
    onArchivePage(function ($wp_query) use ($tagId) {
        $wp_query->is_tag = true;
        $wp_query->queried_object = get_term($tagId);
    });

    expect(builtSummary()['url'])->toBe(get_tag_link($tagId));
});

test('an author archive is addressed by the author page', function () {
    $userId = createUser();
    onArchivePage(function ($wp_query) use ($userId) {
        $wp_query->is_author = true;
        $wp_query->queried_object = get_user_by('id', $userId);
    });

    expect(builtSummary()['url'])->toBe(get_author_posts_url($userId));
});

test('a post type archive is addressed by its archive link', function () {
    onArchivePage(function ($wp_query) {
        $wp_query->is_post_type_archive = true;
        $wp_query->queried_object = get_post_type_object('post');
        $wp_query->set('post_type', 'post'); // post_type_archive_title() reads the query var
    });

    expect(builtSummary()['url'])->toBe(get_post_type_archive_link('post'));
});

test('a taxonomy archive is addressed by its term link', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    onArchivePage(function ($wp_query) use ($termId) {
        $wp_query->is_tax = true;
        $wp_query->queried_object = get_term($termId);
    });

    expect(builtSummary()['url'])->toBe(get_term_link($termId));
});

test('a page with no ratings gets no summary schema at all', function () {
    // An empty aggregateRating is a Google penalty, not a schema.
    expect(glsr(Schema::class)->buildSummary(['assigned_posts' => 999999001], []))->toBe([]);
});

test('generate stores what the parser produced, and hands it back', function () {
    // Priority 1: the SEO integrations (Elementor et al) hang on this filter too, and they
    // stand down when an earlier callback has already produced a schema.
    add_filter('site-reviews/schema/generate', fn () => ['@type' => 'Generated'], 1);

    expect(glsr(Schema::class)->generate())->toBe(['@type' => 'Generated']);
    expect(glsr(Schema::class)->retrieve())->toContain(['@type' => 'Generated']);
});

test('leaves the schema to the seo plugin that owns it', function () {
    // renderSchema() stands down when an SEO integration is nominated.
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)
        ->set('settings.schema.integration.plugin', 'yoast');
    createReview();
    do_shortcode('[site_reviews schema=true]');
    expect(renderFooter())->not->toContain('application/ld+json');
});
