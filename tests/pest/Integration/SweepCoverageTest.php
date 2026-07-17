<?php

use GeminiLabs\SiteReviews\Database\NormalizePaginationArgs;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The shortcode and database leftovers: option lookups for the block editor,
 * the modal excerpt flag, pagination URL normalization, and the query edges.
 */

beforeEach(fn () => resetPluginState());

afterEach(function () {
    glsr()->discard('use_modal');
});

/*
 * Shortcodes.
 */

test('the modal excerpt setting flags the page for the modal script', function () {
    glsr(OptionManager::class)->set('settings.reviews.excerpts_action', 'modal');
    createReview();

    do_shortcode('[site_reviews]');
    expect(glsr()->retrieveAs('bool', 'use_modal'))->toBeTrue();

    glsr()->discard('use_modal');
    do_shortcode('[site_review id="'.glsr_get_reviews(['per_page' => 1])->reviews[0]->ID.'"]');
    expect(glsr()->retrieveAs('bool', 'use_modal'))->toBeTrue();
});

test('a summary tag whose class is filtered away renders as nothing', function () {
    add_filter('site-reviews/summary/tag/rating', fn () => 'No\Such\TagClass');
    createReview(['rating' => 5]);

    $html = do_shortcode('[site_reviews_summary]');

    expect($html)->not->toBe('');
});

test('the form login link carries the redirect and can force reauthentication', function () {
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    glsr(OptionManager::class)->set('settings.general.require.login_url', 'https://example.org/login/');
    wp_set_current_user(0);

    $loginUrl = glsr(SiteReviewsFormShortcode::class)
        ->filterLoginUrl(wp_login_url(), 'https://example.org/reviews/', true);

    expect($loginUrl)->toContain('redirect_to=')
        ->and($loginUrl)->toContain('reauth=1');
});

test('a debugged form dumps its fields to the console instead of failing', function () {
    wp_set_current_user(createUser());

    $html = glsr(SiteReviewsFormShortcode::class)->build(['debug' => true]);

    expect($html)->toContain('glsr-form'); // rendered, with the debug dump logged
});

test('a shortcode option list that is not backed by a protected method is empty', function () {
    // get() dispatches by name; a name that maps to a PUBLIC method must answer
    // nothing rather than call it
    expect(glsr(ShortcodeOptionManager::class)->get('get'))->toBe([]);
});

test('the assigned-terms options honour search and always include the chosen ones', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy, 'name' => 'Aardvark Reviews']);
    $chosen = createTerm(['taxonomy' => glsr()->taxonomy, 'name' => 'Zebra Reviews']);

    $options = glsr(ShortcodeOptionManager::class)->get('assigned_terms', [
        'include' => [get_term($chosen, glsr()->taxonomy)->term_taxonomy_id],
        'search' => 'Aardvark',
    ]);

    expect(implode(' ', $options))->toContain('Aardvark')
        ->and(implode(' ', $options))->toContain('Zebra'); // chosen but not matching the search
});

test('the user options honour search and always include the chosen ones', function () {
    $author = createUser(['display_name' => 'Aardvark Author']);
    $chosen = createUser(['display_name' => 'Zebra Author']);

    foreach (['assigned_users', 'author'] as $option) {
        $options = glsr(ShortcodeOptionManager::class)->get($option, [
            'include' => [$chosen],
            'search' => 'Aardvark',
        ]);

        expect(implode(' ', $options))->toContain('Aardvark')
            ->and(implode(' ', $options))->toContain('Zebra');
    }
});

test('hide options outside a shortcode context are empty', function () {
    expect(glsr(ShortcodeOptionManager::class)->get('hide'))->toBe([]);
});

/*
 * Database edges.
 */

test('pagination on the front page is the home url itself', function () {
    // normalizePageUrl() reads the STORED paged request, not the constructor
    // args — an earlier version of this test passed `url` to the constructor
    // and asserted a value both branches produce, proving nothing
    glsr()->store(glsr()->paged_handle, ['page' => 2, 'url' => \GeminiLabs\SiteReviews\Helpers\Url::home()]);
    try {
        $args = new NormalizePaginationArgs();

        expect($args->pageUrl)->toBe(\GeminiLabs\SiteReviews\Helpers\Url::home());
    } finally {
        glsr()->discard(glsr()->paged_handle);
    }
});

test('a rating outside the allowed range counts as zero', function () {
    $reduced = protectedMethod(RatingManager::class, 'reduce')
        ->invoke(glsr(RatingManager::class), ['local' => [1 => 2, 9 => 4]]);

    expect($reduced[1])->toBe(2)
        ->and($reduced[9])->toBe(0) // kept, but contributing nothing
        ->and(array_sum($reduced))->toBe(2);
});

test('ratings for an unknown meta type are an empty list', function () {
    expect(glsr(Query::class)->ratingsFor('bogus_type'))->toBe([]);
});

test('an unpaginated count is the reviews already in hand', function () {
    expect(glsr(Query::class)->totalReviews(['per_page' => -1], ['a', 'b', 'c']))->toBe(3);
});

test('a site with no previous settings anywhere has none to restore', function () {
    delete_option(OptionManager::databaseKey());

    expect(glsr(OptionManager::class)->previous())->toBe([]);
});

test('an addon shortcode starts with nothing to enqueue and nothing to hide', function () {
    // every shipped shortcode overrides both; the base defaults are the contract
    // an addon shortcode starts from
    $shortcode = new class extends \GeminiLabs\SiteReviews\Shortcodes\Shortcode {
        public function buildTemplate(): string
        {
            return '';
        }

        public function description(): string
        {
            return 'An addon shortcode';
        }

        public function name(): string
        {
            return 'Addon Shortcode';
        }

        protected function config(): array
        {
            return [];
        }
    };
    $shortcode->enqueue(); // the base no-op

    $fn = fn () => $this->hideOptions();
    expect($fn->bindTo($shortcode, \GeminiLabs\SiteReviews\Shortcodes\Shortcode::class)())->toBe([]);
});

test('an assigned post type survives the id sanitizing untouched', function () {
    $normalized = protectedMethod(SiteReviewsShortcode::class, 'normalizeAssignedPosts')
        ->invoke(glsr(SiteReviewsShortcode::class), 'page');

    expect($normalized)->toContain('page');
});

test('a widget field without a type renders nothing', function () {
    $widget = new \GeminiLabs\SiteReviews\Widgets\SiteReviewsWidget();

    ob_start();
    protectedMethod(\GeminiLabs\SiteReviews\Widgets\SiteReviewsWidget::class, 'renderField')
        ->invoke($widget, 'broken', []); // no type: the field is invalid

    expect(ob_get_clean())->toBe('');
});

test('the rest response reports the review status', function () {
    // NOTE: the zeroed-gmt repair on the line above prepareStatus stays
    // uncovered — Review's sanitizers can no longer produce the legacy
    // '0000-00-00' rows it exists to repair (probed: construction and property
    // writes both re-derive a real date).
    $review = createReview();
    $prepared = new \GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\PrepareReviewData(
        ['status'], $review, new WP_REST_Request()
    );

    protectedMethod(get_class($prepared), 'prepareStatus')->invoke($prepared);
    $property = new ReflectionProperty($prepared, 'data');
    $property->setAccessible(true);

    expect($property->getValue($prepared)['status'])->toBe($review->status);
});
