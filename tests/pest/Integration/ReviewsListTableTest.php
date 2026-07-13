<?php

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Overrides\ReviewsListTable;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The reviews screen itself — WordPress's posts list table, subclassed.
 *
 * ListTableController decides WHAT is in the table (which columns, which rows, what order).
 * This decides how it is DRAWN, and the interesting part is what it hides in the page.
 *
 * Quick Edit works by printing a hidden copy of every row's editable data into the page —
 * WordPress does it for the title and the slug, and this does it for the review's CONTENT and
 * the site owner's RESPONSE. So the reviews screen carries, in its HTML, the full text of every
 * review on the page, for anybody the browser hands the page to.
 *
 * That is fine, and it is what makes Quick Edit work. What matters is that it is only printed
 * for somebody who is allowed to change it — a moderator who may respond. Print it for everybody
 * with access to the screen and you have quietly published the unapproved reviews and the draft
 * responses to whoever can see the list.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
});

afterEach(function () {
    set_current_screen('front');
    glsr(Notice::class)->clear();
});

function reviewsListTable(): ReviewsListTable
{
    return new ReviewsListTable(['screen' => get_current_screen()]);
}

function printedByTable(callable $callback): string
{
    ob_start();
    $callback();

    return (string) ob_get_clean();
}

/*
 * The title column.
 */

test('the review title is wrapped so the screen can lay it out', function () {
    // WordPress prints <strong>Title</strong>; the reviews screen needs it in a block of its
    // own, because a review row carries a rating and an excerpt underneath it.
    $review = createReview(['title' => 'A lovely stay']);
    $table = reviewsListTable();

    $html = printedByTable(fn () => $table->column_title(get_post($review->ID)));

    expect($html)->toContain('<div class="review-title"><strong>')
        ->and($html)->toContain('A lovely stay')
        ->and($html)->toContain('</strong></div>');
});

/*
 * The hidden data behind Quick Edit. THIS IS THE ONE.
 */

test('a moderator gets the review and the response, hidden in the page for quick edit', function () {
    $review = createReview(['content' => 'The room was lovely.']);
    glsr(ReviewManager::class)->updateResponse($review->ID, ['response' => 'Thank you!']);
    $table = reviewsListTable();

    $html = printedByTable(fn () => $table->column_title(get_post($review->ID)));

    expect($html)->toContain('The room was lovely.')
        ->and($html)->toContain('Thank you!');
});

test('somebody who may not respond is not given the review to edit', function () {
    // A contributor can reach the reviews screen. They cannot respond to a review, so the
    // response — which may be a draft, or a reply the site owner has thought better of — has no
    // business being in the HTML of a page served to them.
    $review = createReview(['content' => 'The room was lovely.']);
    glsr(ReviewManager::class)->updateResponse($review->ID, ['response' => 'A draft reply nobody has approved.']);
    wp_set_current_user(createUser(['role' => 'contributor']));
    $table = reviewsListTable();

    $html = printedByTable(fn () => $table->column_title(get_post($review->ID)));

    expect($html)->not->toContain('A draft reply nobody has approved.');
});

test('a review cannot smuggle markup onto the moderator\'s screen', function () {
    // This is the one place on the site where an unapproved review's text is rendered to a
    // logged-in administrator — inside a <textarea>, in their session, on an admin screen. A
    // review that closed the textarea would put whatever followed into the page as markup.
    //
    // It cannot, and it is stopped TWICE. The script never reaches the database at all: the
    // review is sanitized on the way IN, so what is stored is "Nice!" and nothing else. And
    // whatever is stored is then esc_textarea()'d on the way out. Belt, and braces.
    $review = createReview([
        'content' => 'Nice!</textarea><script>alert(document.cookie)</script>',
    ]);

    $stored = glsr_get_review($review->ID)->content;
    expect($stored)->toBe('Nice!'); // it never got in

    $table = reviewsListTable();
    $html = printedByTable(fn () => $table->column_title(get_post($review->ID)));

    expect($html)->not->toContain('<script')
        ->and($html)->not->toContain('</textarea><script>');
});

/*
 * The rest of the screen.
 */

test('the notices are drawn above the table', function () {
    glsr(Notice::class)->addWarning('Something needs your attention.');
    $table = reviewsListTable();
    $table->prepare_items(); // parent::views() reads $avail_post_stati, which this populates

    $html = printedByTable(fn () => $table->views());

    expect($html)->toContain('id="glsr-notices"')
        ->and($html)->toContain('Something needs your attention.');
});

test('quick edit offers the review columns, and the author dropdown to somebody who may reassign', function () {
    $table = reviewsListTable();
    $table->prepare_items();

    $html = printedByTable(fn () => $table->inline_edit());

    expect($html)->toContain('post_author'); // may edit others' posts
});

test('quick edit does not offer to reassign the author to somebody who may not', function () {
    wp_set_current_user(createUser(['role' => 'contributor']));
    $table = reviewsListTable();
    $table->prepare_items();

    $html = printedByTable(fn () => $table->inline_edit());

    expect($html)->not->toContain('name="post_author"');
});
