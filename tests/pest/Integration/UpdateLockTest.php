<?php

use GeminiLabs\SiteReviews\Controllers\UpdateController;
use GeminiLabs\SiteReviews\Modules\UpdateLock;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\swapInstance;
use function GeminiLabs\SiteReviews\Tests\unregisterAddons;

uses()->group('plugin');

/*
 * A plugin folder that holds a git repository is managed by git. WordPress's updater replaces
 * the folder, through a symlink if there is one, and the repository is gone; on 2026-10-13 a
 * rollback did exactly that to this checkout. So no update and no rollback is offered for
 * such a folder, and the updater refuses as a backstop on the paths that skip the update list.
 *
 * wp-env mounts this repository as the plugin, so Site Reviews is locked in every test. Tests
 * of the unlocked state swap a fake into the container.
 */

beforeEach(fn () => resetPluginState());

function lockFixture(bool $withGit): string
{
    $dir = WP_PLUGIN_DIR.'/glsr-lock-fixture';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    if ($withGit) {
        file_put_contents("{$dir}/.git", 'gitdir: /somewhere/else'); // the .git FILE of a worktree
    }
    return 'glsr-lock-fixture/glsr-lock-fixture.php';
}

function removeLockFixture(): void
{
    @unlink(WP_PLUGIN_DIR.'/glsr-lock-fixture/.git');
    @rmdir(WP_PLUGIN_DIR.'/glsr-lock-fixture');
}

function lockedEverywhere(): UpdateLock
{
    return new class extends UpdateLock {
        public function isLocked(string $basename): bool
        {
            return true;
        }
    };
}

function unlocked(): UpdateLock
{
    return new class extends UpdateLock {
        public function isLocked(string $basename): bool
        {
            return false;
        }
    };
}

test('this checkout is locked, a folder with a .git file is locked, and a folder without is not', function () {
    $lock = new UpdateLock();

    expect($lock->isLocked(glsr()->basename))->toBeTrue();
    try {
        expect($lock->isLocked(lockFixture(withGit: false)))->toBeFalse()
            ->and($lock->isLocked(lockFixture(withGit: true)))->toBeTrue();
    } finally {
        removeLockFixture();
    }
});

test('the locked list names Site Reviews and each registered addon once', function () {
    glsr()->register(TestAddon::class);
    try {
        $locked = lockedEverywhere()->locked();

        expect($locked)->toBe([
            glsr()->basename => glsr()->name,
            glsr(TestAddon::ID)->basename => 'Test Addon',
        ]);
    } finally {
        unregisterAddons(TestAddon::ID);
    }
});

test('the locked list is empty when no folder holds a repository', function () {
    expect(unlocked()->locked())->toBe([]);
});

test('the lock removes the update of a locked plugin after the rollback filter has offered one', function () {
    // The rollback injects its version at priority 10, the lock strips it at 60. Executed through
    // the filter chain so the order is what is proved, not two methods in isolation.
    set_transient(glsr()->prefix.'rollback_version', '8.0.0', MINUTE_IN_SECONDS);
    $updates = (object) ['response' => ['other/other.php' => 'kept']];

    $filtered = apply_filters('site_transient_update_plugins', $updates);

    expect($filtered->response)->toBe(['other/other.php' => 'kept']);
});

test('the lock hands back a value that is not an update list', function () {
    expect(glsr(UpdateController::class)->filterLockedPluginUpdates(false))->toBeFalse();
});

test('the updater refuses a locked plugin before WordPress can deactivate it', function () {
    // WordPress deactivates the plugin on this hook at priority 10 and reactivates it only after
    // a successful update. The refusal at 9 is what a priority-10 callback sees.
    $seen = null;
    add_filter('upgrader_pre_install', function ($response) use (&$seen) {
        $seen = $response;
        return $response;
    }, 10);

    $result = apply_filters('upgrader_pre_install', true, ['plugin' => glsr()->basename, 'type' => 'plugin', 'action' => 'update']);

    expect($result)->toBeInstanceOf(WP_Error::class)
        ->and($result->get_error_message())->toBe('Site Reviews was not updated because its folder is a git repository.')
        ->and($seen)->toBe($result);
});

test('the updater lets an update of another plugin through', function () {
    expect(apply_filters('upgrader_pre_install', true, ['plugin' => 'other/other.php']))->toBeTrue();
});

test('an uploaded zip of a locked plugin is refused by its folder name', function () {
    // An upload names no plugin; the folder inside the zip is what reaches this hook.
    $hookExtra = ['type' => 'plugin', 'action' => 'install'];

    $refused = apply_filters('upgrader_source_selection', '/tmp/upgrade/site-reviews/', '/tmp/upgrade', null, $hookExtra);
    $allowed = apply_filters('upgrader_source_selection', '/tmp/upgrade/other/', '/tmp/upgrade', null, $hookExtra);

    expect($refused)->toBeInstanceOf(WP_Error::class)
        ->and($refused->get_error_message())->toContain('git repository')
        ->and($allowed)->toBe('/tmp/upgrade/other/');
});

test('the plugins page prints a row under a locked plugin', function () {
    require_once ABSPATH.'wp-admin/includes/list-table.php';
    set_current_screen('plugins');
    try {
        ob_start();
        glsr(UpdateController::class)->renderLockedPluginRow(glsr()->basename, ['Name' => 'Site Reviews']);
        $row = (string) ob_get_clean();

        expect($row)->toContain('<tr class="plugin-update-tr plugin-locked-tr active">')
            ->toContain('class="plugin-update colspanchange"')
            ->toMatch('/colspan="\d+"/')
            ->toContain('notice-error')
            ->toContain('Updates are turned off for Site Reviews because its folder is a git repository.');
    } finally {
        set_current_screen('front');
    }
});

test('the plugins page joins the locked row to its plugin\'s row, as an update row is', function () {
    $rule = '.plugins tr:has(+ .plugin-locked-tr),.plugins tr:has(+ .plugin-locked-tr) > *{box-shadow:none}';
    $inline = fn () => implode('', (array) wp_styles()->get_data('list-tables', 'after'));

    glsr(UpdateController::class)->enqueueLockedRowStyle('index.php');
    expect($inline())->not->toContain($rule);

    glsr(UpdateController::class)->enqueueLockedRowStyle('plugins.php');
    expect($inline())->toContain($rule);
});

test('the rollback card shows a notice in place of the form on a locked checkout', function () {
    wp_set_current_user(createUser(['role' => 'administrator'])); // the card needs update_plugins
    $page = glsr()->build('pages/tools/general/rollback-plugin', [
        'rollback_locked' => true,
        'rollback_script' => '',
        'rollback_versions' => ['8.0.0'],
        'rollback_stops' => [],
    ]);

    expect($page)->toContain('Rollback is unavailable because the Site Reviews folder is a git repository.')
        ->not->toContain('id="rollback-plugin"');
});

test('the rollback card shows the form when the checkout is not locked', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $page = glsr()->build('pages/tools/general/rollback-plugin', [
        'rollback_locked' => false,
        'rollback_script' => '',
        'rollback_versions' => ['8.0.0'],
        'rollback_stops' => [],
    ]);

    expect($page)->toContain('id="rollback-plugin"')
        ->not->toContain('git repository');
});

test('the tools page asks the lock about this checkout', function () {
    // The swap proves the page reads the container, which is how the suite unlocks it elsewhere.
    add_filter('plugins_api', fn () => (object) ['versions' => ['8.0.0' => '']], 10, 3);
    wp_set_current_user(createUser(['role' => 'administrator']));

    $unlocked = swapInstance(UpdateLock::class, unlocked(), function () {
        ob_start();
        glsr(\GeminiLabs\SiteReviews\Controllers\MenuController::class)->renderToolsMenuCallback();
        return (string) ob_get_clean();
    });
    ob_start();
    glsr(\GeminiLabs\SiteReviews\Controllers\MenuController::class)->renderToolsMenuCallback();
    $locked = (string) ob_get_clean();

    expect($unlocked)->toContain('id="rollback-plugin"')
        ->and($locked)->toContain('Rollback is unavailable because the Site Reviews folder is a git repository.');
});
