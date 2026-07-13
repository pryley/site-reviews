<?php

use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * What a shortcode offers to be configured with.
 *
 * ShortcodeTest renders the shortcodes; this is the other half — the settings each one
 * declares, and the options behind them. Nothing here is rendered on the front end. It
 * is what the Gutenberg block's sidebar and the TinyMCE popup are BUILT from, and what
 * the REST endpoint behind their search fields answers with (RestShortcodeController →
 * ShortcodeOptionManager). Get it wrong and the block's controls are missing, mislabelled
 * or unfillable, and none of the rendering tests would notice.
 *
 * The options are answered by ShortcodeOptionManager, which dispatches on the option
 * name through a ReflectionMethod — so an option nobody wrote a method for is not an
 * error, it is an empty list, and a method that is not protected is not reachable at
 * all. That indirection is why it is worth naming the options out loud.
 */

beforeEach(fn () => resetPluginState());

/**
 * Every shortcode, so that each of the four gets the same questions asked of it.
 *
 * @return array<string, array{0:class-string, 1:string}>
 */
function everyShortcode(): array
{
    return [
        'site_reviews' => [SiteReviewsShortcode::class, 'site_reviews'],
        'site_review' => [SiteReviewShortcode::class, 'site_review'],
        'site_reviews_form' => [SiteReviewsFormShortcode::class, 'site_reviews_form'],
        'site_reviews_summary' => [SiteReviewsSummaryShortcode::class, 'site_reviews_summary'],
    ];
}

test('a shortcode knows its own tag, name and description', function (string $class, string $tag) {
    // The tag is derived from the class name, and the block, the shortcode and the
    // container alias all hang off it — glsr()->shortcode($tag) is how everything else
    // finds this object.
    $shortcode = glsr($class);

    expect($shortcode->tag)->toBe($tag)
        ->and($shortcode->name)->not->toBeEmpty()
        ->and($shortcode->description)->not->toBeEmpty();
    expect(glsr()->shortcode($tag))->toBeInstanceOf($class);
})->with(everyShortcode());

test('a shortcode declares the settings its block renders, and every one is complete', function (string $class) {
    // A setting with no `type` is a control the block cannot draw, and one with no
    // `group` lands in no panel at all — either way it simply would not appear, and
    // nobody would be told why.
    $settings = glsr($class)->settings();

    expect($settings)->not->toBeEmpty();

    $incomplete = array_keys(array_filter($settings,
        fn ($setting) => !isset($setting['group'], $setting['type'])
    ));

    expect($incomplete)->toBe([]); // named, so a failure says which setting
})->with(everyShortcode());

test('a shortcode can be given settings it does not ship with', function () {
    // The filter an addon adds its own controls through.
    add_filter('site-reviews/shortcode/config/site_reviews', fn ($config) => $config + [
        'from_an_addon' => ['group' => 'general', 'type' => 'text'],
    ]);

    expect(glsr(SiteReviewsShortcode::class)->settings())->toHaveKey('from_an_addon');
    expect(glsr(SiteReviewsSummaryShortcode::class)->settings())->not->toHaveKey('from_an_addon');
});

test('every shortcode that hides things says what can be hidden', function () {
    // The `hide` option is the one setting whose options come from the shortcode itself
    // rather than from the database (ShortcodeOptionManager::hide() reaches back into
    // the shortcode's protected hideOptions()).
    expect(array_keys(glsr(SiteReviewsShortcode::class)->options('hide')))->toBe([
        'title', 'rating', 'date', 'assigned_links', 'content', 'avatar', 'author',
        'verified', 'response',
    ]);
    expect(array_keys(glsr(SiteReviewsFormShortcode::class)->options('hide')))->toBe([
        'rating', 'title', 'content', 'name', 'email', 'terms',
    ]);
    expect(array_keys(glsr(SiteReviewsSummaryShortcode::class)->options('hide')))->toBe([
        'rating', 'stars', 'summary', 'bars', 'if_empty',
    ]);
});

test('a shortcode with every field hidden has nothing left to show', function () {
    // hasVisibleFields() is what the block uses to warn somebody that they have hidden
    // the whole thing. `if_empty` does not count: it hides the shortcode when there is
    // nothing to show, which is not the same as hiding a field.
    $form = glsr(SiteReviewsFormShortcode::class);

    expect($form->hasVisibleFields(['hide' => 'rating,title,content,name,email,terms']))->toBeFalse();
    expect($form->hasVisibleFields(['hide' => 'rating,title']))->toBeTrue();

    $summary = glsr(SiteReviewsSummaryShortcode::class);
    expect($summary->hasVisibleFields(['hide' => 'rating,stars,summary,bars,if_empty']))->toBeFalse();
    expect($summary->hasVisibleFields(['hide' => 'if_empty']))->toBeTrue(); // not a field
});

/*
 * The options behind the settings.
 */

function shortcodeOptions(string $option, array $args = []): array
{
    return glsr(ShortcodeOptionManager::class)->get($option, $args);
}

test('an option nobody has written a method for is an empty list, not an error', function () {
    // ShortcodeOptionManager::get() dispatches with a ReflectionMethod and catches the
    // ReflectionException. An addon asking for an option that does not exist gets
    // nothing back, rather than a fatal on somebody's site.
    expect(shortcodeOptions('there_is_no_such_option'))->toBe([]);
});

test('the fixed option lists are what the block draws its dropdowns from', function () {
    expect(array_keys(shortcodeOptions('pagination')))->toBe(['loadmore', 'ajax', 'true']);
    expect(array_keys(shortcodeOptions('schema')))->toBe(['true', 'false']);
    expect(array_keys(shortcodeOptions('terms')))->toBe(['true', 'false']);
    expect(array_keys(shortcodeOptions('verified')))->toBe(['true', 'false']);
});

test('the review type is only offered as a choice when there is more than one', function () {
    // A site with only local reviews has nothing to filter by, and a dropdown with one
    // entry is a dropdown that should not be there. A platform addon registers its own
    // type, and then the choice is worth offering.
    //
    // `review_types` lives in the plugin's own in-memory store, which — unlike the
    // database and the hooks — Pest does not roll back. So it is put back by hand.
    $types = glsr()->retrieveAs('array', 'review_types', []);

    try {
        expect(shortcodeOptions('type'))->toBe([]); // only `local` is registered

        glsr()->store('review_types', ['local' => 'Local', 'google' => 'Google']);

        expect(shortcodeOptions('type'))->toBe(['local' => 'Local', 'google' => 'Google']);
    } finally {
        glsr()->store('review_types', $types);
    }
});

test('a placeholder is prepended to the options, but only when there are options', function () {
    // The empty-string key is the "Select a…" entry. Prepending it to an empty list
    // would give the block a dropdown with nothing in it but a placeholder.
    $options = shortcodeOptions('pagination', ['placeholder' => 'Select a pagination style...']);

    expect(array_key_first($options))->toBe('');
    expect($options[''])->toBe('Select a pagination style...');

    expect(shortcodeOptions('there_is_no_such_option', ['placeholder' => 'Select...']))->toBe([]);
});

test('the pages a review can be assigned to are searched by title', function () {
    // The token field in the block searches as you type — this is what answers it.
    createPost(['post_title' => 'The Findable Page']);
    createPost(['post_title' => 'Something Else']);

    $options = shortcodeOptions('assigned_posts', ['search' => 'Findable']);

    // the two dynamic choices come first, whatever the search found
    expect($options)->toHaveKey('post_id')
        ->and($options)->toHaveKey('parent_id')
        ->and($options)->toContain('The Findable Page')
        ->and($options)->not->toContain('Something Else');
});

test('a page already chosen is offered back even though nobody searched for it', function () {
    // Reopening the block has to show the page by name, not by id — but the field is
    // not searching for anything, it is asking "what is number 42 called?". That is what
    // `include` is for.
    $postId = createPost(['post_title' => 'Already Chosen']);

    expect(shortcodeOptions('assigned_posts', ['include' => [$postId]]))
        ->toContain('Already Chosen');
});

test('searching the assigned pages by number looks the page up rather than searching for it', function () {
    // ShortcodeApiFetchDefaults::finalize() moves a numeric search into `include`. A
    // search for "42" means the page with that id, not a page with "42" in its title.
    $postId = createPost(['post_title' => 'Found By Its Id']);

    expect(shortcodeOptions('assigned_posts', ['search' => (string) $postId]))
        ->toContain('Found By Its Id');
});

test('the categories a review can be assigned to are offered', function () {
    $termId = createTerm(['name' => 'Service', 'taxonomy' => glsr()->taxonomy]);

    $options = shortcodeOptions('assigned_terms');

    expect($options)->toHaveKey($termId);
    expect($options[$termId])->toBe('Service');
});

test('the users a review can be assigned to are offered, and so are the three that are worked out at render time', function () {
    // `user_id`, `author_id` and `profile_id` are not users — they are instructions,
    // resolved against whoever is looking at the page. They have to be offered
    // alongside the real users.
    createUser(['display_name' => 'Jane Doe']);

    $options = shortcodeOptions('assigned_users', ['search' => 'Jane']);

    expect($options)->toHaveKey('user_id')
        ->and($options)->toHaveKey('author_id')
        ->and($options)->toHaveKey('profile_id')
        ->and($options)->toContain('Jane Doe');
});

test('the review authors are offered, with only the choice that makes sense for an author', function () {
    // `author` is who WROTE the review, so "the page author" and "the profile user" are
    // meaningless here — only "the logged in user" is offered.
    createUser(['display_name' => 'Jane Doe']);

    $options = shortcodeOptions('author', ['search' => 'Jane']);

    expect($options)->toHaveKey('user_id')
        ->and($options)->not->toHaveKey('author_id')
        ->and($options)->not->toHaveKey('profile_id')
        ->and($options)->toContain('Jane Doe');
});

test('the single review shortcode offers the reviews themselves', function () {
    $review = createReview(['title' => 'A Findable Review']);
    createReview(['title' => 'Another Review']);

    expect(shortcodeOptions('post_id', ['search' => 'Findable']))
        ->toContain('A Findable Review')
        ->not->toContain('Another Review');

    // and it looks one up by id, for the block reopening on a review already chosen
    expect(shortcodeOptions('post_id', ['include' => [$review->ID]]))
        ->toContain('A Findable Review');
});

test('the options can be asked for by naming the shortcode instead of the option', function () {
    // The __call shortcut: glsr(ShortcodeOptionManager::class)->hide('site_reviews'),
    // which is what the shortcodes' own options() calls end up as. It takes a tag, an
    // instance, or the full argument array.
    $manager = glsr(ShortcodeOptionManager::class);

    expect($manager->hide('site_reviews'))
        ->toBe($manager->hide(glsr(SiteReviewsShortcode::class)))
        ->and($manager->hide('site_reviews'))
        ->toBe(glsr(SiteReviewsShortcode::class)->options('hide'));
});

test('the options can be changed by a filter', function () {
    add_filter('site-reviews/shortcode/options/pagination', fn () => ['none' => 'No pagination']);

    expect(shortcodeOptions('pagination'))->toBe(['none' => 'No pagination']);
});
