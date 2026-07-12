<?php

use GeminiLabs\SiteReviews\Commands\DownloadCsvTemplate;
use GeminiLabs\SiteReviews\Modules\Rating;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The CSV import template.
 *
 * This command is the plugin's contract with anyone importing reviews: the
 * template's columns ARE the accepted columns, and the table on the Tools page
 * describing them is built from the same source. So the thing worth pinning is
 * that the two agree — a column offered in the template that the importer does not
 * accept, or a documented column missing from the template, is a support ticket.
 *
 * handle() ends in $writer->output() + exit and cannot be run here. Everything it
 * writes comes from data(), which can.
 */

beforeEach(fn () => resetPluginState());

function csvTemplate(): DownloadCsvTemplate
{
    return new DownloadCsvTemplate();
}

test('the template offers a column for every field of a review', function () {
    $columns = array_keys(csvTemplate()->data());

    // A round of the fields a review actually has. The order is the column order of
    // the file itself, and it is deliberate: date first, then the review.
    expect($columns)->toBe([
        'date', 'date_gmt', 'rating', 'title', 'content', 'name', 'email', 'avatar',
        'ip_address', 'terms', 'author_id', 'is_approved', 'is_pinned', 'is_verified',
        'response', 'assigned_posts', 'assigned_terms', 'assigned_users', 'score',
    ]);
});

test('the template row is a review someone could actually have left', function () {
    // The row is an example, and an example that would not import is worse than no
    // example at all: the rating has to be in range and the date has to be a date.
    $data = csvTemplate()->data();

    expect($data['rating'])->toBeGreaterThanOrEqual(Rating::min())
        ->and($data['rating'])->toBeLessThanOrEqual(Rating::max())
        ->and(strtotime($data['date']))->not->toBeFalse()
        ->and(is_email($data['email']))->not->toBeFalse();
});

test('only the date and the rating are required', function () {
    // Everything else the importer can do without — a review with no title, no name
    // and no email is still a rating on a date.
    expect(csvTemplate()->required())->toBe(['date', 'rating']);
});

test('every column in the template is documented', function () {
    // tableData() is what the Tools page prints. A column in the file with no row in
    // that table is an undocumented column.
    $documented = array_keys(csvTemplate()->tableData());

    foreach (array_keys(csvTemplate()->data()) as $column) {
        expect($documented)->toContain($column);
    }
});

test('the documentation says which columns are required and which addon they need', function () {
    $table = csvTemplate()->tableData();

    // required is rendered, not implied
    expect($table['date']['required'])->toContain('Yes')
        ->and($table['rating']['required'])->toContain('Yes')
        ->and($table['title']['required'])->toContain('No');

    // a column that only exists with an addon says so, and a core column does not
    expect($table['date']['notice'])->toBe('')
        ->and($table['images']['notice'])->toContain('addon required');
});

test('the documented columns are listed in an order somebody could read', function () {
    // tableData() ksorts, because it is a reference table rather than a file layout —
    // unlike data(), whose order is the column order of the CSV.
    $documented = array_keys(csvTemplate()->tableData());
    $sorted = $documented;
    sort($sorted);

    expect($documented)->toBe($sorted);
});

test('the geolocation columns are documented but are not template columns', function () {
    // Geolocation is written by the plugin, not by the person importing — it is
    // documented so that an export can be re-imported, but the template does not
    // invite anyone to fill it in.
    $documented = array_keys(csvTemplate()->tableData());
    $template = array_keys(csvTemplate()->data());

    expect($documented)->toContain('geolocation_city')
        ->and($template)->not->toContain('geolocation_city');
});
