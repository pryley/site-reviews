<?php

use GeminiLabs\SiteReviews\Integrations\Elementor\Controller as ElementorController;
use GeminiLabs\SiteReviews\Integrations\RankMath\Controller as RankMathController;
use GeminiLabs\SiteReviews\Integrations\SEOPress\Controller as SEOPressController;
use GeminiLabs\SiteReviews\Integrations\YoastSEO\Controller as YoastController;
use GeminiLabs\SiteReviews\Modules\Schema;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The SEO integrations: RankMath, Yoast and SEOPress.
 *
 * A site that runs an SEO plugin ends up with two competing JSON-LD graphs, so
 * when one of them is nominated in settings (schema.integration.plugin) the
 * plugin stops printing its own schema and injects the rating into the SEO
 * plugin's graph instead. Each of these three controllers is that injection, and
 * all three do the same thing: generate the plugin's schema, then walk the SEO
 * plugin's graph and attach aggregateRating and review to any node whose @type is
 * one Google will show a rating for (RatingSchemaTypeDefaults).
 *
 * The controllers are called directly here — the same way the filter would call
 * them — because none of the three SEO plugins is stubbed. Their Hooks classes
 * gate on the setting, not on the plugin being installed (RankMath and SEOPress
 * do not check at all), so what is under test is entirely the plugin's own code.
 *
 * Schema::generate() reads the CURRENT POST's content, not the rendered page, so
 * each test puts a schema-enabled shortcode in a post and makes it the global
 * post — which is the state WordPress is in when an SEO plugin runs its filter.
 */

beforeEach(function () {
    resetPluginState();
    glsr()->discard('schemas'); // Schema::store() keeps them on the process-wide container

    /*
     * Elementor has to be unhooked from the schema, and only a stub makes that necessary.
     *
     * Elementor is the one page builder the stubs fully switch on — Elementor\Plugin is declared
     * and the stub's ELEMENTOR_VERSION (3.29.0) clears the required 3.19.0 — so its integration
     * hooks filterGeneratedSchema onto site-reviews/schema/generate, the first thing
     * SchemaParser::generate() fires. That callback reads \Elementor\Plugin::$instance, a static the
     * stub declares but never populates, and dereferences it: fatal.
     *
     * Not a defect: on a real site Elementor assigns $instance on plugins_loaded, long before a
     * page renders, so class_exists() implies $instance; class-without-instance is a stub-only
     * state. It does mean Elementor\SchemaParser is uncovered — an integration excluded from the
     * coverage gate (see tests/pest/README.md).
     *
     * remove_filter() is asserted so a rename or priority change here fails loudly instead of
     * silently restoring the fatal.
     */
    $removed = remove_filter('site-reviews/schema/generate',
        [glsr(ElementorController::class), 'filterGeneratedSchema']
    );
    expect($removed)->toBeTrue();
});

afterEach(function () {
    glsr()->discard('schemas');
    unset($GLOBALS['post']);
});

/**
 * A post whose content asks for the schema, made the global post. get_post() with
 * no argument returns $GLOBALS['post'], which is what SchemaParser::generate()
 * and Schema::getSchemaOption() both read.
 */
function schemaPost(string $shortcode = '[site_reviews schema=true]'): void
{
    $GLOBALS['post'] = get_post(createPost(['post_content' => $shortcode]));
}

test('the generated schema is what gets injected', function () {
    // The premise of all three integrations, asserted once: a schema-enabled
    // shortcode in the post content produces a rating for the SEO plugin to carry.
    schemaPost();
    createReview(['rating' => 4]);
    createReview(['rating' => 5]);

    $schema = glsr(Schema::class)->generate();

    expect($schema['@type'])->toBe('LocalBusiness') // the default schema.type setting
        ->and($schema['aggregateRating']['ratingValue'])->toBe(4.5)
        ->and($schema['aggregateRating']['reviewCount'])->toBe(2)
        ->and($schema['review'])->toHaveCount(2);
});

test('rankmath receives the rating on a rated node', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // RankMath hands its validated data over keyed by node: richSnippet is the
    // one it renders, schema-* and new-* are the editor preview's.
    $data = glsr(RankMathController::class)->filterSchema([
        'richSnippet' => ['@type' => 'LocalBusiness'],
    ]);

    expect($data['richSnippet']['aggregateRating']['ratingValue'])->toBe(5.0)
        ->and($data['richSnippet']['aggregateRating']['reviewCount'])->toBe(1)
        ->and($data['richSnippet']['review'])->toHaveCount(1);
});

test('rankmath receives the rating on its preview nodes too', function () {
    schemaPost();
    createReview(['rating' => 3]);

    $data = glsr(RankMathController::class)->filterSchema([
        'new-1' => ['@type' => 'Product'],
        'schema-2' => ['@type' => 'Recipe'],
    ]);

    expect($data['new-1'])->toHaveKey('aggregateRating')
        ->and($data['schema-2'])->toHaveKey('aggregateRating');
});

test('rankmath leaves a node alone when its type cannot show a rating', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // WebPage is not in RatingSchemaTypeDefaults — Google will not show a rating
    // for it — and neither is a key RankMath does not own.
    $data = glsr(RankMathController::class)->filterSchema([
        'richSnippet' => ['@type' => 'WebPage'],
        'somethingElse' => ['@type' => 'LocalBusiness'],
    ]);

    expect($data)->toBe([
        'richSnippet' => ['@type' => 'WebPage'],
        'somethingElse' => ['@type' => 'LocalBusiness'],
    ]);
});

test('yoast receives the rating on a rated node of its graph', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // Yoast's graph is a flat list of nodes, each with an @type that may be an
    // array.
    //
    // The filter opens with a WooCommerce escape hatch — function_exists('is_product')
    // && is_product() — which is NOT under test: the woocommerce stub declares
    // is_product_taxonomy() but not is_product(), so function_exists() is false
    // and the hatch is never even evaluated. Covering that branch needs the real
    // WooCommerce, since a stubbed is_product() would only ever return null.
    $graph = glsr(YoastController::class)->filterSchema([
        ['@type' => 'LocalBusiness'],
        ['@type' => 'WebPage'], // not a rated type
    ]);

    expect($graph[0]['aggregateRating']['ratingValue'])->toBe(5.0)
        ->and($graph[0]['review'])->toHaveCount(1)
        ->and($graph[1])->toBe(['@type' => 'WebPage']);
});

test('yoast puts the rating under itemReviewed on a node that is itself a review', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // A rating cannot hang off a Review node — it belongs to the thing reviewed —
    // so the controller nests it. Note the node has to carry a rated type as well:
    // Review is not in RatingSchemaTypeDefaults, so a node typed only "Review"
    // never reaches this branch at all.
    $graph = glsr(YoastController::class)->filterSchema([
        ['@type' => ['Product', 'Review']],
    ]);

    expect($graph[0]['itemReviewed']['aggregateRating']['ratingValue'])->toBe(5.0)
        ->and($graph[0]['itemReviewed']['review'])->toHaveCount(1)
        ->and($graph[0])->not->toHaveKey('aggregateRating');
});

test('seopress receives the rating on a single schema', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // The free version filters one schema at a time (seopress_schemas_auto_*_json),
    // which the controller handles by wrapping it into a list of one.
    $schema = glsr(SEOPressController::class)->filterSchema(['@type' => 'Product']);

    expect($schema['aggregateRating']['ratingValue'])->toBe(5.0)
        ->and($schema['review'])->toHaveCount(1);
});

test('seopress receives the rating on a list of schemas', function () {
    schemaPost();
    createReview(['rating' => 5]);

    // The pro version filters them all at once (seopress_json_schema_generator_get_jsons).
    $schemas = glsr(SEOPressController::class)->filterSchemas([
        ['@type' => 'Product'],
        ['@type' => 'WebPage'],
    ]);

    expect($schemas[0])->toHaveKey('aggregateRating')
        ->and($schemas[1])->toBe(['@type' => 'WebPage']);
});

test('no reviews means no injection at all', function () {
    schemaPost();
    // no reviews: Schema::buildSummary() returns [] when the rating count is zero

    $node = ['@type' => 'LocalBusiness'];

    expect(glsr(RankMathController::class)->filterSchema(['richSnippet' => $node]))
        ->toBe(['richSnippet' => $node]);
    expect(glsr(YoastController::class)->filterSchema([$node]))->toBe([$node]);
    expect(glsr(SEOPressController::class)->filterSchemas([$node]))->toBe([$node]);
});

test('a post without a schema shortcode means no injection', function () {
    schemaPost('[site_reviews]'); // schema is off by default
    createReview(['rating' => 5]);

    $node = ['@type' => 'LocalBusiness'];

    expect(glsr(RankMathController::class)->filterSchema(['richSnippet' => $node]))
        ->toBe(['richSnippet' => $node]);
});
