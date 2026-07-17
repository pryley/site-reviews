<?php

/*
 * Boots the MULTISITE wp-env instance (tests/multisite/.wp-env.json — its own
 * containers on port 8892, converted to a network by `make test:multisite`).
 * This suite exists for the branches the main suite structurally cannot reach:
 * is_multisite() paths (Install, NetworkController, per-blog constraint names)
 * and the EMPTY_TRASH_DAYS=0 guard (a first-define constant, 0 in this env's
 * wp-config and 30 in the main one).
 *
 * No transactions, no stubs, no factories: the environment is dedicated and
 * disposable, and every test here is written to be re-runnable — anything it
 * breaks (constraints, tables, options) it restores.
 */

define('GLSR_UNIT_TESTS', true);
define('WP_HTTP_BLOCK_EXTERNAL', true);
define('WP_ACCESSIBLE_HOSTS', '');

$root = rtrim((string) (getenv('WP_ROOT') ?: '/var/www/html'), '/');
if (!file_exists("{$root}/wp-load.php")) {
    fwrite(STDOUT, "wp-load.php not found at: {$root}. Run this suite with `make test:multisite`.".PHP_EOL);
    exit(1);
}

// Shims WP expects from a web SAPI when loaded from the CLI. The host MUST
// match DOMAIN_CURRENT_SITE ('localhost:8892' — the port in .wp-env.json is
// part of the network domain): ms-settings.php answers any other host with a
// redirect and a bare exit, which a CLI process reports as a silent success.
$_SERVER['HTTP_HOST'] ??= 'localhost:8892';
$_SERVER['REMOTE_ADDR'] ??= '127.0.0.1';
$_SERVER['REQUEST_METHOD'] ??= 'GET';
$_SERVER['REQUEST_URI'] ??= '/';
$_SERVER['SERVER_NAME'] ??= 'localhost';
$_SERVER['SERVER_PROTOCOL'] ??= 'HTTP/1.1';

require "{$root}/wp-load.php";
require_once ABSPATH.'wp-admin/includes/plugin.php';

if (!function_exists('glsr')) {
    fwrite(STDOUT, 'The Site Reviews plugin is not active in the multisite WordPress.'.PHP_EOL);
    exit(1);
}
if (!is_multisite()) {
    fwrite(STDOUT, implode(PHP_EOL, [
        'This WordPress is not a multisite network yet. Run:',
        '    make test:multisite',
        'which converts it before running the suite.',
        '',
    ]));
    exit(1);
}
if (0 !== EMPTY_TRASH_DAYS) {
    fwrite(STDOUT, 'Expected EMPTY_TRASH_DAYS=0 from tests/multisite/.wp-env.json.'.PHP_EOL);
    exit(1);
}

// The network needs the plugin network-activated (Install::run() and
// MainController::installOnNewSite() both branch on it) and a second site
// (per-blog branches are invisible on blog 1).
if (!is_plugin_active_for_network(glsr()->basename)) {
    activate_plugin(glsr()->basename, '', true);
}
if (count(get_sites(['count' => false, 'fields' => 'ids'])) < 2) {
    $siteId = wp_insert_site([
        'domain' => 'localhost:8892', // subdirectory install: same domain as the network
        'path' => '/second/',
        'title' => 'Second Site',
    ]);
    if (is_wp_error($siteId)) {
        fwrite(STDOUT, 'Could not create the second site: '.$siteId->get_error_message().PHP_EOL);
        exit(1);
    }
}
