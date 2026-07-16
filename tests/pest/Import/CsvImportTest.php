<?php

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\ImportReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviewsCleanup;
use GeminiLabs\SiteReviews\Commands\ProcessCsvFile;
use GeminiLabs\SiteReviews\Controllers\ImportController;
use GeminiLabs\SiteReviews\Database\ImportManager;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\UploadedFile;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\definesWpImporting;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The CSV import, end to end.
 *
 * A CSV becomes reviews in three moves, each a separate admin request because a big file cannot be
 * done in one:
 *
 *   ProcessCsvFile        reads the upload, drops the rows that would not import, and writes the
 *                         survivors to a temp CSV in uploads. Creates no reviews.
 *   ImportReviews         reads a page of that temp CSV and turns each row into a review. Called
 *                         repeatedly until the file is done.
 *   ImportReviewsCleanup  drops the temp table and file, and reports.
 *
 * WHY THIS SUITE RUNS LAST: ProcessCsvFile and ImportManager both define WP_IMPORTING, which
 * cannot be unset — so from the first test here it is defined for the rest of the process, and
 * nineteen places read it. It stops an import generating an avatar per review, a verification email
 * per review, geolocating every IP, flushing the page cache a thousand times, or treating a
 * spreadsheet row as a form submission: correct during an import, wrong everywhere else. So this is
 * the LAST suite in phpunit.xml. Move it and ThirdParty/CacheTest fails: it asserts WP_IMPORTING is
 * not defined, precisely so this cannot happen quietly.
 *
 * handle() cannot be reached past its first guard — it fetches the upload with
 * UploadedFile::isValid(), which asks is_uploaded_file(), and no CLI process received an HTTP
 * upload. process() takes the file it would have got, so the pipeline is driven from there. See
 * tests/pest/Integration/UploadedFileTest.
 */

beforeEach(function () {
    commitsTransaction(); // every test here does — see below
    definesWpImporting(); // and every test here does that too — which is why this suite runs last
    purgeEverythingThisSuiteCreates(); // the last run may have crashed out
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr(Notice::class)->clear();
    glsr(ImportManager::class)->unlinkTempFile();
    glsr(ImportManager::class)->flush();
});

afterEach(function () {
    $_FILES = [];
    glsr(ImportManager::class)->unlinkTempFile();
    glsr(ImportManager::class)->flush();
    glsr(Notice::class)->clear();
    purgeEverythingThisSuiteCreates();
});

/**
 * THE TRANSACTION CANNOT PROTECT THIS SUITE — it cleans up after itself instead.
 *
 * TableTmp::create()/drop() are DDL, and MySQL implicitly COMMITs the open transaction on DDL, so
 * every test here commits what it has done and the ROLLBACK finds nothing to undo. Left alone the
 * reviews and users pile up: a test dedupes away the previous test's reviews, and the next whole
 * run collides on the users ("Sorry, that username already exists") in CommandTest, a different
 * suite. The COMMIT at the end is not optional — without it Pest.php's ROLLBACK would take the
 * cleanup with it.
 */
function purgeEverythingThisSuiteCreates(): void
{
    global $wpdb;

    // The ratings table's review_id FK is ON DELETE CASCADE, and the three assignment tables
    // cascade from ratings — so deleting the review posts empties all four (see
    // Database\Tables\AbstractTable::addForeignConstraint).
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type
    ));
    $wpdb->query(
        "DELETE pm FROM {$wpdb->postmeta} pm
         LEFT JOIN {$wpdb->posts} p ON (p.ID = pm.post_id)
         WHERE p.ID IS NULL"
    );
    $wpdb->query("DELETE FROM {$wpdb->users} WHERE ID > 1"); // 1 is the wp-env admin
    $wpdb->query(
        "DELETE um FROM {$wpdb->usermeta} um
         LEFT JOIN {$wpdb->users} u ON (u.ID = um.user_id)
         WHERE u.ID IS NULL"
    );
    $wpdb->query('COMMIT');
    wp_cache_flush();
}

/**
 * The file as ProcessCsvFile would have received it. Only the path is ever read
 * off it, so is_uploaded_file() never comes into it.
 */
function csvUpload(string $contents): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'glsr').'.csv';
    file_put_contents($path, $contents);

    return new UploadedFile([
        'error' => \UPLOAD_ERR_OK,
        'name' => 'reviews.csv',
        'size' => strlen($contents),
        'tmp_name' => $path,
        'type' => 'text/csv',
    ]);
}

function processCsv(string $contents, array $args = []): ProcessCsvFile
{
    $command = new ProcessCsvFile(new Request(wp_parse_args($args, [
        'date_format' => 'Y-m-d',
        'delimiter' => ',',
    ])));
    protectedMethod(ProcessCsvFile::class, 'process')->invoke($command, csvUpload($contents));

    return $command;
}

/**
 * Two rows that import, one with a date the format cannot read, one with a rating
 * outside the scale.
 */
function csvFixture(string $delimiter = ','): string
{
    $rows = [
        ['date', 'rating', 'title', 'content', 'name', 'email'],
        ['2024-01-15', '5', 'Loved it', 'Would come back', 'Alice', 'alice@example.org'],
        ['2024-02-20', '3', 'It was fine', 'Nothing to report', 'Bob', 'bob@example.org'],
        ['the fifteenth', '4', 'Bad date', 'Skipped', 'Carol', 'carol@example.org'],
        ['2024-03-01', '9', 'Bad rating', 'Skipped', 'Dave', 'dave@example.org'],
    ];

    return implode("\n", array_map(fn ($row) => implode($delimiter, $row), $rows))."\n";
}

function importedReviewCount(): int
{
    return count(get_posts([
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'any',
        'post_type' => glsr()->post_type,
    ]));
}

test('a csv becomes reviews', function () {
    // The whole pipeline: the only way to know the three commands agree about the temp file they
    // hand along.
    $command = processCsv(csvFixture());

    // Move one: good rows staged, bad ones counted and explained.
    //
    // skipped once came back doubled. Statement::process() returns a LAZY ResultSet (the where()
    // callbacks run on iteration), and iterating it twice — insertAll() then count() — ran
    // validateRecord() twice per row, so ++$this->skipped fired twice per skipped row and the admin
    // saw double (import.js hands stage 1's count to stage 4). The records are walked once now.
    $response = $command->response();
    expect($response['total'])->toBe(2)
        ->and($response['skipped'])->toBe(2)
        ->and($response['errors'])->toBe([
            'date' => 'Incorrect date format',
            'rating' => 'Empty or invalid rating',
        ]);
    expect(is_file(glsr(ImportManager::class)->tempFilePath()))->toBeTrue();
    expect(importedReviewCount())->toBe(0); // nothing is imported yet

    // Move two: the staged rows become reviews.
    $import = new ImportReviews(new Request(['page' => 1, 'per_page' => 10]));
    $import->handle();

    expect($import->response()['imported'])->toBe(2)
        ->and($import->response()['skipped'])->toBe(0)
        ->and(importedReviewCount())->toBe(2);

    // and they are reviews, not just posts: the rating came across too
    $reviews = glsr_get_reviews(['per_page' => 10])->reviews;
    expect(wp_list_pluck($reviews, 'title'))
        ->toContain('Loved it')
        ->toContain('It was fine');
    expect(wp_list_pluck($reviews, 'rating'))->toContain(5)->toContain(3);
    expect(wp_list_pluck($reviews, 'email'))->toContain('alice@example.org');

    // Move three: the temp file and table are cleared away.
    $cleanup = new ImportReviewsCleanup(new Request([
        'errors' => ['Incorrect date format'],
        'imported' => 2,
        'skipped' => 2,
    ]));
    $cleanup->handle();

    expect(is_file(glsr(ImportManager::class)->tempFilePath()))->toBeFalse();
    expect($cleanup->response()['notices'])
        ->toContain('2 reviews were imported')
        ->toContain('2 entries were skipped');
});

test('the same csv imported twice does not make the reviews twice', function () {
    // Every review carries a hash of what was submitted, and the importer checks for it before
    // creating anything — so an import that timed out halfway can be re-run without doubling.
    processCsv(csvFixture());
    (new ImportReviews(new Request(['page' => 1, 'per_page' => 10])))->handle();
    expect(importedReviewCount())->toBe(2);

    $again = new ImportReviews(new Request(['page' => 1, 'per_page' => 10]));
    $again->handle();

    expect($again->response()['imported'])->toBe(0)
        ->and($again->response()['skipped'])->toBe(2)
        ->and(importedReviewCount())->toBe(2);
});

test('the reviews are imported a page at a time', function () {
    // The admin calls ImportReviews once per page: a file of ten thousand rows will not import in
    // one request.
    processCsv(csvFixture());

    $first = new ImportReviews(new Request(['page' => 1, 'per_page' => 1]));
    $first->handle();
    expect($first->response()['imported'])->toBe(1)
        ->and(importedReviewCount())->toBe(1);

    $second = new ImportReviews(new Request(['page' => 2, 'per_page' => 1]));
    $second->handle();
    expect($second->response()['imported'])->toBe(1)
        ->and(importedReviewCount())->toBe(2);
});

test('the temp table is created for the import and dropped after it', function () {
    expect(glsr(TableTmp::class)->exists())->toBeFalse();

    processCsv(csvFixture());
    expect(glsr(TableTmp::class)->exists())->toBeTrue();

    (new ImportReviewsCleanup(new Request(['imported' => 2, 'skipped' => 0])))->handle();
    expect(glsr(TableTmp::class)->exists())->toBeFalse();
});

test('a cleanup that imported nothing leaves the file alone', function () {
    // Nothing was imported, so nothing to clean up — and the temp file is the only record of what
    // was staged.
    processCsv(csvFixture());

    (new ImportReviewsCleanup(new Request(['imported' => 0, 'skipped' => 4])))->handle();

    expect(is_file(glsr(ImportManager::class)->tempFilePath()))->toBeTrue();
});

test('a file bigger than one chunk is processed once, not once per chunk', function () {
    // Looks like a bug and is not — worth writing down before somebody "fixes" it:
    //
    //     $chunks = $reader->chunkBy(1000);
    //     foreach ($chunks as $chunk) {
    //         $records = Statement::create()->where(…)->process($reader, $header);
    //
    // $chunk is unused and the whole $reader is processed inside a loop over its own chunks, which
    // reads like a 2,500-row file processed three times. It is not: Reader::chunkBy() is a lazy
    // GENERATOR over the SplFileObject's iterator, and process($reader) calls getRecords(), which
    // rewinds and reads that same document to EOF — so the generator's iterator is already at EOF
    // when resumed, there is no second chunk, and the loop body runs once. The chunking is a no-op,
    // not a defect. 1,001 rows is two chunks' worth, the smallest file that tells the difference.
    $rows = ['date,rating,title'];
    for ($i = 1; $i <= 1001; ++$i) {
        $rows[] = sprintf('2024-01-15,5,Review %d', $i);
    }
    $command = processCsv(implode("\n", $rows)."\n");

    expect($command->response()['total'])->toBe(1001); // and not 2002
});

test('the delimiter is worked out when it is not given', function () {
    // The import form lets you leave the delimiter blank, and a European CSV is
    // semicolon separated.
    $command = processCsv(csvFixture(';'), ['delimiter' => '']);

    expect($command->response()['total'])->toBe(2);
});

test('a file whose delimiter cannot be worked out is refused', function () {
    // Both a comma and a semicolon appear consistently, so there is no telling which
    // is the delimiter, and guessing would silently import nonsense.
    $ambiguous = "date,rating;title\n2024-01-15,5;Loved it\n2024-02-20,3;It was fine\n";
    $command = processCsv($ambiguous, ['delimiter' => '']);

    expect($command->response()['total'])->toBe(0);
    expect(glsr(Notice::class)->get())->toContain('Cannot detect the delimiter');
});

test('a file missing a required column is refused, with the reasons', function () {
    // date and rating are the two columns a review cannot be built without.
    $command = processCsv("date,title\n2024-01-15,Loved it\n");

    expect($command->response()['total'])->toBe(0);
    expect(glsr(Notice::class)->get())
        ->toContain('The CSV file could not be imported')
        ->toContain('Does the CSV file include all required columns?');
    expect(is_file(glsr(ImportManager::class)->tempFilePath()))->toBeFalse(); // nothing staged
});

test('a row is only staged if its date and rating can be read', function (array $record, bool $expected) {
    $command = new ProcessCsvFile(new Request(['date_format' => 'Y-m-d', 'delimiter' => ',']));

    expect(protectedMethod(ProcessCsvFile::class, 'validateRecord')->invoke($command, $record))
        ->toBe($expected);
})->with([
    'good' => [['date' => '2024-01-15', 'rating' => '5'], true],
    'padded' => [['date' => ' 2024-01-15 ', 'rating' => ' 5 '], true], // whitespace is trimmed
    'wrong date format' => [['date' => '15/01/2024', 'rating' => '5'], false],
    'not a date' => [['date' => 'yesterday', 'rating' => '5'], false],
    'no date' => [['date' => '', 'rating' => '5'], false],
    'rating too high' => [['date' => '2024-01-15', 'rating' => '9'], false],
    // 0 is a rating: MIN_RATING is 0 and it means "unrated", which is why the CSV
    // template documents the range as 0-5. So an empty rating column is not a
    // reason to skip a row — it is an unrated review.
    'rating of zero' => [['date' => '2024-01-15', 'rating' => '0'], true],
    'no rating' => [['date' => '2024-01-15', 'rating' => ''], true],
]);

test('a date is rewritten into the format the database wants', function () {
    // The person importing says what format their dates are in; the database takes only one.
    // DateTime::createFromFormat() fills every field the format omits from the CURRENT time, and
    // twelve of the eighteen accepted formats leave it something to fill (six carry no time, six no
    // seconds) — which would stamp every review with the import's moment. The "!" formatRecord()
    // puts in front of the format zeroes them instead.
    $command = new ProcessCsvFile(new Request(['date_format' => 'd/m/Y', 'delimiter' => ',']));

    $record = protectedMethod(ProcessCsvFile::class, 'formatRecord')
        ->invoke($command, ['date' => '15/01/2024', 'rating' => '5']);

    expect($record['date'])->toBe('2024-01-15 00:00:00');
});

test('uploading the same csv twice does not import the reviews twice', function () {
    // What the date bug cost, and why it was worth fixing. ImportManager dedupes by a hash of the
    // submitted fields (CreateReview::submitted); `date` is in that hash (not in
    // SubmittedFieldsDefaults::$guarded), and `ip_address` is guarded, so the date is the only
    // field that can change between two readings of the same row. With twelve formats leaving a
    // field for the clock, processing the same file a second apart hashed every row differently and
    // re-imported everything — the dedupe held within one file but came apart across uploads, which
    // is exactly when a person reaches for it (the import timed out, so they upload again).
    $csv = "date,rating,title\n2024-01-15,5,Loved it\n";

    processCsv($csv);
    (new ImportReviews(new Request(['page' => 1, 'per_page' => 10])))->handle();
    expect(importedReviewCount())->toBe(1);

    sleep(1); // a second passes, as one does between two uploads
    processCsv($csv);
    (new ImportReviews(new Request(['page' => 1, 'per_page' => 10])))->handle();

    expect(importedReviewCount())->toBe(1); // and not 2
});

test('an avatar the plugin drew itself is not imported back in', function () {
    // The generated avatars are SVGs served from the plugin's own URL. Importing one
    // would pin a review to an avatar that only exists on the site it came from.
    $command = new ProcessCsvFile(new Request(['date_format' => 'Y-m-d', 'delimiter' => ',']));
    $format = protectedMethod(ProcessCsvFile::class, 'formatRecord');

    $generated = $format->invoke($command, [
        'avatar' => 'https://example.org/wp-content/plugins/'.glsr()->ID.'/avatars/AB.svg',
    ]);
    $real = $format->invoke($command, [
        'avatar' => 'https://secure.gravatar.com/avatar/1234',
    ]);

    expect($generated['avatar'])->toBe('')
        ->and($real['avatar'])->toBe('https://secure.gravatar.com/avatar/1234');
});

test('an upload that is not there is reported rather than imported', function () {
    // handle()'s one reachable path: $_FILES holds nothing, so the file it builds
    // has an error code PHP never issues and isValid() turns it away.
    $_FILES = [];
    $command = new ProcessCsvFile(new Request(['date_format' => 'Y-m-d', 'delimiter' => ',']));

    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(glsr(Notice::class)->get())->toContain('notice-error');
});

/*
 * The geolocation columns.
 *
 * ImportController::filterReviewPostData fires on review/create/post_data, but ONLY during an import
 * (it checks WP_IMPORTING) — which is why it is tested here, in the one suite where that constant is
 * defined. It lifts the row's geolocation_* fields into the _geolocation meta the plugin reads back.
 * StatDefaults decides which survive; rating_id belongs to the ratings table, not the meta, and is
 * dropped.
 */

test('an imported review carries its geolocation columns into meta, minus the rating id', function () {
    // The whole filter hinges on WP_IMPORTING. An import test defines it as a side effect, but not
    // necessarily before this one under test:random — so define it here (the beforeEach's
    // definesWpImporting() is what tells Pest.php that is expected).
    if (!defined('WP_IMPORTING')) {
        define('WP_IMPORTING', true);
    }

    // The request is set directly rather than through the constructor: the filter's contract is
    // "given a create command whose request holds geolocation_* keys, add the meta", and this is the
    // request as ProcessCsvFile hands it over, geolocation columns and all.
    $command = new CreateReview(new Request());
    $command->request = new Request([
        'geolocation_country' => 'US',
        'geolocation_city' => 'Springfield',
        'geolocation_region' => 'IL',
        'geolocation_rating_id' => 99, // belongs to the ratings table — must not survive into meta
    ]);

    $data = glsr(ImportController::class)->filterReviewPostData(['meta_input' => []], $command);

    expect($data['meta_input'])->toHaveKey('_geolocation');
    $geolocation = $data['meta_input']['_geolocation'];
    expect($geolocation['country'])->toBe('US')
        ->and($geolocation['city'])->toBe('Springfield')
        ->and($geolocation['region'])->toBe('IL')
        ->and($geolocation)->not->toHaveKey('rating_id');
});

test('without geolocation columns the review meta is left untouched', function () {
    // The common import: no geolocation data on the row, so insertGeolocationMeta finds nothing and
    // adds no _geolocation key rather than an empty one.
    if (!defined('WP_IMPORTING')) {
        define('WP_IMPORTING', true);
    }
    $command = new CreateReview(new Request());
    $command->request = new Request(['title' => 'A review with no location']);

    $data = glsr(ImportController::class)->filterReviewPostData(['meta_input' => ['existing' => 'kept']], $command);

    expect($data['meta_input'])->toBe(['existing' => 'kept']);
});

/*
 * The two encodings the reader transcodes, and the one file it never receives.
 */

test('a UTF-16 CSV is transcoded to UTF-8 before it is read', function () {
    // Windows spreadsheet exports are often UTF-16 with a byte-order mark; the reader detects the BOM
    // and converts, rather than reading the bytes as garbage.
    $content = "\xFF\xFE".mb_convert_encoding(csvFixture(), 'UTF-16LE', 'UTF-8');

    $command = processCsv($content);

    expect($command->response()['total'])->toBe(2); // the two importable rows, read through the transcode
});

test('a UTF-32 CSV is transcoded to UTF-8 before it is read', function () {
    $content = "\xFF\xFE\x00\x00".mb_convert_encoding(csvFixture(), 'UTF-32LE', 'UTF-8');

    $command = processCsv($content);

    expect($command->response()['total'])->toBe(2);
});

test('a process job whose uploaded file has vanished is reported, not fatal', function () {
    // handle() (as opposed to the process() the other tests drive) opens the file itself, and the
    // temp copy can be gone by the time it looks — a FileNotFoundException it must catch.
    $path = tempnam(sys_get_temp_dir(), 'glsr').'.csv';
    file_put_contents($path, csvFixture());
    unlink($path); // the temp file is gone
    $_FILES['import-files'] = [
        'error' => \UPLOAD_ERR_OK,
        'name' => 'reviews.csv',
        'size' => 1,
        'tmp_name' => $path,
        'type' => 'text/csv',
    ];

    $command = new ProcessCsvFile(new Request(['date_format' => 'Y-m-d', 'delimiter' => ',']));
    $command->handle();

    expect($command->successful())->toBeFalse();
});
