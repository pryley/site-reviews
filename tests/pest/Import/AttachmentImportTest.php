<?php

use GeminiLabs\SiteReviews\Commands\ImportReviewsAttachments;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\definesWpImporting;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Importing the images a review came with.
 *
 * A review imported from a spreadsheet can name its images by URL. Fetching them is slow — an HTTP
 * request and an uploads write each — so it is a second, paged pass driven from the browser (page
 * 1, then 2, watching a progress bar), not done while the import runs.
 *
 * THIS FILE IS IN THE IMPORT SUITE, AND HAS TO BE. ImportManager::importAttachments() defines
 * WP_IMPORTING, which PHP cannot undo; the plugin reads it in fourteen places to mean "not a form
 * submission", so every later test would get no avatar, no verification email, no recalculated
 * counts, no cache flush, and unprotected is_pinned / is_verified / ip_address. phpunit.xml
 * declares the Import suite LAST so this may poison a process nobody uses afterwards, and Pest.php
 * throws if any test outside this directory defines the constant.
 */

beforeEach(function () {
    definesWpImporting(); // importAttachments() does, and it is why this file is in this suite
    resetPluginState();
});

test('importing attachments reports how many it did, a page at a time', function () {
    // Paged: the importer is called repeatedly from the browser, one page each. The fetching
    // itself hangs off the import/reviews/attachments filter — what the command owns is the
    // paging math handed TO that filter and the counted result the progress bar reads back.
    $captured = [];
    add_filter('site-reviews/import/reviews/attachments', function ($result, $limit, $offset) use (&$captured) {
        $captured = compact('limit', 'offset');
        return ['attachments' => 3, 'imported' => 2, 'skipped' => 1];
    }, 10, 3);
    $command = new ImportReviewsAttachments(new Request(['page' => 2, 'per_page' => 10]));

    $command->handle();

    expect($captured)->toBe(['limit' => 10, 'offset' => 10]); // page 2 of 10 starts at 10
    $response = $command->response();
    expect($response['attachments'])->toBe(3)
        ->and($response['imported'])->toBe(2)
        ->and($response['skipped'])->toBe(1)
        ->and($response['message'])->toBe('Imported %1$d of %2$d'); // ImportResultDefaults' default
});

test('a page and a per-page of nothing are still a page of one', function () {
    // max(1, …) on both: a per_page of 0 imports nothing forever while claiming progress, and an
    // offset computed from page 0 would be negative.
    $captured = [];
    add_filter('site-reviews/import/reviews/attachments', function ($result, $limit, $offset) use (&$captured) {
        $captured = compact('limit', 'offset');
        return $result;
    }, 10, 3);
    $command = new ImportReviewsAttachments(new Request(['page' => 0, 'per_page' => 0]));

    $command->handle();

    expect($captured)->toBe(['limit' => 1, 'offset' => 0]); // clamped, not negative
    expect($command->response()['attachments'])->toBe(0); // a page of nothing imports nothing
});
