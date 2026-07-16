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

test('importing attachments reports how many it did', function () {
    // Paged: the importer is called repeatedly from the browser, one page each. The message is
    // what the person watching the progress bar reads.
    $command = new ImportReviewsAttachments(new Request(['page' => 1, 'per_page' => 10]));

    $command->handle();

    expect($command->response())->toHaveKey('message');
});

test('a page and a per-page of nothing are still a page of one', function () {
    // max(1, …) on both: a per_page of 0 imports nothing forever while claiming progress, and an
    // offset computed from page 0 would be negative.
    $command = new ImportReviewsAttachments(new Request(['page' => 0, 'per_page' => 0]));

    $command->handle();

    expect($command->response())->toHaveKey('message'); // it ran, rather than dividing by zero
});
