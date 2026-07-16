<?php

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The reviews collection, and the search behind the "Assigned Users" box.
 *
 * Reviews is what every shortcode, block and widget is handed — an ArrayObject of Review models that
 * also knows the TOTAL (not the same as its own count, since it holds one page) and so how many
 * pages there are. Wrong, and pagination either hides reviews or offers empty pages.
 *
 * SearchAssignedUsers is the review editor's autocomplete: type three letters, get the users you
 * could assign to. It searches login, display name and nickname, because people do not know which
 * they are looking at.
 */

beforeEach(function () {
    resetPluginState();
});

/*
 * The collection.
 */

test('a page of reviews knows how many pages there are, which is not how many it holds', function () {
    // The collection holds ONE PAGE. `total` is every review the query matched — so a collection of
    // 5 reviews out of 12, displayed 5 at a time, is page one of three. A max_num_pages computed
    // from count($reviews) would say "one page" and the other seven reviews would be unreachable.
    $reviews = new Reviews([createReview(), createReview()], 12, ['display' => 5]);

    expect($reviews->total)->toBe(12)
        ->and($reviews->max_num_pages)->toBe(3)   // ceil(12 / 5)
        ->and(count($reviews))->toBe(2);          // …and it still holds only what it was given
});

test('a page size that divides exactly does not produce an empty last page', function () {
    // ceil(10 / 5) is 2, not 3. An off-by-one here is a "next" link that leads to nothing.
    expect((new Reviews([], 10, ['display' => 5]))->max_num_pages)->toBe(2);
    expect((new Reviews([], 11, ['display' => 5]))->max_num_pages)->toBe(3);
    expect((new Reviews([], 0, ['display' => 5]))->max_num_pages)->toBe(0);
});

test('the collection is indexed like an array, and read like an object', function () {
    // Both, and this is why offsetGet is overridden: the templates do `$reviews[0]` AND
    // `$reviews->total`, and an ArrayObject with ARRAY_AS_PROPS would otherwise look for a review
    // at index "total".
    $review = createReview(['content' => 'The room was lovely.']);
    $reviews = new Reviews([$review], 1, ['display' => 5]);

    expect($reviews[0])->toBeInstanceOf(Review::class)
        ->and($reviews[0]->content)->toBe('The room was lovely.');
    expect($reviews['total'])->toBe(1)
        ->and($reviews['max_num_pages'])->toBe(1);
});

test('and asking it for something that is neither is nothing, rather than an error', function () {
    // The templates are written by hand, by people, and a typo in one must not be a fatal on the
    // front end of somebody's site.
    $reviews = new Reviews([], 0, ['display' => 5]);

    expect($reviews['a_key_that_does_not_exist'])->toBeNull()
        ->and($reviews[99])->toBeNull();
});

test('a collection renders itself, and can be echoed', function () {
    // `echo $reviews` is what the shortcodes do. __toString is the whole reason it works.
    createReview(['content' => 'The room was lovely.']);
    $reviews = glsr_get_reviews();

    expect($reviews->build())->toBeInstanceOf(ReviewsHtml::class);
    expect((string) $reviews)->toContain('The room was lovely.');

    ob_start();
    $reviews->render();

    expect((string) ob_get_clean())->toContain('The room was lovely.');
});

test('the collection carries the arguments it was built with, for the javascript', function () {
    // These become the data attributes on the wrapper, and the paged-reviews ajax route rebuilds
    // the shortcode from them. A collection that forgot them would paginate into a different query.
    $reviews = new Reviews([], 0, ['display' => 3, 'rating' => 4]);

    expect($reviews->args['display'])->toBe(3);
    expect($reviews->attributes())->toBeArray();
});

/*
 * The assigned-users search.
 */

test('a user with a review assigned to them can be found by name', function () {
    // The autocomplete on the review editor. It searches only among users who ALREADY have a review
    // assigned — this is the box for finding the reviewer, not for browsing the whole user list of
    // a site with fifty thousand members.
    $userId = createUser(['display_name' => 'Jane Doe', 'user_login' => 'janed']);
    createReview(['assigned_users' => $userId]);

    $found = glsr(Database::class)->searchAssignedUsers('Jane')->users();

    expect($found)->toHaveCount(1)
        ->and($found[0]->ID)->toBe($userId);
});

test('and can be found by login or nickname, because people do not know which they are looking at', function () {
    $userId = createUser(['display_name' => 'Jane Doe', 'user_login' => 'janed']);
    createReview(['assigned_users' => $userId]);

    expect(glsr(Database::class)->searchAssignedUsers('janed')->users())->toHaveCount(1);
});

test('a user with no reviews assigned to them is not offered', function () {
    // They are not a reviewer. Offering them would let somebody assign a review to a person who
    // never wrote one, from a box that exists to find the person who did.
    createUser(['display_name' => 'Nobody Atall']);

    expect(glsr(Database::class)->searchAssignedUsers('Nobody')->users())->toBe([]);
});

test('a search that matches nothing is empty, not everything', function () {
    // The failure mode worth naming: a LIKE with an empty term matches every row in the table.
    $userId = createUser(['display_name' => 'Jane Doe']);
    createReview(['assigned_users' => $userId]);

    expect(glsr(Database::class)->searchAssignedUsers('zzzz-nobody')->users())->toBe([]);
});

test('a user who has since been deleted is skipped rather than returned as nothing', function () {
    // The assigned_users table can outlive a user — the row is keyed by id, and WordPress deletes
    // users without asking the plugin. get_user_by() returns false, and a false in this list would
    // be a fatal in the template that renders it.
    $userId = createUser(['display_name' => 'Jane Doe']);
    createReview(['assigned_users' => $userId]);
    wp_delete_user($userId);

    expect(glsr(Database::class)->searchAssignedUsers('Jane')->users())->toBe([]);
});
