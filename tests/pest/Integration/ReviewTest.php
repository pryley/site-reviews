<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Review;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Review object itself — the read-only value object every template, tag and API response
 * is built from. CreateReviewTest proves reviews are MADE correctly; this file proves the
 * object's own surface: the magic call filter, the date formats, the custom-fields door
 * (the only writable thing on it), and the token urls.
 */

beforeEach(function () {
    resetPluginState();
});

test('an unknown method is an extension point, not a fatal', function () {
    // $review->somethingCustom() fires site-reviews/review/call/somethingCustom — how addons
    // bolt methods onto reviews. No listener: nothing comes back (the review itself is
    // returned by the unfiltered hook, and swallowed so it cannot leak as a "result").
    $review = createReview();

    expect($review->somethingCustom())->toBeNull();

    add_filter('site-reviews/review/call/somethingCustom',
        fn (Review $r, $arg) => "computed-{$arg}", 10, 2);
    expect($review->somethingCustom('x'))->toBe('computed-x');
});

test('the date follows the setting: default, custom, or relative', function () {
    $review = createReview(['date' => '2024-06-01 12:00:00']);
    $options = glsr(OptionManager::class);

    expect($review->date())->toBe(mysql2date(get_option('date_format'), '2024-06-01 12:00:00'));

    $options->set('settings.reviews.date.format', 'custom');
    $options->set('settings.reviews.date.custom', 'Y/m/d');
    expect($review->date())->toBe('2024/06/01');

    $options->set('settings.reviews.date.format', 'relative');
    expect($review->date())->toContain('ago');

    expect($review->date('d.m.Y'))->toBe('01.06.2024'); // an explicit format beats the setting
});

test('custom fields are the one writable door, and everything else is sealed', function () {
    $review = createReview(['title' => 'Sealed']);

    $review['custom'] = ['color' => 'red'];
    expect($review->custom()->color)->toBe('red');

    $review['title'] = 'Overwritten'; // offsetSet refuses anything but custom
    expect($review->title)->toBe('Sealed');

    unset($review['title']); // offsetUnset refuses everything
    expect($review->title)->toBe('Sealed');
});

test('a review renders itself as stars and html', function () {
    $review = createReview(['rating' => 4, 'content' => 'Rendered content.']);

    expect($review->rating())->toContain('glsr-star'); // the star svg markup

    ob_start();
    $review->render();
    $html = (string) ob_get_clean();
    expect($html)->toContain('Rendered content.');
    expect((string) $review)->toContain('Rendered content.'); // __toString is the same build
});

test('assigned terms degrade to nothing when the taxonomy query fails', function () {
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $review = createReview(['assigned_terms' => $termId]);

    expect($review->assignedTerms())->toHaveCount(1);

    add_filter('get_terms', fn () => new WP_Error('boom', 'query failed'));
    expect($review->assignedTerms())->toBe([]);
});

test('a verified review with a verification date offers no verify url', function () {
    // The verify link is for reviews still awaiting verification; on an already-verified
    // review it must be nothing at all, not a link that re-verifies.
    $review = createReview();
    expect($review->verifyUrl())->toContain(glsr()->prefix.'='); // awaiting: a token url

    glsr(ReviewManager::class)->updateRating($review->ID, ['is_verified' => true]);
    update_post_meta($review->ID, '_verified_on', current_time('mysql'));
    $verified = glsr(ReviewManager::class)->get($review->ID, true);

    expect($verified->is_verified)->toBeTrue()
        ->and($verified->verifyUrl())->toBe('');
});

test('the author is formatted by the name settings', function () {
    $review = createReview(['name' => 'Jane Marie Doe']);
    glsr(OptionManager::class)->set('settings.reviews.name.format', 'first');

    expect($review->author())->toBe('Jane');
});

test('assigned users resolve to user objects', function () {
    $userId = createUser();
    $review = createReview(['assigned_users' => $userId]);

    $users = $review->assignedUsers();
    expect($users)->toHaveCount(1)
        ->and((int) $users[0]->ID)->toBe($userId);

    expect(createReview()->assignedUsers())->toBe([]);
});
