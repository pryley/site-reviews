<?php

use GeminiLabs\SiteReviews\Commands\ConvertTableEngine;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Notices\MigrationNotice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Rollback;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The tools somebody reaches for when the plugin has gone wrong.
 *
 *   Rollback            go back to the previous version. This is what a site owner does at 2am when
 *                       an update has broken their site, and it has to work when nothing else does.
 *   ConvertTableEngine  a site on MyISAM cannot have foreign keys, so the plugin's tables cannot
 *                       cascade and its data drifts out of step. This converts them to InnoDB.
 *   ImportSettings      restore a settings backup — or copy a configured site onto a new one.
 *   MigrationNotice     the banner that says a migration is pending, and the thing that SCHEDULES
 *                       that migration. It is the only notice in the plugin that does work.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr(Notice::class)->clear();
});

afterEach(fn () => glsr(Notice::class)->clear());

/*
 * Rolling back.
 */

test('a rollback remembers which version to go back to, and hands wordpress the update it needs', function () {
    // The rollback works by telling WordPress to "update" the plugin — and then swapping the
    // download URL for the old version's zip while it is not looking. The transient is how the
    // version survives from this request into the one WordPress makes, and it is deliberately
    // short-lived: a stale one would silently downgrade somebody on their next ordinary update.
    $data = glsr(Rollback::class)->rollbackData('8.0.0');

    expect(get_transient(glsr()->prefix.'rollback_version'))->toBe('8.0.0');
    expect($data['data']['action'])->toBe('update-plugin')
        ->and($data['data']['plugin'])->toBe(glsr()->basename)
        ->and($data['data']['slug'])->toBe(glsr()->id)
        ->and($data['data']['_ajax_nonce'])->not->toBeEmpty() // WordPress's own updates nonce
        ->and($data['url'])->toContain('welcome');
});

/*
 * MyISAM.
 */

test('a table that is already InnoDB is not converted, and the person is told why', function () {
    // Every table the plugin creates is InnoDB. This tool exists for the sites whose wp_posts is
    // MyISAM — because a foreign key from an InnoDB table into a MyISAM one cannot be created, so
    // the plugin's cascades silently do not exist there.
    //
    // -1 means "not found, or not MyISAM", and the message says both, because the person running a
    // repair tool needs to know it did nothing.
    $command = new ConvertTableEngine(new Request(['table' => 'posts']));

    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(glsr(Notice::class)->get())->toContain('not found in the database, or does not use the MyISAM engine');
});

test('a table nobody has heard of is refused rather than guessed at', function () {
    $command = new ConvertTableEngine(new Request(['table' => 'not_a_table']));

    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(glsr(Notice::class)->get())->toContain('not_a_table');
});

test('and somebody without permission cannot alter the database at all', function () {
    // An ALTER TABLE on wp_posts. This is the most destructive thing in the plugin's tool box, and
    // the permission check is the only thing standing in front of it.
    wp_set_current_user(createUser(['role' => 'subscriber']));
    set_current_screen(glsr()->post_type); // hasPermission() is false off an admin screen anyway

    $command = new ConvertTableEngine(new Request(['table' => 'posts']));
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(glsr(Notice::class)->get())->toContain('do not have permission');

    set_current_screen('front');
});

/*
 * Importing settings.
 */

test('importing settings replaces what was there', function () {
    // This is a restore, not a merge: the file is the source of truth, and a setting the person
    // removed before exporting must not come back from the settings they are restoring over.
    //
    // It runs the migrations afterwards, which is DDL, so it commits.
    commitsTransaction();
    glsr(OptionManager::class)->set('settings.general.require.approval', 'yes');

    $imported = protectedMethod(ImportSettings::class, 'import')->invoke(
        glsr(ImportSettings::class),
        ['settings' => ['general' => ['require' => ['approval' => 'no']]]]
    );

    expect($imported)->toBeTrue();
    expect(glsr_get_option('general.require.approval'))->toBe('no');
});

test('and it does not import the version, because the version is not a setting', function () {
    // THE assertion of this section. A settings file exported from a site running 7.0 carries
    // `version: 7.0` in it. Import that onto a site running 8.1 and the plugin now believes it has
    // been DOWNGRADED — so it runs every migration from 7.0 forwards, against a database that has
    // already had them. The same applies to version_upgraded_from.
    commitsTransaction();
    $options = glsr(OptionManager::class);
    $options->set('version', glsr()->version);
    $options->set('version_upgraded_from', '8.0.0');

    protectedMethod(ImportSettings::class, 'import')->invoke(glsr(ImportSettings::class), [
        'settings' => [],
        'version' => '7.0.0',                // from the file…
        'version_upgraded_from' => '6.0.0',  // …and so is this
    ]);

    expect($options->get('version'))->toBe(glsr()->version)      // …and neither was believed
        ->and($options->get('version_upgraded_from'))->toBe('8.0.0');
});

test('an empty file imports nothing, rather than wiping the settings', function () {
    // An empty or malformed JSON file must not be read as "the person wants no settings".
    $imported = protectedMethod(ImportSettings::class, 'import')
        ->invoke(glsr(ImportSettings::class), []);

    expect($imported)->toBeFalse();
});

test('an addon can import its own data alongside the settings', function () {
    // `import/settings/extra`. The addons keep some of their data outside the settings row, and a
    // restore that brought back the settings without it would leave the site half-configured.
    commitsTransaction();
    $extra = new ArrayObject();
    add_action('site-reviews/import/settings/extra', fn ($data) => $extra->append($data));

    protectedMethod(ImportSettings::class, 'import')->invoke(glsr(ImportSettings::class), [
        'extra' => ['an-addon' => ['some' => 'data']],
        'settings' => [],
    ]);

    expect($extra)->toHaveCount(1)
        ->and($extra[0])->toBe(['an-addon' => ['some' => 'data']]);
});

/*
 * The migration banner, which is the only notice that does any work.
 */

test('a pending migration is announced, and cannot be dismissed', function () {
    // It cannot be dismissed because it is not an opinion: until the migration runs, the plugin is
    // reading a database it does not fully understand. Hiding that would be hiding the reason the
    // site is misbehaving.
    // The screen is a precondition, not scenery: MigrationNotice::canLoad() calls parent::canLoad()
    // FIRST, and that refuses on any screen the plugin does not own. Without it this test would be
    // asserting that a notice does not load on the front end — which is true, and not the point.
    set_current_screen('edit-'.glsr()->post_type);
    NullQueue::$isPending = true;

    $notice = new MigrationNotice();

    expect(false !== has_action('admin_notices', [$notice, 'render']))->toBeTrue();

    ob_start();
    $notice->render();

    expect((string) ob_get_clean())->not->toContain('is-dismissible');

    set_current_screen('front');
});

test('and a site with nothing to migrate is not told about migrations', function () {
    // Which is every site, on every admin page load, forever after the first one.
    NullQueue::$isPending = false;
    set_current_screen('edit-'.glsr()->post_type);

    $notice = new MigrationNotice();

    expect(has_action('admin_notices', [$notice, 'render']))->toBeFalse();

    set_current_screen('front');
});
