<?php

use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The Tools page, over ajax.
 *
 * Almost every tool has two entry points — a plain admin POST and an ajax one — and the page uses
 * the ajax one. It ends in wp_send_json(), which is why these tests run inside the ajax harness:
 * wp_send_json() only calls wp_die() when wp_doing_ajax() is true; otherwise a bare `die` nothing
 * can intercept, and the test process would stop.
 *
 * The envelope is the contract with the admin JS: {data, success} for progress-reporting tools, and
 * WordPress's own from wp_send_json_success/_error for the rest. The JS branches on success, so a
 * tool that fails silently with success:true lies to the person using it.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->setUpAjax();
});

afterEach(fn () => $this->tearDownAjax());

test('clearing the console empties it and says so', function () {
    glsr_log()->error('something went wrong');
    expect(glsr(Console::class)->get())->not->toBeEmpty();

    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->clearConsoleAjax());

    expect($response['success'])->toBeTrue();
    expect(glsr(Console::class)->get())->toContain('Console is empty'); // and it is
});

test('the console log level can be changed', function () {
    // The level lives in its own option rather than in the plugin's settings, so that
    // turning the console down does not need the settings to be writable.
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->changeConsoleLevelAjax(
        new Request(['level' => Console::NOTICE])
    ));

    expect($response['success'])->toBeTrue();
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::NOTICE);
});

test('a log level that does not exist is refused', function () {
    $before = (int) get_option(Console::LOG_LEVEL_KEY);

    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->changeConsoleLevelAjax(
        new Request(['level' => 3]) // not one of the PSR levels
    ));

    expect($response['success'])->toBeFalse();
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe($before);
});

test('the console can be fetched, and refuses someone who may not see it', function () {
    // The console holds error messages, file paths and query fragments — it is not
    // for anyone who happens to reach the URL.
    glsr_log()->error('a logged error');

    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->fetchConsoleAjax());
    expect($response['success'])->toBeTrue()
        ->and($response['data']['console'])->toContain('a logged error');

    // hasPermission() only asks the question on an admin screen — off one it always
    // says yes, because the capability check is there to protect the admin UI.
    set_current_screen('edit.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $refused = $this->jsonSentBy(fn () => glsr(ToolsController::class)->fetchConsoleAjax());
    expect($refused['success'])->toBeFalse()
        ->and($refused['data']['notices'])->toContain('do not have permission');
});

test('repairing the permissions reports back', function () {
    get_role('editor')->remove_cap('edit_others_site-reviews');

    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->repairPermissionsAjax(new Request()));

    expect($response['success'])->toBeTrue();
    expect(get_role('editor')->has_cap('edit_others_site-reviews'))->toBeTrue();
});

test('repairing the review relations reports back', function () {
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->repairReviewRelationsAjax());

    expect($response['success'])->toBeTrue();
});

test('resetting the assigned meta reports back', function () {
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->resetAssignedMetaAjax());

    expect($response['success'])->toBeTrue();
});

test('the plugin can be migrated', function () {
    // The migration runner is idempotent — this is the tool a person reaches for when
    // something looks wrong after an upgrade. Idempotent in EFFECT, not in isolation:
    // MigrateReviews wraps each of its passes in Database::beginTransaction(), which on
    // InnoDB is a literal START TRANSACTION, and that ends the test's own.
    commitsTransaction();
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->migratePluginAjax(new Request()));

    expect($response['success'])->toBeTrue();
});

test('the ip address proxy can be configured, and detected', function () {
    // Two tools behind one route: "alt" asks the plugin to work the header out for
    // itself rather than being told.
    $configured = $this->jsonSentBy(fn () => glsr(ToolsController::class)->ipAddressDetectionAjax(
        new Request(['proxy_http_header' => 'HTTP_X_FORWARDED_FOR', 'trusted_proxies' => '10.0.0.1'])
    ));
    expect($configured['success'])->toBeTrue();

    $detected = $this->jsonSentBy(fn () => glsr(ToolsController::class)->ipAddressDetectionAjax(
        new Request(['alt' => 1])
    ));
    expect($detected['success'])->toBeTrue();
});

test('the table engine can be converted', function () {
    // The custom tables must be InnoDB — the foreign keys the plugin relies on do not
    // exist on MyISAM. This is the tool that fixes a site that was migrated badly.
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->convertTableEngineAjax(
        new Request(['table' => 'ratings'])
    ));

    expect($response)->toHaveKey('success');
});

test('the system info can be fetched, and refuses someone who may not see it', function () {
    // The report names the environment, the theme, the plugin list — reconnaissance for anyone
    // who does not already run the site. hasPermission() only asks the question on an admin screen.
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->fetchSystemInfoAjax());
    expect($response['success'])->toBeTrue()
        ->and($response['data']['data'])->toBeString()->not->toBeEmpty();

    set_current_screen('edit.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $refused = $this->jsonSentBy(fn () => glsr(ToolsController::class)->fetchSystemInfoAjax());
    expect($refused['success'])->toBeFalse()
        ->and($refused['data']['notices'])->toContain('permission');
});

test('geolocating reviews reports back, and refuses without permission', function () {
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->geolocateReviewsAjax(new Request()));
    expect($response)->toHaveKey('success');

    set_current_screen('edit.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $refused = $this->jsonSentBy(fn () => glsr(ToolsController::class)->geolocateReviewsAjax(new Request()));
    expect($refused['success'])->toBeFalse()
        ->and($refused['data']['notices'])->toContain('permission');
});

test('the alt flag removes location data instead of geolocating', function () {
    // Same route, "alt" turns it from "look these reviews up" into "forget where they came from".
    // RemoveLocationData empties the stats table with a TRUNCATE, which is DDL and commits.
    commitsTransaction();
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->geolocateReviewsAjax(
        new Request(['alt' => 1])
    ));

    expect($response)->toHaveKey('success');
});

test('importing refuses without permission, and does nothing without a valid stage', function () {
    // The importer is a four-stage pipeline the JS polls one stage at a time; a request whose stage
    // is not 1-4 falls through to a bare success. The stages themselves are covered by CsvImportTest.
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->importReviewsAjax(new Request()));
    expect($response['success'])->toBeTrue();

    set_current_screen('edit.php');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $refused = $this->jsonSentBy(fn () => glsr(ToolsController::class)->importReviewsAjax(new Request()));
    expect($refused['success'])->toBeFalse()
        ->and($refused['data']['notices'])->toContain('permission');
});

test('the rollback ajax hands back what the updater needs, and refuses without permission', function () {
    // rollbackPluginAjax gates on the update_plugins capability (not hasPermission), so the refusal
    // bites off an admin screen too. The success path returns rollbackData — the nonce, action,
    // plugin and slug the admin JS feeds straight into wp.updates.
    $response = $this->jsonSentBy(fn () => glsr(ToolsController::class)->rollbackPluginAjax(
        new Request(['version' => '8.0.0'])
    ));
    expect($response['success'])->toBeTrue()
        ->and($response['data']['data']['plugin'])->toBe(glsr()->basename)
        ->and($response['data']['data']['slug'])->toBe(glsr()->id)
        ->and($response['data']['url'])->toContain('welcome');

    wp_set_current_user(createUser(['role' => 'subscriber']));

    $refused = $this->jsonSentBy(fn () => glsr(ToolsController::class)->rollbackPluginAjax(
        new Request(['version' => '8.0.0'])
    ));
    expect($refused['success'])->toBeFalse()
        ->and($refused['data']['error'])->toContain('permission');
});
