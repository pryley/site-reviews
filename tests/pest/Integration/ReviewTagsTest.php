<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewContentTag;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewLocationTag;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewResponseTag;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTitleTag;
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

    // The value is the flag and the country code, joined by a non-breaking space.
    expect($rendered)->toContain('US.svg')
        ->toContain('&nbsp;US')
        ->not->toContain('San Francisco');
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

    // The label precedes the value, so a label and a value share every tag's shape: notice, label, value.
    expect($rendered)->toBe(
        '<div class="glsr-review-response">'.
        '<span class="glsr-tag-label">Response from '.get_bloginfo('name').'</span>'.
        '<div class="glsr-tag-value" data-expanded="false"><p>Thank you for the kind words!</p></div>'.
        '</div>'
    );
});

test('a response curls its quotes as the review text does, and so does its label', function () {
    // WordPress texturizes post titles and content; the review's content already curls through
    // Text::normalize, so typed quotes read the same in every part of the review. The site name
    // is stored escaped, so only a name set through the filter can carry typed quotes.
    add_filter('site-reviews/review/build/tag/response/by', fn () => 'Paul\'s "Shop" -- est. 1999');

    expect((new ReviewResponseTag('response'))->handleFor('review', 'We said "thanks" -- twice', createReview()))
        ->toContain('We said &#8220;thanks&#8221; &#8212; twice')
        ->toContain('Response from Paul&#8217;s &#8220;Shop&#8221; &#8212; est. 1999');
});

test('a title curls its quotes, and an empty title still reads "No Title"', function () {
    $review = createReview();

    expect((new ReviewTitleTag('title'))->handleFor('review', 'A "great" product', $review))
        ->toContain('A &#8220;great&#8221; product');
    expect((new ReviewTitleTag('title'))->handleFor('review', '', $review))
        ->toContain('No Title');
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

/*
 * The tag machinery itself, at its edges.
 */

test('a review tag given anything but a review renders nothing', function () {
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewRatingTag('rating');

    expect($tag->handleFor('review', '5', 'not-a-review'))->toBe('');
});

test('a raw tag ignores the hide options and the display settings', function () {
    $review = createReview(['rating' => 5]);
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewRatingTag('rating', ['hide' => ['rating'], 'raw' => true]);
    $cooked = new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewRatingTag('rating');
    // A declared display setting that is switched off. The cooked tag obeys it; the raw tag does not.
    glsr(OptionManager::class)->set('settings.reviews.geolocation', 'no');

    expect($tag->isEnabled('reviews.rating'))->toBeTrue()
        ->and($tag->isHidden())->toBeFalse()
        ->and($cooked->isEnabled('reviews.geolocation'))->toBeFalse()
        ->and($tag->isEnabled('reviews.geolocation'))->toBeTrue()
        ->and($tag->isHidden('reviews.geolocation'))->toBeFalse();
});

test('a tag with no handler of its own hands back its trimmed value', function () {
    // The Tag base defaults: validate() accepts anything, handle() is the value.
    $tag = new class('anything') extends \GeminiLabs\SiteReviews\Modules\Html\Tags\Tag {
    };

    expect($tag->handleFor('review', '  the value  ', null))->toBe('the value');
});

test('the review type tag names any type except the default', function () {
    $review = createReview();
    $tag = fn () => new \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewTypeTag('type');

    // a local review is the norm and is not labelled
    expect($tag()->handleFor('review', 'local', $review))->toBe('');
    // anything else is labelled with the review's registered type name
    expect($tag()->handleFor('review', 'imported', $review))->toContain('glsr-review-type');
});

test('a hidden summary rating renders nothing', function () {
    $tag = new \GeminiLabs\SiteReviews\Modules\Html\Tags\SummaryRatingTag('rating', ['hide' => ['rating']]);

    expect($tag->handleFor('summary', '', [0, 0, 1, 2, 5]))->toBe('');
});

test('a tag with no class of its own is escaped', function () {
    // {{ custom }} is every custom field, serialized, and has no tag class. The serialized
    // string always holds double quotes, which end an attribute if they are not escaped.
    $review = createReview(['xyz' => 'value']);
    add_filter('site-reviews/build/template/review', fn ($template) => $template.'<i data-custom="{{ custom }}"></i>');

    expect((string) $review->build())
        ->toContain('data-custom="a:1:{s:3:&quot;xyz&quot;;s:5:&quot;value&quot;;}"');
});

test('a tag written in the review content stays text', function () {
    $review = createReview([
        'content' => 'Before {custom} and {{ email }} after.',
        'secret' => 'value',
    ]);

    expect((string) $review->build())->toContain('Before {custom} and {{ email }} after.');
});

/*
 * THE LABEL AND NOTICE SLOTS. Every tag has both, empty until a feature fills them through the
 * tag's label and notice filters; the response names its own label. The notice sits first in the
 * tag, then the label, then the value, so a message about an edit stands with the content and one
 * about a reply stands with the response.
 */

test('a label added through the label filter is printed before the value, and not without one', function () {
    // The Forms addon labels a tag after the field it came from.
    add_filter('site-reviews/review/label/content', fn () => 'Your experience');

    $review = createReview();

    expect((string) $review->build())->toContain(
        '<div class="glsr-review-content"><span class="glsr-tag-label">Your experience</span><div class="glsr-tag-value" data-expanded="false">'
    );
    expect((new ReviewContentTag('content'))->handleFor('review', '', $review))->toBe('');
});

test('the label filter without a tag name runs first and sees the tag', function () {
    // A plugin that labels many tags hooks the general filter once; the tag tells it which one.
    add_filter('site-reviews/review/label', fn (string $label, $tag) => 'response' === $tag->tag ? 'They said' : $label, 10, 2);
    add_filter('site-reviews/review/label/response', fn (string $label) => $label.':');

    expect((new ReviewResponseTag('response'))->handleFor('review', 'Thanks!', createReview()))
        ->toContain('<span class="glsr-tag-label">They said:</span>');
});

test('a review with nothing to say to its viewer prints no notice', function () {
    expect((string) createReview()->build())->not->toContain('glsr-tag-notice');
});

test('a notice added through the notice filter is printed inside the tag, before its value', function () {
    $review = createReview();
    add_filter('site-reviews/review/notice/content', fn (string $notice) => $notice.'<p>Your changes are awaiting moderation.</p>');

    expect((string) $review->build())->toContain(
        '<div class="glsr-review-content">'.
        '<div class="glsr-tag-notice"><p>Your changes are awaiting moderation.</p></div>'.
        '<div class="glsr-tag-value" data-expanded="false">'
    );
});

test('a notice is printed for a tag with no value, with no label', function () {
    // A reply held for moderation is the case: the review has no response yet, but its sender
    // must be told where their reply stands. The box holds the notice alone, since the label
    // names a response that is not there.
    add_filter('site-reviews/review/notice/response', fn () => '<p>Your reply is awaiting moderation.</p>');

    expect((new ReviewResponseTag('response'))->handleFor('review', '', createReview()))
        ->toBe('<div class="glsr-review-response"><div class="glsr-tag-notice"><p>Your reply is awaiting moderation.</p></div></div>');
});

test('a raw value carries no notice', function () {
    // The Designer's previews read raw values, and a message for one viewer is not a value.
    add_filter('site-reviews/review/notice/content', fn () => '<p>Your changes are awaiting moderation.</p>');

    expect((new ReviewContentTag('content', ['raw' => true]))->handleFor('review', 'Lovely.', createReview()))
        ->not->toContain('glsr-tag-notice');
});
