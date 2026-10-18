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

/*
 * The Paginate module itself: the numbered links and the URL bookkeeping.
 */

test('the current page url query is carried into every page link, minus the verification keys', function () {
    // A visitor lands on ?verified=…&review_id=… from a verification email, then
    // pages through the reviews: the filter args (foo) must survive on every page
    // link, and the one-shot verification keys must NOT — or every page they visit
    // would re-verify the same review.
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $_SERVER['REQUEST_URI'] = '/?foo=bar&review_id=123&verified=token';
    try {
        $paginate = new \GeminiLabs\SiteReviews\Modules\Paginate([
            // a paginate-style base, as a caller passes it; the page var it carries
            // is subtracted from the current URL's query, never duplicated into it
            'base' => home_url('/').'?'.glsr()->constant('PAGED_QUERY_VAR').'=%#%',
            'total' => 3,
            'current' => 2,
        ]);

        expect($paginate->args->add_args)->toBe(['foo' => 'bar']);
    } finally {
        $_SERVER['REQUEST_URI'] = $requestUri;
    }
});

test('over admin-ajax, the own url query of a paged request is not added to the page links', function () {
    // Over REST, REST_REQUEST marks the request URL as the endpoint's own, not the page's. Over
    // admin-ajax that constant is not defined; the stored paged request is the plugin's own sign
    // for the same thing, so the request's query is dropped on both transports alike. admin-ajax is
    // posted without a query; the URL here is given one so that the stored request, not the URL's
    // shape, is what the assertion rests on. REST_REQUEST cannot be defined in this suite, so the
    // REST branch is traced, not executed.
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $_SERVER['REQUEST_URI'] = '/wp-admin/admin-ajax.php?atts%5Bdisplay%5D=5&page=2';
    glsr()->store(glsr()->paged_handle, ['page' => 2, 'url' => 'https://example.org/reviews/?foo=bar']);
    try {
        $paginate = new \GeminiLabs\SiteReviews\Modules\Paginate([
            'add_args' => ['foo' => 'bar'],
            'base' => home_url('/reviews/').'?'.glsr()->constant('PAGED_QUERY_VAR').'=%#%',
            'total' => 3,
            'current' => 2,
        ]);
        expect($paginate->args->add_args)->toBe(['foo' => 'bar']);
    } finally {
        glsr()->discard(glsr()->paged_handle);
        $_SERVER['REQUEST_URI'] = $requestUri;
    }
});

test('an empty stored request is no paged request, and the page url query is carried', function () {
    // CreateReview stores the form's pagination atts, which a form without them leaves empty;
    // NormalizePaginationArgs treats that as absent, and Paginate must agree with it.
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $_SERVER['REQUEST_URI'] = '/?foo=bar';
    glsr()->store(glsr()->paged_handle, []);
    try {
        $paginate = new \GeminiLabs\SiteReviews\Modules\Paginate([
            'base' => home_url('/').'?'.glsr()->constant('PAGED_QUERY_VAR').'=%#%',
            'total' => 3,
            'current' => 2,
        ]);
        expect($paginate->args->add_args)->toBe(['foo' => 'bar']);
    } finally {
        glsr()->discard(glsr()->paged_handle);
        $_SERVER['REQUEST_URI'] = $requestUri;
    }
});

test('one page means no links at all', function () {
    expect((new \GeminiLabs\SiteReviews\Modules\Paginate(['total' => 1]))->links())->toBe([]);
});

test('a long run of pages is elided to dots around the current page', function () {
    $links = (new \GeminiLabs\SiteReviews\Modules\Paginate([
        'total' => 20,
        'current' => 10,
        'mid_size' => 2,
        'end_size' => 1,
    ]))->links();

    $types = array_column($links, 'type');
    expect($types)->toContain('dots')
        ->and($types)->toContain('current')
        ->and($types)->toContain('prev')
        ->and($types)->toContain('next');

    $dots = array_values(array_filter($links, fn ($link) => 'dots' === $link['type']));
    expect($dots)->toHaveCount(2) // one gap each side of the current page
        ->and($dots[0]['link'])->toContain('&hellip;');
});
