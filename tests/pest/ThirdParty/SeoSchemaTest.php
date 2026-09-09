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
 * plugin's graph instead.
 *
 * RankMath and SEOPress are handed only the schemas the site owner configured,
 * so every one of them is a candidate and the type gate (RatingSchemaTypeDefaults)
 * is the whole decision.
 *
 * Yoast is different: wpseo_schema_graph is the entire page graph, most of which
 * Yoast generates on its own — the featured image, the publisher, the website.
 * A type gate alone rates the featured image, which has no name, and Google then
 * reports "Missing field 'name' (in '<parent_node>')". So the Yoast controller
 * picks ONE node by its role: the publisher for a rating of the site, the
 * primary content for a rating of an assigned post, term or user.
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
    glsr()->discard('schema_args'); // and SchemaParser::storeArgs() keeps these

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
    glsr()->discard('schema_args');
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

/**
 * The shape Yoast emits for a post with a featured image, which is the graph in
 * the report this behaviour comes from. The Article and the WebPage are not
 * types Google rates. The ImageObject is (MediaObject is on Google's list) but
 * it carries a caption instead of a name, and the Organization is the publisher.
 *
 * Yoast emits that Organization only when "Site represents" is a company with a
 * name and a logo. yoastPersonGraph() is the graph without it.
 */
function yoastGraph(array $nodes = []): array
{
    $url = 'https://example.org/a-course/';
    return array_merge([
        [
            '@type' => 'Article',
            '@id' => "{$url}#article",
            'headline' => 'A course',
            'image' => ['@id' => "{$url}#primaryimage"],
            'isPartOf' => ['@id' => $url],
            'mainEntityOfPage' => ['@id' => $url],
            'publisher' => ['@id' => 'https://example.org/#organization'],
        ],
        [
            '@type' => 'WebPage',
            '@id' => $url,
            'name' => 'A course',
            'primaryImageOfPage' => ['@id' => "{$url}#primaryimage"],
        ],
        [
            '@type' => 'ImageObject',
            '@id' => "{$url}#primaryimage",
            'caption' => 'A course',
            'contentUrl' => 'https://example.org/image.webp',
            'url' => 'https://example.org/image.webp',
        ],
        [
            '@type' => 'WebSite',
            '@id' => 'https://example.org/#website',
            'publisher' => ['@id' => 'https://example.org/#organization'],
        ],
        [
            '@type' => 'Organization',
            '@id' => 'https://example.org/#organization',
            'name' => 'Example Company',
        ],
    ], $nodes);
}

/**
 * A node of a Yoast graph, by the fragment of its @id.
 */
function yoastNode(array $graph, string $fragment): array
{
    foreach ($graph as $node) {
        if (str_ends_with((string) ($node['@id'] ?? ''), $fragment)) {
            return $node;
        }
    }
    return [];
}

/**
 * A Course is a type Google rates, and Yoast connects a piece like this to the
 * page with mainEntityOfPage.
 */
function yoastCourseNode(array $values = []): array
{
    return array_replace([
        '@type' => 'Course',
        '@id' => 'https://example.org/a-course/#course',
        'mainEntityOfPage' => ['@id' => 'https://example.org/a-course/'],
        'name' => 'A course',
    ], $values);
}

/**
 * Yoast emits an Organization piece only when its "Site represents" setting is a
 * company that has both a name and a logo. A site that represents a person gets
 * a Person piece instead, and Person is not a type Google rates.
 *
 * @see Yoast\WP\SEO\Generators\Schema\Organization::is_needed()
 * @see Yoast\WP\SEO\Context\Meta_Tags_Context::generate_site_represents()
 */
function yoastPersonGraph(): array
{
    $url = 'https://example.org/a-course/';
    $personId = 'https://example.org/#/schema/person/9a8b7c6d5e';
    return [
        [
            '@type' => 'WebPage',
            '@id' => $url,
            'name' => 'A course',
        ],
        [
            '@type' => 'WebSite',
            '@id' => 'https://example.org/#website',
            'publisher' => ['@id' => $personId],
        ],
        [
            '@type' => 'Person',
            '@id' => $personId,
            'name' => 'Jane Doe',
        ],
    ];
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

test('yoast rates the publisher when the summary covers the whole site', function () {
    schemaPost(); // no assignment, so the rating is a rating of the site
    createReview(['rating' => 5]);

    $graph = glsr(YoastController::class)->filterSchema(yoastGraph());

    expect(yoastNode($graph, '#organization')['aggregateRating']['ratingValue'])->toBe(5.0)
        ->and(yoastNode($graph, '#organization')['review'])->toHaveCount(1)
        ->and(yoastNode($graph, '#primaryimage'))->toBe(yoastNode(yoastGraph(), '#primaryimage'));
});

test('yoast rates nothing when the page has no node the rating can belong to', function () {
    // The report this comes from: a summary assigned to a post, on a page whose
    // only rated types are the publisher and the featured image. The rating is
    // not a rating of the site, so the publisher is out; the primary content is
    // an Article, which Google does not rate. Nothing carries it.
    $postId = createPost();
    schemaPost("[site_reviews_summary assigned_posts={$postId} schema=true]");
    createReview(['assigned_posts' => $postId, 'rating' => 5]);

    $graph = glsr(YoastController::class)->filterSchema(yoastGraph());

    expect($graph)->toBe(yoastGraph());
});

test('yoast rates the primary content when the summary is assigned', function () {
    $postId = createPost();
    schemaPost("[site_reviews_summary assigned_posts={$postId} schema=true]");
    createReview(['assigned_posts' => $postId, 'rating' => 4]);

    // The Article carries mainEntityOfPage too, so this also asserts that a
    // primary node of an unrated type does not end the search.
    $graph = glsr(YoastController::class)->filterSchema(yoastGraph([yoastCourseNode()]));

    expect(yoastNode($graph, '#course')['aggregateRating']['ratingValue'])->toBe(4.0)
        ->and(yoastNode($graph, '#organization'))->not->toHaveKey('aggregateRating')
        ->and(yoastNode($graph, '#primaryimage'))->not->toHaveKey('aggregateRating');
});

test('yoast rates the publisher and not the page content when the summary is not assigned', function () {
    schemaPost('[site_reviews_summary schema=true]');
    createReview(['rating' => 5]);

    $graph = glsr(YoastController::class)->filterSchema(yoastGraph([yoastCourseNode()]));

    expect(yoastNode($graph, '#organization'))->toHaveKey('aggregateRating')
        ->and(yoastNode($graph, '#course'))->not->toHaveKey('aggregateRating');
});

test('yoast rates nothing when the site represents a person', function () {
    // publisherKey() does find the Person node through the WebSite publisher
    // reference. The type gate is what rejects it.
    schemaPost(); // no assignment, so the rating is a rating of the site
    createReview(['rating' => 5]);

    $graph = glsr(YoastController::class)->filterSchema(yoastPersonGraph());

    expect($graph)->toBe(yoastPersonGraph());
});

test('yoast rates nothing when the node it would choose has no name', function () {
    // Google needs a name on the thing the rating describes. Without one the
    // rating can only produce a Search Console error, so it is not attached.
    $postId = createPost();
    schemaPost("[site_reviews_summary assigned_posts={$postId} schema=true]");
    createReview(['assigned_posts' => $postId, 'rating' => 5]);

    $nameless = [yoastCourseNode(['name' => ''])];

    $graph = glsr(YoastController::class)->filterSchema(yoastGraph($nameless));

    expect($graph)->toBe(yoastGraph($nameless));
});

test('yoast treats a schema built without shortcode args as a rating of the site', function () {
    // A third party can supply the schema through this filter, in which case
    // SchemaParser::storeArgs() never runs. Nothing narrowed such a rating.
    $callback = fn () => [
        '@type' => 'LocalBusiness',
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => 4.0,
            'reviewCount' => 2,
        ],
        'name' => 'Example Company',
    ];
    add_filter('site-reviews/schema/generate', $callback);

    $graph = glsr(YoastController::class)->filterSchema(yoastGraph());

    remove_filter('site-reviews/schema/generate', $callback);

    expect(yoastNode($graph, '#organization'))->toHaveKey('aggregateRating')
        ->and(yoastNode($graph, '#primaryimage'))->not->toHaveKey('aggregateRating');
});

test('yoast puts the rating under itemReviewed on a node that is itself a review', function () {
    $postId = createPost();
    schemaPost("[site_reviews assigned_posts={$postId} schema=true]");
    createReview(['assigned_posts' => $postId, 'rating' => 5]);

    // A rating cannot hang off a Review node — it belongs to the thing reviewed —
    // so the controller nests it. Note the node has to carry a rated type as well:
    // Review is not in RatingSchemaTypeDefaults, so a node typed only "Review"
    // is never chosen in the first place.
    $graph = glsr(YoastController::class)->filterSchema([
        [
            '@type' => ['Product', 'Review'],
            'mainEntityOfPage' => ['@id' => 'https://example.org/a-product/'],
            'name' => 'A product',
        ],
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

    $node = ['@type' => 'LocalBusiness', 'name' => 'Example Company'];

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
