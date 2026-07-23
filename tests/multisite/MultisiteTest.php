<?php

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestReviewController;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Controllers\NetworkController;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Install;

/*
 * The is_multisite() branches, exercised on a real network (see bootstrap.php).
 * These tests share one environment with no transactions: each one restores
 * whatever it breaks, and the file reads top to bottom as install → inspect →
 * deactivate → drop → reinstall.
 */

function siteIds(): array
{
    return array_map('intval', get_sites(['count' => false, 'fields' => 'ids', 'orderby' => 'ids', 'order' => 'ASC']));
}

function secondSiteId(): int
{
    return max(siteIds());
}

test('a network activation installs on every site', function () {
    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        delete_option(glsr()->prefix.'db_version');
        restore_current_blog();
    }

    glsr(Install::class)->run(); // network-activated: the per-site loop

    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        expect(glsr(Tables::class)->tablesExist())->toBeTrue()
            ->and(get_option(glsr()->prefix.'db_version'))->not->toBeEmpty();
        restore_current_blog();
    }
});

test('the foreign constraint names carry the blog id on a subsite', function () {
    expect(glsr(TableRatings::class)->foreignConstraint('review_id'))
        ->not->toContain('_'.secondSiteId());

    switch_to_blog(secondSiteId());
    try {
        expect(glsr(TableRatings::class)->foreignConstraint('review_id'))
            ->toEndWith('_'.secondSiteId());
    } finally {
        restore_current_blog();
    }
});

test('a new site gets the plugin installed on arrival', function () {
    switch_to_blog(secondSiteId());
    delete_option(glsr()->prefix.'db_version');
    restore_current_blog();

    glsr(MainController::class)->installOnNewSite(get_site(secondSiteId()));

    switch_to_blog(secondSiteId());
    try {
        expect(get_option(glsr()->prefix.'db_version'))->not->toBeEmpty();
    } finally {
        restore_current_blog();
    }
});

test('the 5.25 database repair visits every site on a network', function () {
    // repairDatabase() branches on is_plugin_active_for_network() and reinstalls per site —
    // the loop the main suite structurally cannot reach. The migration is an idempotent
    // repair, so the network is left as found.
    $migration = glsr(\GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateDatabase::class);

    expect($migration->run())->toBeTrue();

    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        expect(glsr(Tables::class)->tablesExist())->toBeTrue();
        restore_current_blog();
    }
});

test('a network deactivation cleans every site', function () {
    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        update_option(glsr()->prefix.'activated', true);
        restore_current_blog();
    }

    glsr(Install::class)->deactivate(true);

    try {
        foreach (siteIds() as $siteId) {
            switch_to_blog($siteId);
            expect(get_option(glsr()->prefix.'activated'))->toBeFalse();
            restore_current_blog();
        }
    } finally {
        // deactivate() dropped the foreign constraints on every site; put them back
        foreach (siteIds() as $siteId) {
            switch_to_blog($siteId);
            glsr(Tables::class)->addForeignConstraints();
            restore_current_blog();
        }
    }
});

test('an uninstall drops the tables of every site, and an install rebuilds them', function () {
    // constraints first, as uninstall.php does — the ratings table is the FK
    // parent and refuses to drop while the assignment tables still point at it
    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        glsr(Tables::class)->dropForeignConstraints();
        restore_current_blog();
    }

    glsr(Install::class)->dropTables(true);

    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        expect(glsr(Tables::class)->tablesExist())->toBeFalse();
        restore_current_blog();
    }

    glsr(Install::class)->run(); // leave the network as we found it

    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        expect(glsr(Tables::class)->tablesExist())->toBeTrue();
        restore_current_blog();
    }
});

test('the network admin bar links each of your sites to its reviews', function () {
    require_once ABSPATH.WPINC.'/class-wp-admin-bar.php';

    // logged out: nothing is added
    wp_set_current_user(0);
    $bar = new WP_Admin_Bar();
    $bar->initialize();
    glsr(NetworkController::class)->extendAdminBar($bar);
    expect($bar->get_nodes())->toBeEmpty();

    // a user with no sites and no network rights: nothing is added
    $userId = wp_insert_user(['user_login' => 'no-sites-'.wp_rand(), 'user_pass' => wp_generate_password()]);
    remove_user_from_blog($userId); // wp_insert_user memberships them on the current site
    wp_set_current_user($userId);
    $bar = new WP_Admin_Bar();
    $bar->initialize();
    glsr(NetworkController::class)->extendAdminBar($bar);
    expect($bar->get_nodes())->toBeEmpty();

    // the super admin: a Manage Reviews node per site, and the visit-site node
    // is moved after ours rather than lost
    wp_set_current_user(1);
    $bar = new WP_Admin_Bar();
    $bar->initialize();
    $blogIds = array_keys((array) $bar->user->blogs);
    expect($blogIds)->not->toBeEmpty();
    $firstBlog = $blogIds[0];
    $bar->add_node(['id' => "blog-{$firstBlog}-v", 'title' => 'Visit Site', 'parent' => "blog-{$firstBlog}"]);

    glsr(NetworkController::class)->extendAdminBar($bar);

    expect($bar->get_node("blog-{$firstBlog}-site-reviews"))->not->toBeNull()
        ->and($bar->get_node("blog-{$firstBlog}-v"))->not->toBeNull();

    wp_delete_user($userId);
});

test('deleting a review over rest without force is refused, because this network empties trash immediately', function () {
    // EMPTY_TRASH_DAYS is 0 in this environment's wp-config (tests/multisite/.wp-env.json):
    // WordPress only defines it if nobody else has, and the main suite's WordPress
    // answers 30 — which is why this branch lives in this suite.
    wp_set_current_user(1);
    $review = glsr_create_review(['rating' => 5, 'title' => 'To be deleted']);
    expect($review)->not->toBeFalse();

    $request = new WP_REST_Request('DELETE');
    $request->set_param('id', $review->ID);
    $request->set_param('force', false);

    $result = glsr(RestReviewController::class)->delete_item($request);

    expect($result)->toBeInstanceOf(WP_Error::class)
        ->and($result->get_error_code())->toBe('rest_trash_not_supported');

    wp_delete_post($review->ID, true); // leave nothing behind
});

test('an addon network deactivation cleans every site', function () {
    // the plain-addon fixture (autoloaded from tests/pest/fixtures) stands in for
    // any real addon: onDeactivation(true) must clean the option on every site
    $controller = glsr(\GeminiLabs\SiteReviews\PlainAddon\Controller::class);
    $option = glsr()->prefix.'activated_'.$controller->app()->id;
    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        update_option($option, true);
        restore_current_blog();
    }

    $controller->onDeactivation(true);

    foreach (siteIds() as $siteId) {
        switch_to_blog($siteId);
        expect(get_option($option))->toBeFalse();
        restore_current_blog();
    }
});
