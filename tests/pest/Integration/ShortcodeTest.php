<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Schema;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createReviews;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The four shortcodes, rendered end to end.
 *
 * These are the plugin's real entry points: a shortcode pulls its arguments
 * through the Defaults pipeline, queries the database, and renders through the
 * template/tag/style machinery. Asserting on the rendered HTML is the cheapest
 * way to hold that whole stack to its contract, and the most honest one — it is
 * exactly what a visitor gets.
 *
 * The assertions check content and structure, not exact markup: the HTML varies
 * by style framework (see config/styles), and pinning it byte-for-byte would
 * turn every CSS tweak into a test failure.
 *
 * Note the two different field-name prefixes, both verified against the source:
 * the review form uses glsr()->id ("site-reviews", from Field::namePrefix),
 * while the settings form uses OptionManager::databaseKey() ("site_reviews",
 * from SettingField::namePrefix).
 */

beforeEach(fn () => resetPluginState());

test('renders reviews', function () {
    $review = createReview([
        'content' => 'The pizza was excellent and the staff were friendly.',
        'name' => 'Jane Doe',
        'title' => 'Excellent pizza',
    ]);
    $html = do_shortcode('[site_reviews]');
    expect($html)->toContain('The pizza was excellent and the staff were friendly.')
        ->toContain('Excellent pizza')
        ->toContain('Jane Doe')
        ->toContain('glsr-reviews-wrap')
        ->toContain('data-rating="5"')
        ->toContain('id="review-'.$review->ID.'"');
});

test('renders no reviews when there are none', function () {
    $html = do_shortcode('[site_reviews]');
    expect($html)->not->toContain('id="review-');
});

test('limits the number of reviews rendered', function () {
    createReviews(5);
    $html = do_shortcode('[site_reviews display=2]');
    expect(substr_count($html, 'id="review-'))->toBe(2);
});

test('hides the review fields it is told to hide', function () {
    // hide options: title, rating, date, assigned_links, content, avatar, author,
    // verified, response (SiteReviewsShortcode::hideOptions)
    createReview([
        'content' => 'Hidden content check.',
        'title' => 'Hidden title check',
    ]);
    $html = do_shortcode('[site_reviews hide=title,content]');
    expect($html)->toContain('id="review-')
        ->not->toContain('Hidden content check.')
        ->not->toContain('Hidden title check');
});

test('renders only the reviews assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'content' => 'Assigned to the post.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_posts={$postId}]");
    expect($html)->toContain('Assigned to the post.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews assigned to a category', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    createReview(['assigned_terms' => $termId, 'content' => 'Assigned to the category.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_terms={$termId}]");
    expect($html)->toContain('Assigned to the category.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews assigned to a user', function () {
    $userId = createUser();
    createReview(['assigned_users' => $userId, 'content' => 'Assigned to the user.']);
    createReview(['content' => 'Assigned to nothing.']);
    $html = do_shortcode("[site_reviews assigned_users={$userId}]");
    expect($html)->toContain('Assigned to the user.')
        ->not->toContain('Assigned to nothing.');
});

test('renders only the reviews rated at least the given rating', function () {
    // Sql::clauseAndRating() is "rating > (rating - 1)", i.e. at least.
    createReview(['content' => 'A five star review.', 'rating' => 5]);
    createReview(['content' => 'A one star review.', 'rating' => 1]);
    $html = do_shortcode('[site_reviews rating=5]');
    expect($html)->toContain('A five star review.')
        ->not->toContain('A one star review.');
});

test('paginates', function () {
    // pagination enum: ajax, loadmore, 1, true (SiteReviewsDefaults)
    createReviews(3);
    $html = do_shortcode('[site_reviews display=1 pagination=true]');
    expect(substr_count($html, 'id="review-'))->toBe(1);
    expect($html)->toContain('class="pagination"');
});

test('renders a single review', function () {
    // The review is selected with post_id; "id" is the shortcode's own custom id.
    $review = createReview(['content' => 'The single review.']);
    $html = do_shortcode("[site_review post_id={$review->ID}]");
    expect($html)->toContain('The single review.')
        ->toContain('id="review-'.$review->ID.'"');
});

test('renders nothing for a single review that does not exist', function () {
    $html = do_shortcode('[site_review post_id=999999001]');
    expect($html)->not->toContain('id="review-');
});

test('renders the summary of all reviews', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);
    $html = do_shortcode('[site_reviews_summary]');
    // data-rating / data-reviews are set by the star-rating partial.
    expect($html)->toContain('glsr-summary-wrap')
        ->toContain('data-reviews="2"');
});

test('renders the summary of only the reviews assigned to a post', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 1]);
    createReview(['rating' => 5]); // not assigned: must not count
    $html = do_shortcode("[site_reviews_summary assigned_posts={$postId}]");
    expect($html)->toContain('glsr-summary-wrap')
        ->toContain('data-rating="1"')
        ->toContain('data-reviews="1"');
});

test('renders the review form', function () {
    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('<form')
        ->toContain('glsr-form-wrap')
        ->toContain('name="site-reviews[content]"')
        ->toContain('name="site-reviews[rating]"')
        // hidden fields, from ReviewForm::configHidden()
        ->toContain('name="site-reviews[_action]"')
        ->toContain('name="site-reviews[_nonce]"')
        ->toContain('name="site-reviews[form_id]"');
});

test('hides the review form fields it is told to hide', function () {
    // hide options: rating, title, content, name, email, terms
    // (SiteReviewsFormShortcode::hideOptions)
    $html = do_shortcode('[site_reviews_form hide=title,email]');
    expect($html)->toContain('name="site-reviews[content]"')
        ->not->toContain('name="site-reviews[title]"')
        ->not->toContain('name="site-reviews[email]"');
});

test('renders the review form honeypot', function () {
    // Form::build() always appends Honeypot::build(), hidden with inline styles.
    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('display:none;');
});

/*
 * The rating summary.
 *
 * Four template tags — rating, stars, text, percentages — each of which can be hidden
 * on its own (SiteReviewsSummaryShortcode::hideOptions), and each of which is a class
 * resolved by NAME from the tag (buildTemplateTag → Helper::buildClassName). So a tag
 * that resolves to nothing renders as nothing, silently, and that is worth pinning.
 */

test('the summary says what it says in the sentence a visitor reads', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);

    $html = do_shortcode('[site_reviews_summary]');

    // the default sentence: "{rating} out of {max} stars (based on {num} reviews)"
    // Rating::format() gives one decimal for any average above zero
    expect($html)->toContain('4.0 out of 5 stars (based on 2 reviews)');
});

test('the summary sentence can be written by the person whose site it is', function () {
    // {rating}, {max} and {num} are the only tags, and they are what makes a custom
    // sentence worth allowing at all.
    createReviews(3, ['rating' => 4]);

    $html = do_shortcode('[site_reviews_summary text="Rated {rating}/{max} by {num} people"]');

    expect($html)->toContain('Rated 4.0/5 by 3 people');
});

test('one review is a review, not reviews', function () {
    // _nx() with the count, which is the reason the default text is not a plain string.
    createReview(['rating' => 5]);

    expect(do_shortcode('[site_reviews_summary]'))->toContain('based on 1 review)');
});

test('the summary hides the parts it is told to hide', function () {
    createReview(['rating' => 5]);

    $all = do_shortcode('[site_reviews_summary]');
    expect($all)->toContain('glsr-summary-text')
        ->toContain('glsr-summary-percentages')
        ->toContain('glsr-summary-stars');

    // and the hide keys are NOT the tag names — SummaryTag::hideOption() maps the `text`
    // tag onto `summary` and the `percentages` tag onto `bars`, because those are the
    // words a person would use for them.
    $hidden = do_shortcode('[site_reviews_summary hide=summary,bars,stars]');
    expect($hidden)->not->toContain('glsr-summary-text')
        ->not->toContain('glsr-summary-percentages')
        ->not->toContain('glsr-summary-stars');
});

test('the percentage bars can be labelled by the person whose site it is', function () {
    // Rating::labels() is Excellent/Very good/Average/Poor/Terrible, highest first, and
    // the custom list is read in the same order. A short list leaves the rest as they
    // were rather than blanking them (normalizeLabels).
    createReview(['rating' => 5]);

    $html = do_shortcode('[site_reviews_summary labels="Superb, Fine"]');

    expect($html)->toContain('Superb')
        ->toContain('Fine')
        ->toContain('Average')   // not given, so left alone
        ->not->toContain('Excellent'); // replaced
});

test('a summary of nothing can be hidden, or can say there is nothing', function () {
    // `if_empty` is not a field — it is the answer to "what should a page with no
    // reviews on it look like". Without it the summary renders zeroes, which is the
    // right thing for a page that expects reviews and the wrong thing for one that
    // does not.
    expect(do_shortcode('[site_reviews_summary hide=if_empty]'))->toBe('');

    expect(do_shortcode('[site_reviews_summary]'))
        ->toContain('glsr-summary-wrap')
        ->toContain('based on 0 reviews)');
});

test('the summary with nothing in it can be given something to say', function () {
    // `summary/if_empty` is what turns "hide it" into "say this instead". Note that
    // whatever it returns is still WRAPPED like any other output — an empty template is
    // what makes Shortcode::build() return nothing at all, and this one is not empty
    // any more. That matters: the wrapper carries the data-shortcode attributes, and
    // those are how the review form finds the summary it has to update after somebody
    // submits a review to a page that had none.
    add_filter('site-reviews/summary/if_empty', fn () => '<p>No reviews yet.</p>');

    $html = do_shortcode('[site_reviews_summary hide=if_empty]');

    expect($html)->toContain('<p>No reviews yet.</p>')
        ->toContain('data-shortcode="site_reviews_summary"');
});

test('the summary writes the aggregate rating into the page schema, but only when asked', function () {
    // The schema is what Google reads to show the stars in a search result. It must be
    // on the page ONCE — hence the setting — so it is off unless it is asked for.
    glsr()->store('schemas', []); // the plugin's in-memory store is not rolled back
    createReview(['rating' => 5]);
    createReview(['rating' => 4]);

    expect(schemaOnThePage())->toBe(''); // nothing asked for it

    do_shortcode('[site_reviews_summary schema=true]');

    expect(schemaOnThePage())->toContain('application/ld+json')
        ->toContain('AggregateRating')
        ->toContain('"reviewCount":2')
        ->toContain('"bestRating":5');

    glsr()->store('schemas', []);
});

/**
 * The JSON-LD the summary puts on the page, as Schema::render() would print it on
 * wp_footer.
 */
function schemaOnThePage(): string
{
    ob_start();
    glsr(Schema::class)->render();

    return (string) ob_get_clean();
}

/*
 * The review form on a site that will not take a review from a stranger.
 */

test('a site that requires a login asks a stranger to log in, and does not show them the form', function () {
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    wp_set_current_user(0);

    $html = do_shortcode('[site_reviews_form]');

    expect($html)->toContain('You must be')
        ->toContain(wp_login_url())
        ->not->toContain('<form');
});

test('and shows the form to somebody who has logged in', function () {
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    wp_set_current_user(createUser());

    expect(do_shortcode('[site_reviews_form]'))->toContain('<form');
});

test('a site that takes registrations offers to register them', function () {
    // Both have to be true: the plugin's setting AND WordPress's own "anyone can
    // register". Offering a registration link on a site that refuses registrations
    // sends people to a page that turns them away.
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    glsr(OptionManager::class)->set('settings.general.require.register', 'yes');
    wp_set_current_user(0);

    update_option('users_can_register', 0);
    expect(do_shortcode('[site_reviews_form]'))->not->toContain('You may also');

    update_option('users_can_register', 1);
    expect(do_shortcode('[site_reviews_form]'))
        ->toContain('You may also')
        ->toContain(wp_registration_url());
});

test('a site with its own login and registration pages sends people there instead', function () {
    // A membership plugin's login page, rather than wp-login.php. The filters are added
    // and removed around the one call, so they cannot affect anybody else's login link.
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    glsr(OptionManager::class)->set('settings.general.require.register', 'yes');
    glsr(OptionManager::class)->set('settings.general.require.login_url', 'https://example.org/signin');
    glsr(OptionManager::class)->set('settings.general.require.register_url', 'https://example.org/join');
    update_option('users_can_register', 1);
    wp_set_current_user(0);

    $html = do_shortcode('[site_reviews_form]');

    expect($html)->toContain('https://example.org/signin')
        ->toContain('https://example.org/join')
        ->not->toContain('wp-login.php');

    expect(wp_login_url())->toContain('wp-login.php'); // and everybody else's is untouched
});

/*
 * The wrapper every shortcode is rendered inside.
 */

test('a rendered shortcode says what it is and where it came from', function () {
    // The JS finds a shortcode by these: pagination, the load-more button and the form's
    // "put the new review here" all look for data-shortcode and the custom id.
    createReview();

    $html = do_shortcode('[site_reviews id=my-reviews]');

    expect($html)->toContain('data-shortcode="site_reviews"')
        ->toContain('data-from="shortcode"')
        ->toContain('id="my-reviews"');
});

test('a class is put on the element it belongs on', function () {
    // Classes prefixed has-/is-/items- are layout, and go on the WRAPPER; anything else
    // is styling for the reviews themselves and goes on the root. Getting it the wrong
    // way round is a CSS rule that silently does nothing.
    createReview();

    $html = do_shortcode('[site_reviews class="my-styling is-large"]');

    expect($html)->toContain('my-styling')->toContain('is-large');

    // the wrapper is the outer element, so its classes appear before the root's
    expect(strpos($html, 'is-large'))->toBeLessThan(strpos($html, 'my-styling'));
});

test('a shortcode can be asked to explain itself', function () {
    // debug=true, and only from an actual shortcode — a block or a function call must
    // never print it (Shortcode::debug checks $this->from).
    createReview();

    expect(do_shortcode('[site_reviews debug=true]'))->toContain('glsr-debug');

    expect(glsr()->shortcode('site_reviews')->build(['debug' => true], 'function'))
        ->not->toContain('glsr-debug');
});
