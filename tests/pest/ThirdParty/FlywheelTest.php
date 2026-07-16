<?php

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Flywheel integration: one warning, on one screen.
 *
 * Site Reviews puts foreign key constraints on its custom tables, and Flywheel's migration tool does
 * not carry them over — so a site migrated with the plugin active arrives with its reviews broken.
 * The integration prints a warning on the Flywheel Migrations page telling you to deactivate first.
 *
 * It registers on every site: toplevel_page_flywheel never fires without the Flywheel plugin, so
 * there is nothing to detect or gate on — which is what makes it testable, the action fired here in
 * its place.
 */

beforeEach(function () {
    resetPluginState();
    // wp-includes/vars.php sets $pagenow for every request, CLI included, and
    // nothing in Pest.php restores it — so it is put back rather than unset.
    $this->pagenow = $GLOBALS['pagenow'] ?? null;
});

afterEach(function () {
    $GLOBALS['pagenow'] = $this->pagenow;
});

function flywheelNotice(): string
{
    ob_start();
    do_action('toplevel_page_flywheel');

    return (string) ob_get_clean();
}

test('the warning is printed on the flywheel admin screen', function () {
    $GLOBALS['pagenow'] = 'admin.php';

    expect(flywheelNotice())
        ->toContain('Please deactivate Site Reviews before migrating your website.')
        ->toContain('foreign key constraints');
});

test('the warning is not printed anywhere else', function () {
    // toplevel_page_flywheel is a load-{page} hook, so it can only fire on an
    // admin screen — but the controller checks $pagenow anyway, and this pins that
    // it is the check and not the hook doing the work.
    $GLOBALS['pagenow'] = 'edit.php';

    expect(flywheelNotice())->toBe('');
});
