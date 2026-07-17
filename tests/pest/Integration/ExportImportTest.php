<?php

use GeminiLabs\SiteReviews\Commands\ExportRatings;
use GeminiLabs\SiteReviews\Commands\ExportReviews;
use GeminiLabs\SiteReviews\Commands\ImportRatings;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Getting reviews out of the plugin and back in — two separate roads:
 *
 *   ExportRatings / ImportRatings   WordPress's own exporter (Tools > Export, the WXR file). Core
 *                                   knows nothing about the plugin's custom tables, so ExportRatings
 *                                   copies every rating row into POST META for the WXR exporter to
 *                                   pick up, and ImportRatings puts it back into the tables on the
 *                                   other side and deletes the meta again.
 *   ExportReviews                   the plugin's own CSV export (Tools > General).
 *
 * The queue calls do nothing here: the suite binds a NullQueue, so once() and async() return 0
 * without touching Action Scheduler.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front'); // the permission test leaves an admin screen behind
    $_FILES = []; // the settings-import test puts a file there
});

function reviewsTable(string $name): string
{
    return glsr(Tables::class)->table($name);
}

function countRows(string $table): int
{
    global $wpdb;

    return (int) $wpdb->get_var('SELECT COUNT(*) FROM '.reviewsTable($table));
}

/**
 * The review as it is in the database right now — the plugin keeps an in-memory
 * cache of reviews, and these tests delete rows out from under it.
 */
function freshReview(int $reviewId): \GeminiLabs\SiteReviews\Review
{
    return glsr(ReviewManager::class)->get($reviewId, $bypassCache = true);
}

/**
 * The export meta rows as they are in the database right now.
 *
 * ExportRatings writes them with Database::insertBulk() — one raw INSERT for the
 * lot, rather than a few thousand calls to add_post_meta() — so WordPress's meta
 * cache, which was populated when the review was created, does not know they are
 * there. That is not a bug: the WXR exporter reads postmeta with a direct query
 * (wp-admin/includes/export.php) and never asks the cache either. Reading through
 * get_post_meta() without dropping the cache first would be reading it the one way
 * production never does.
 */
function exportedMeta(int $reviewId, bool $single = true)
{
    wp_cache_delete($reviewId, 'post_meta');

    return get_post_meta($reviewId, glsr()->export_key, $single);
}

test('a rating survives a WordPress export and import', function () {
    // The round trip, which is the whole point of these two commands: the rating,
    // the assignments and the flags all live in custom tables that a WXR file
    // cannot carry, so they are parked in post meta for the journey.
    $postId = createPost();
    $userId = createUser();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);
    $review = createReview([
        'assigned_posts' => $postId,
        'assigned_terms' => $termId,
        'assigned_users' => $userId,
        'rating' => 4,
    ]);

    (new ExportRatings(glsr()->args(['content' => glsr()->post_type])))->handle();

    // Parked: one meta row per review, holding the whole rating record.
    $exported = exportedMeta($review->ID);
    expect($exported)->toBeArray()
        ->and((int) $exported['rating'])->toBe(4)
        ->and((int) $exported['ID'])->toBe($review->rating_id);

    // Now be the other side of the import: the posts and their meta arrived, the
    // custom tables did not.
    global $wpdb;
    $wpdb->query('DELETE FROM '.reviewsTable('ratings')); // assignments cascade
    expect(freshReview($review->ID)->isValid())->toBeFalse();
    expect(countRows('assigned_posts'))->toBe(0);

    (new ImportRatings())->handle();

    $imported = freshReview($review->ID);
    expect($imported->isValid())->toBeTrue()
        ->and($imported->rating)->toBe(4)
        ->and($imported->assigned_posts)->toBe([$postId])
        ->and($imported->assigned_users)->toBe([$userId]);

    // and the meta it travelled in is cleaned up after itself
    expect(exportedMeta($review->ID))->toBeEmpty();
});

test('a review assigned to nothing is imported with just its rating, no assignment rows', function () {
    // prepareAssignedValues skips any review with no assigned posts, users or terms — there is
    // nothing to rebuild for it, so it moves on rather than inserting an empty assignment row.
    $review = createReview(['rating' => 4]); // assigned to nothing

    (new ExportRatings(glsr()->args(['content' => glsr()->post_type])))->handle();

    global $wpdb;
    $wpdb->query('DELETE FROM '.reviewsTable('ratings'));
    expect(freshReview($review->ID)->isValid())->toBeFalse();

    (new ImportRatings())->handle();

    $imported = freshReview($review->ID);
    expect($imported->isValid())->toBeTrue()
        ->and($imported->rating)->toBe(4)
        ->and($imported->assigned_posts)->toBe([]); // nothing was assigned, nothing rebuilt
});

test('the export meta is rebuilt rather than appended to', function () {
    // handle() deletes every export key before it writes, so exporting twice does
    // not leave two copies of every rating behind.
    $review = createReview();
    $export = fn () => (new ExportRatings(glsr()->args(['content' => glsr()->post_type])))->handle();

    $export();
    $export();

    expect(exportedMeta($review->ID, $single = false))->toHaveCount(1);
});

test('only the plugin post meta is carried into the export file', function () {
    // WordPress asks, for every meta key, whether to skip it. The plugin says yes
    // to everything that is not its own — the export key and the custom fields —
    // so a review's WXR entry does not drag along meta belonging to other plugins.
    $command = new ExportRatings(glsr()->args(['content' => glsr()->post_type]));

    expect($command->filterExportSkipPostMeta(false, glsr()->export_key))->toBeFalse()
        ->and($command->filterExportSkipPostMeta(false, '_custom_field'))->toBeFalse()
        ->and($command->filterExportSkipPostMeta(false, '_edit_lock'))->toBeTrue()
        ->and($command->filterExportSkipPostMeta(false, '_thumbnail_id'))->toBeTrue();
});

test('the export filter is only added for a review export', function () {
    // The constructor hooks wxr_export_skip_postmeta only when the export being run
    // is an export of reviews. Exporting pages must not have the plugin quietly
    // stripping their meta.
    remove_all_filters('wxr_export_skip_postmeta');

    new ExportRatings(glsr()->args(['content' => 'page']));
    expect(has_filter('wxr_export_skip_postmeta'))->toBeFalse();

    new ExportRatings(glsr()->args(['content' => glsr()->post_type]));
    expect(has_filter('wxr_export_skip_postmeta'))->not->toBeFalse();
});

/*
 * The CSV export.
 *
 * handle() ends in $writer->output() + exit, which would take the test process
 * with it, so what is tested is everything that decides WHAT gets written:
 * fetchReviews() — the generator that yields one record per review — and the two
 * ways handle() refuses to write anything at all.
 */

test('the csv export yields a record per review, with its meta flattened into columns', function () {
    $postId = createPost();
    $review = createReview(['assigned_posts' => $postId, 'rating' => 3, 'title' => 'A title']);
    update_post_meta($review->ID, '_custom_favourite_colour', 'blue');

    $command = new ExportReviews(new Request());
    $records = iterator_to_array(
        protectedMethod(ExportReviews::class, 'fetchReviews')->invoke($command)
    );

    expect($records)->toHaveCount(1);
    $record = $records[0];
    expect($record['title'])->toBe('A title')
        ->and((int) $record['rating'])->toBe(3)
        ->and((int) $record['assigned_posts'])->toBe($postId)
        // a custom field becomes its own column, with the leading underscore gone
        ->and($record['custom_favourite_colour'])->toBe('blue')
        // the post ID is an internal key: it is the pagination cursor, not a column
        ->and($record)->not->toHaveKey('ID');
});

test('a geolocated review is exported with a column per location field', function () {
    // The _geolocation meta is a single blob; the export fans it out into geolocation_country,
    // geolocation_city and so on, so a spreadsheet has a column for each rather than a struct.
    $review = createReview(['rating' => 4]);
    update_post_meta($review->ID, '_geolocation', [
        'country' => 'US',
        'city' => 'Springfield',
        'region' => 'IL',
    ]);

    $command = new ExportReviews(new Request());
    $record = iterator_to_array(
        protectedMethod(ExportReviews::class, 'fetchReviews')->invoke($command)
    )[0];

    expect($record['geolocation_country'])->toBe('US')
        ->and($record['geolocation_city'])->toBe('Springfield')
        ->and($record['geolocation_region'])->toBe('IL');
});

test('the csv export refuses to write a file with nothing in it', function () {
    // No reviews: the generator yields nothing, and rather than handing the browser
    // an empty CSV the command fails and says so.
    glsr(Notice::class)->clear();
    $command = new ExportReviews(new Request());

    $command->handle();

    expect($command->successful())->toBeFalse()
        ->and(glsr(Notice::class)->get())->toContain('No reviews were found to export');
});

test('the csv export refuses a user who cannot use the tools page', function () {
    // hasPermission() is only a real question on an admin screen — off one it
    // returns true, because the capability check is there to protect the admin UI.
    // So the screen has to be set for this branch to exist at all.
    createReview();
    set_current_screen('edit.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));
    glsr(Notice::class)->clear();
    $command = new ExportReviews(new Request());

    $command->handle();

    expect($command->successful())->toBeFalse()
        ->and(glsr(Notice::class)->get())->toContain('You do not have permission to export reviews');
});

/*
 * The settings import.
 *
 * handle() reads an uploaded file, and an uploaded file cannot exist here:
 * UploadedFile::isValid() asks is_uploaded_file(), which is only ever true for a
 * file that arrived in THIS request over HTTP — never in a CLI process. So its
 * happy path is out of reach and what is tested for it is import(), which is what
 * handle() calls once it has the JSON. The one slice of handle() that IS reachable
 * is its refusal, tested last.
 */

function importSettings(array $data): bool
{
    return protectedMethod(ImportSettings::class, 'import')
        ->invoke(new ImportSettings(), $data);
}

test('importing settings replaces them', function () {
    commitsTransaction(); // ImportSettings migrates the settings it imported, and the migrations commit
    glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');

    expect(importSettings([
        'settings' => ['general' => ['require' => ['approval' => 'no']]],
    ]))->toBeTrue();

    expect(glsr_get_option('general.require.approval'))->toBe('no');
});

test('importing nothing imports nothing', function () {
    expect(importSettings([]))->toBeFalse();
});

test('an imported version number is discarded', function () {
    // The version in a settings file is the version of the site it came FROM, and
    // adopting it would tell the migration runner that migrations it has never run
    // are already done.
    commitsTransaction(); // see above
    $version = glsr(OptionManager::class)->get('version');

    importSettings([
        'settings' => ['general' => ['require' => ['approval' => 'no']]],
        'version' => '0.0.1',
        'version_upgraded_from' => '0.0.1',
    ]);

    expect(glsr(OptionManager::class)->get('version'))->toBe($version)
        ->and(glsr(OptionManager::class)->get('version_upgraded_from'))->not->toBe('0.0.1');
});

test('the settings import handles a whole upload, stubbed past the SAPI check', function () {
    // handle() itself, with getImportFile() overridden in a subclass to hand back the file it
    // WOULD have got — the seam sits exactly on the is_uploaded_file() wall described above,
    // and everything after it (the JSON read, import(), the notice) is real.
    commitsTransaction(); // import() migrates the imported settings
    $path = tempnam(sys_get_temp_dir(), 'glsr').'.json';
    file_put_contents($path, (string) wp_json_encode([
        'settings' => ['general' => ['require' => ['approval' => 'no']]],
    ]));
    register_shutdown_function(fn () => @unlink($path));
    $upload = new GeminiLabs\SiteReviews\UploadedFile([
        'error' => \UPLOAD_ERR_OK,
        'name' => 'settings.json',
        'size' => filesize($path),
        'tmp_name' => $path,
        'type' => 'application/json',
    ]);
    $command = new class($upload) extends ImportSettings {
        private GeminiLabs\SiteReviews\UploadedFile $upload;

        public function __construct(GeminiLabs\SiteReviews\UploadedFile $upload)
        {
            $this->upload = $upload;
        }

        protected function getImportFile(string $expectedMimeType): ?GeminiLabs\SiteReviews\UploadedFile
        {
            return $this->upload;
        }
    };
    glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');
    glsr(Notice::class)->clear();

    $command->handle();

    expect($command->successful())->toBeTrue()
        ->and(glsr(Notice::class)->get())->toContain('Settings imported');
    expect(glsr_get_option('general.require.approval'))->toBe('no');
});

test('a settings file with nothing in it imports nothing, with a warning', function () {
    $path = tempnam(sys_get_temp_dir(), 'glsr').'.json';
    file_put_contents($path, '{}');
    register_shutdown_function(fn () => @unlink($path));
    $upload = new GeminiLabs\SiteReviews\UploadedFile([
        'error' => \UPLOAD_ERR_OK,
        'name' => 'settings.json',
        'size' => 2,
        'tmp_name' => $path,
        'type' => 'application/json',
    ]);
    $command = new class($upload) extends ImportSettings {
        private GeminiLabs\SiteReviews\UploadedFile $upload;

        public function __construct(GeminiLabs\SiteReviews\UploadedFile $upload)
        {
            $this->upload = $upload;
        }

        protected function getImportFile(string $expectedMimeType): ?GeminiLabs\SiteReviews\UploadedFile
        {
            return $this->upload;
        }
    };
    glsr(Notice::class)->clear();

    $command->handle();

    expect($command->successful())->toBeFalse()
        ->and(glsr(Notice::class)->get())->toContain('nothing found to import');
});

test('the meta of a post that does not exist is an empty list', function () {
    // The guard in ExportReviews::postMeta() — a record whose post vanished mid-export must
    // contribute nothing, not a PHP warning in the CSV.
    $meta = protectedMethod(ExportReviews::class, 'postMeta')
        ->invoke(new ExportReviews(new Request()), 0);

    expect($meta)->toBe([]);
});

test('the settings import fails cleanly when no file was uploaded', function () {
    // The one path through handle() a CLI test can reach. It is not a contrivance: it is exactly
    // what an admin who submits the import form without choosing a file gets — getImportFile()
    // refuses, and handle() (which marked itself failed up front) bails, importing nothing.
    $_FILES['import-files'] = [
        'error' => UPLOAD_ERR_NO_FILE,
        'name' => '',
        'size' => 0,
        'tmp_name' => '',
        'type' => '',
    ];
    glsr(Notice::class)->clear();
    $command = new ImportSettings();

    $command->handle();

    expect($command->successful())->toBeFalse()
        ->and(glsr(Notice::class)->get())->toContain('No file was uploaded');
});
