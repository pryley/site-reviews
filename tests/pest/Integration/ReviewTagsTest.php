<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewLocationTag;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewResponseTag;
use GeminiLabs\SiteReviews\Review;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Two of the template tags: where the reviewer was, and what the site said back.
 *
 * LOCATION is geolocation made visible, with the most ways to be wrong — the data comes from an IP
 * lookup and every field can be missing, meaningless, or both:
 *
 *   - the flag is an SVG on disk chosen by country code. 255 files, ~200 countries, so an unexpected
 *     lookup result has no file — a broken image is worse than no flag.
 *   - the region is shown only for the US, because "Bavaria" is not what anyone outside Germany means
 *     by a region, and the lookup returns a NUMBER for many countries.
 *
 * RESPONSE is the site owner's reply, printed under the review as "Response from <site name>". The
 * name is filterable because on a WooCommerce store it should be the shop's, not the blog's.
 */

beforeEach(function () {
    resetPluginState();
    glsr(OptionManager::class)->set('settings.reviews.geolocation', 'yes');
});

/**
 * A review that the geolocation lookup has already been run against.
 */
function reviewFromLocation(array $location): Review
{
    $review = createReview();
    glsr(PostMeta::class)->set($review->ID, 'geolocation', $location);

    return glsr_get_review($review->ID);
}

/**
 * The location tag, rendered for a review, the way Template does it.
 */
function locationTag(Review $review, string $format = 'flag'): string
{
    glsr(OptionManager::class)->set('settings.reviews.geolocation_format', $format);

    return (new ReviewLocationTag('location'))->handleFor('review', '', $review);
}

/*
 * The flag.
 */

test('a reviewer\'s country is shown as its flag', function () {
    $review = reviewFromLocation(['country' => 'US', 'region' => 'CA', 'city' => 'San Francisco']);

    expect(locationTag($review, 'flag'))
        ->toContain('glsr-flag')
        ->toContain('assets/images/flags/US.svg')
        ->toContain('alt="US"');
});

test('a country with no flag on disk shows nothing, rather than a broken image', function () {
    // The lookup is a third party's, and it can answer with anything — an empty code, a code for
    // a territory that has no flag file, or something that is not a country code at all. There
    // are 255 SVGs and rather more possible answers.
    $review = reviewFromLocation(['country' => 'ZZ']);

    expect(locationTag($review, 'flag'))->not->toContain('<img');
});

test('a review the lookup never ran against shows nothing at all', function () {
    // Every review created before geolocation was switched on, and every review from a local or
    // private IP address. This is the common case on a site that has just enabled the feature.
    expect(locationTag(createReview(), 'flag'))->toBe('');
});

/*
 * The region, which is only meaningful in one country.
 */

test('a US reviewer is shown their city and state', function () {
    $review = reviewFromLocation(['country' => 'US', 'region' => 'CA', 'city' => 'San Francisco']);

    expect(locationTag($review, 'city_region'))->toContain('San Francisco, CA');
});

test('a reviewer outside the US is shown their city, and not their region', function () {
    // "Nordrhein-Westfalen" beneath a review is noise, and the region a lookup returns for most
    // countries is an administrative division nobody recognises by name.
    $review = reviewFromLocation(['country' => 'DE', 'region' => 'NW', 'city' => 'Köln']);

    $rendered = locationTag($review, 'city_region');

    expect($rendered)->toContain('Köln')
        ->and($rendered)->not->toContain('NW');
});

test('a region that comes back as a number is dropped, even in the US', function () {
    // Which the lookup does. "San Francisco, 06" is not an address anybody has ever written.
    $review = reviewFromLocation(['country' => 'US', 'region' => '06', 'city' => 'San Francisco']);

    $rendered = locationTag($review, 'city_region');

    expect($rendered)->toContain('San Francisco')
        ->and($rendered)->not->toContain('06');
});

/*
 * The formats, and the one that does not exist.
 */

test('the flag and the country are shown together when that is what was asked for', function () {
    $review = reviewFromLocation(['country' => 'US', 'region' => 'CA', 'city' => 'San Francisco']);

    $rendered = locationTag($review, 'flag_country');

    expect($rendered)->toContain('US.svg')
        ->toContain('&nbsp;');
});

test('the flag, the city and the region are shown together when that is what was asked for', function () {
    $review = reviewFromLocation(['country' => 'US', 'region' => 'CA', 'city' => 'San Francisco']);

    expect(locationTag($review, 'flag_city_region'))
        ->toContain('US.svg')
        ->toContain('San Francisco, CA');
});

test('a format nobody implemented renders nothing, rather than calling a method that is not there', function () {
    // The format is a stored SETTING, and the method it names is built from it by string
    // concatenation. A setting left behind by an older version — or by an addon that has been
    // deactivated — would otherwise be a fatal on every review on the site.
    $review = reviewFromLocation(['country' => 'US']);

    expect(locationTag($review, 'a_format_that_does_not_exist'))->toBe('');
});

test('the location is not shown at all when geolocation is switched off', function () {
    // The reviewer's city is personal data. A site that has turned the feature off must not print
    // it because the meta happens to still be on the review from before.
    $review = reviewFromLocation(['country' => 'US', 'city' => 'San Francisco']);
    glsr(OptionManager::class)->set('settings.reviews.geolocation', 'no');

    expect(locationTag($review, 'city_region'))->toBe('');
});

/*
 * The response.
 */

test('a response is printed under the review, saying who it is from', function () {
    $review = createReview();

    $rendered = (new ReviewResponseTag('response'))
        ->handleFor('review', 'Thank you for the kind words!', $review);

    expect($rendered)->toContain('glsr-review-response-inner')
        ->toContain('Response from '.get_bloginfo('name'))
        ->toContain('Thank you for the kind words!');
});

test('a review with no response prints nothing, not an empty box', function () {
    // Most reviews have no response, so this is the path that runs on nearly every review on
    // nearly every site. An empty response box under each one would be a visible bug everywhere.
    expect((new ReviewResponseTag('response'))->handleFor('review', '', createReview()))->toBe('');
});

test('a store can say the response came from the shop, not from the blog', function () {
    // get_bloginfo('name') is "My WordPress Site" on more stores than anybody would like. The
    // filter is how the WooCommerce integration puts the shop's name there instead.
    add_filter('site-reviews/review/build/tag/response/by', fn () => 'The Corner Shop');

    expect((new ReviewResponseTag('response'))->handleFor('review', 'Thanks!', createReview()))
        ->toContain('Response from The Corner Shop');
});
