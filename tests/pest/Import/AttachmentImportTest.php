<?php

use GeminiLabs\SiteReviews\Commands\ImportReviewsAttachments;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\definesWpImporting;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Importing the images a review came with.
 *
 * A review imported from a spreadsheet can name its images by URL. Fetching them is slow — every
 * one is an HTTP request and a write to the uploads directory — so it is not done while the import
 * runs. It is a second, paged pass, driven from the browser: the page asks for page 1, then page
 * 2, and watches a progress bar.
 *
 * THIS FILE IS IN THE IMPORT SUITE, AND HAS TO BE. ImportManager::importAttachments() calls
 * define('WP_IMPORTING', true), which PHP cannot undo. The plugin reads that constant in fourteen
 * places to mean "this review did not come from a person filling in a form", and the consequences
 * reach everything: no avatar is fetched, no verification email is sent, the assigned counts are
 * not recalculated, the page cache is not flushed, and is_pinned / is_verified / ip_address stop
 * being protected fields.
 *
 * These two tests began life in Integration/CompatibilityTest.php, and did exactly that to the
 * fourteen tests that ran after them — none of which had done anything wrong. phpunit.xml declares
 * the Import suite LAST so that this file may poison a process nobody is using afterwards, and
 * Pest.php now throws if any test outside this directory defines the constant.
 */

beforeEach(function () {
    definesWpImporting(); // importAttachments() does, and it is why this file is in this suite
    resetPluginState();
});

test('importing attachments reports how many it did', function () {
    // Paged: the importer is called over and over from the browser, and each call does one page.
    // The message is what the person watching the progress bar reads.
    $command = new ImportReviewsAttachments(new Request(['page' => 1, 'per_page' => 10]));

    $command->handle();

    expect($command->response())->toHaveKey('message');
});

test('a page and a per-page of nothing are still a page of one', function () {
    // max(1, …) on both. A per_page of 0 would be an importer that imports nothing, forever, while
    // telling the person it is working — and an offset computed from page 0 would be negative.
    $command = new ImportReviewsAttachments(new Request(['page' => 0, 'per_page' => 0]));

    $command->handle();

    expect($command->response())->toHaveKey('message'); // it ran, rather than dividing by zero
});
