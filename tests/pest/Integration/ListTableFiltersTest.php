<?php

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedPost;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedUser;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterTerms;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterType;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueType;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The dropdowns above the reviews table.
 *
 * Four filters, two kinds:
 *
 *   a FIXED list      Terms and Type. Every choice is known up front, so the dropdown holds them all
 *                     and `selected()` is a lookup.
 *   a DYNAMIC list    Assigned Post and Assigned User. The choices are every post and user on the
 *                     site — tens of thousands — so the dropdown is an ajax search box holding only
 *                     "any", "none" and whatever is selected. That last part breaks: the selected
 *                     value arrives from the URL as an ID and must be turned back into a NAME, or the
 *                     person filtering by a page sees 4,271 where the title should be.
 *
 * Every filter reads its value with filter_input(INPUT_GET, …), which is why none of this could be
 * tested before the suite shadowed that function.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
});

afterEach(function () {
    $_GET = [];
    set_current_screen('front');
});

/**
 * The filter, with the value the admin's URL would have carried.
 */
/**
 * A filter, with the value it would have been given by the URL.
 *
 * The value is UNSET when none is given, rather than left alone: every filter of a given kind
 * answers to the same query key, so a value left behind by an earlier call would be read by the
 * next filter built — and selected() reads $_GET when it is CALLED, not when the filter is built.
 */
function columnFilter(string $class, $value = null): object
{
    $filter = new $class();
    if (null === $value) {
        unset($_GET[$filter->name()]);
    } else {
        $_GET[$filter->name()] = $value;
    }

    return $filter;
}

dataset('filters', [
    'assigned post' => [ColumnFilterAssignedPost::class, 'assigned_post'],
    'assigned user' => [ColumnFilterAssignedUser::class, 'assigned_user'],
    'terms' => [ColumnFilterTerms::class, 'terms'],
    'type' => [ColumnFilterType::class, 'type'],
]);

/*
 * What every filter has to have.
 */

test('every filter names itself after its own class, which is what the URL carries', function (
    string $class, string $name
) {
    // name() is derived by reflection — ColumnFilterAssignedPost becomes `assigned_post` — and it
    // is both the query arg in the URL and the key the list table reads back. A rename here
    // silently detaches the dropdown from the query it is supposed to drive.
    expect(columnFilter($class)->name())->toBe($name);
})->with('filters');

test('every filter has a label, a title and a placeholder', function (string $class) {
    // The label is what a screen reader announces, the title is the column heading, and the
    // placeholder is what the box says when nothing is chosen. An empty one of any of them is a
    // dropdown nobody can identify.
    $filter = columnFilter($class);

    expect($filter->label())->not->toBeEmpty()
        ->and($filter->title())->not->toBeEmpty()
        ->and($filter->placeholder())->not->toBeEmpty();
})->with('filters');

test('every filter renders something to click on', function (string $class) {
    createReview();

    expect(columnFilter($class)->render())->not->toBeEmpty();
})->with('filters');

test('a filter nobody has used shows its placeholder, not a blank box', function (string $class) {
    expect(columnFilter($class)->selected())->toBe(columnFilter($class)->placeholder());
})->with('filters');

/*
 * The dynamic ones, where the id has to become a name.
 */

test('filtering by a page shows the page\'s TITLE, not its id', function () {
    // The URL carries `assigned_post=4271`. The box has to say "Our Hotel", because the person
    // reading it chose a page, not a number — and the dropdown is a search box, so the title is
    // not in the option list waiting to be looked up. It is fetched.
    $postId = createPost(['post_title' => 'Our Hotel']);

    expect(columnFilter(ColumnFilterAssignedPost::class, $postId)->selected())->toBe('Our Hotel');
});

test('filtering by a person shows their NAME, not their id', function () {
    $userId = createUser(['display_name' => 'Jane Doe']);

    expect(columnFilter(ColumnFilterAssignedUser::class, $userId)->selected())->toBe('Jane Doe');
});

test('filtering by "none" is not the same as filtering by nothing', function () {
    // 0 means "reviews assigned to no post at all", which is a real and useful thing to ask for.
    // An empty value means "do not filter". They must not collapse into each other — `0` is
    // is_numeric and empty at the same time, which is exactly the trap.
    // Read EAGERLY, one at a time. selected() reads $_GET when it is called, not when the filter is
    // built, and both filters answer to the same query key — so holding two of them at once means
    // the second one's value is what the first one reports. There is only ever one of these on the
    // screen, and the test has to be the screen.
    $any = columnFilter(ColumnFilterAssignedPost::class)->selected();
    $noneFilter = columnFilter(ColumnFilterAssignedPost::class, 0);
    $none = $noneFilter->selected();

    expect($none)->toBe($noneFilter->options()[0])
        ->and($none)->not->toBe($any);
});

test('a post id that no longer exists does not put an empty box on the screen', function () {
    // Somebody deletes the page a review was assigned to, and the filter is still in their URL —
    // or in a bookmark. get_the_title() of a missing post is an empty string.
    $filter = columnFilter(ColumnFilterAssignedPost::class, 999999);

    expect($filter->selected())->toBeString(); // and it does not fatal
});

test('a user id that no longer exists falls back rather than showing nothing', function () {
    $filter = columnFilter(ColumnFilterAssignedUser::class, 999999);

    expect($filter->selected())->toBe($filter->placeholder());
});

/*
 * The fixed ones.
 */

test('the terms filter can ask for the reviews that did NOT accept', function () {
    // Both directions are useful, and `0` is the one that matters: it is how a site owner finds
    // the reviews that were recorded without consent.
    $filter = columnFilter(ColumnFilterTerms::class);

    expect($filter->options())->toHaveKeys([0, 1])
        ->and($filter->options()[0])->toContain('not accepted');
});

test('the review type filter offers whatever review types are installed', function () {
    // `review_types` is a register an addon writes to. On a free site there is only `local`, and
    // the filter is drawn from the register rather than from a list, so an addon's reviews become
    // filterable the moment it is activated.
    glsr()->store('review_types', ['local' => 'Local', 'google' => 'Google']);

    expect(columnFilter(ColumnFilterType::class)->options())->toHaveKey('google');
});

/*
 * And the type column itself.
 */

test('a local review says so in words, and an imported one gets its platform badge', function () {
    // The badge is an SVG on disk, named after the platform. A review from a platform with no
    // badge must still say WHAT it is, rather than showing a broken image.
    $local = createReview();

    expect(glsr(ColumnValueType::class)->handle($local))->toBe($local->type());
});
