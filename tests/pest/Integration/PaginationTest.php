<?php

use GeminiLabs\SiteReviews\Modules\Html\Partials\Pagination;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The pagination partial that sits under a set of reviews. It draws one of two things depending on
 * the shortcode's `pagination` setting: numbered page links (covered through the public controller)
 * or a "Load more" button that fetches the next page over ajax. This pins the load-more branch,
 * which the numbered path never reaches — including the one case where it draws nothing, because
 * there is nothing left to load.
 */

beforeEach(fn () => resetPluginState());

test('the load-more button is drawn while there are more pages to fetch', function () {
    $html = (new Pagination())->build([
        'type' => 'loadmore',
        'total' => 3,   // three pages of reviews
        'current' => 1, // on the first, so there is more to load
    ]);

    expect($html)->toContain('glsr-button-loadmore')
        ->and($html)->toContain('Load more')
        ->and($html)->toContain('data-page="2"'); // the button asks for the NEXT page
});

test('there is no load-more button once the last page has been reached', function () {
    $html = (new Pagination())->build([
        'type' => 'loadmore',
        'total' => 2,
        'current' => 2, // already on the last page — nothing more to fetch
    ]);

    expect($html)->toBe('');
});

test('a single page of reviews gets no pagination at all', function () {
    // The guard shared by both types: one page (or none) needs no controls whatever the setting.
    expect((new Pagination())->build(['type' => 'loadmore', 'total' => 1]))->toBe('');
});
