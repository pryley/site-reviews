<?php

use GeminiLabs\SiteReviews\CLI;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Helper;

use function GeminiLabs\SiteReviews\Tests\commitsTransaction;
use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * `wp site-reviews …`
 *
 * Three commands, and they exist because of the two situations where the admin screens cannot
 * help: a site with so many reviews that the browser times out doing this, and a site that is
 * already broken enough that the admin will not load.
 *
 *   ip-address   what IP the server actually sees. This is the first thing support asks for when
 *                somebody behind Cloudflare finds every review recorded from the same address —
 *                the answer tells them what to put in the trusted-proxy setting.
 *   migrate      run the schema migrations. `--force` resets them all and runs them from the
 *                beginning, which is the recovery path when a migration half-ran.
 *   repair       recount the assigned meta, drop rating rows whose review is gone, and put the
 *                capabilities back on the roles.
 *
 * They run with no browser, no user and no screen, so nothing about them fails visibly. WP_CLI is
 * faked in tests/stubs/wp-cli.php — it records what the command told the person instead of
 * printing it — and the assertions below are on exactly that: what a site owner staring at a
 * broken site is told happened.
 */

beforeEach(function () {
    resetPluginState();
});

test('the command is registered with wp-cli, once, at plugin load', function () {
    // site-reviews.php line 34 does `new CLI()` unconditionally, and the constructor is what
    // registers it — gated on class_exists('WP_CLI') so that the ordinary web request, where
    // \WP_CLI::add_command() does not exist, is not a fatal on every page load.
    $registered = array_column(WP_CLI::$commands, 'callable', 'name');

    expect($registered)->toHaveKey(glsr()->id)
        ->and($registered[glsr()->id])->toBe(CLI::class);
});

test('ip-address prints the address the server actually sees', function () {
    // The whole command. It is a one-liner and it is the most-used of the three, because it is the
    // answer to "why is every review coming from 172.70.x.x".
    glsr(CLI::class)->ipAddress();

    expect(WP_CLI::successes())->toBe([Helper::clientIp()]);
});

test('migrate runs the migrations and says so', function () {
    // The ordinary path: nothing to do (the suite migrates at bootstrap), and it still has to
    // report success rather than silence — a command that prints nothing is a command the person
    // will run again.
    glsr(CLI::class)->migrate([], []);

    expect(WP_CLI::successes())->toBe(['The plugin has been migrated.']);
});

test('migrate --force resets every migration and runs them all, leaving the schema intact', function () {
    // The recovery path, and the dangerous one: it DROPS the foreign key constraints before
    // re-running everything. If the migrations did not put them back, the review tables would be
    // left without the cascade that deletes a review's ratings along with the review — silently,
    // on a site whose owner ran this because it was already broken.
    //
    // (This is DDL, so it commits the test's transaction. Declared.)
    commitsTransaction();

    glsr(CLI::class)->migrate([], ['force' => true]);

    expect(WP_CLI::successes())->toBe(['All plugin migrations have been run.']);

    // The schema survived: a review can still be written and read back.
    $review = createReview(['rating' => 4]);
    expect(glsr_get_review($review->ID)->rating)->toBe(4);
});

test('repair recounts, cleans up, and puts the capabilities back', function () {
    // Three steps, three messages, in order — the person watching is meant to be able to tell
    // which of the three is the one that hung.
    glsr(CLI::class)->repair([], []);

    expect(WP_CLI::successes())->toBe([
        'Assigned meta values have been repaired.',
        'Review relationships have been repaired.',
        'User permissions have been repaired.',
    ]);
});

test('repair recalculates the counts a page shows, from the reviews that are actually there', function () {
    // What `repair` is FOR. The counts are cached in post meta, and they go wrong — an import that
    // half-ran, a review deleted directly in the database, a plugin that moved a post. This
    // command is the only way back.
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 5]);
    createReview(['assigned_posts' => $postId, 'rating' => 3]);

    expect((int) get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe(2); // the counts were right…

    delete_post_meta($postId, CountManager::META_AVERAGE); // …and then they went missing
    delete_post_meta($postId, CountManager::META_REVIEWS);

    glsr(CLI::class)->repair([], []);

    expect((int) get_post_meta($postId, CountManager::META_REVIEWS, true))->toBe(2)
        ->and((float) get_post_meta($postId, CountManager::META_AVERAGE, true))->toBe(4.0);
});

test('repair --force removes the capabilities and puts them back, rather than only topping them up', function () {
    // The difference between the two branches, and the reason --force exists: resetAll() only ADDS
    // what is missing, so a capability that was wrongly GRANTED to a role — by an old version, or
    // by another plugin — survives it. hardResetAll() strips them all first. The test is that it
    // strips and restores: a site owner who runs --force must not be left with an administrator
    // who cannot edit reviews.
    $pluginCaps = function () {
        $capabilities = (array) get_role('administrator')->capabilities;
        return array_keys(array_filter(
            $capabilities,
            fn ($granted, $cap) => $granted && str_contains($cap, glsr()->post_type),
            ARRAY_FILTER_USE_BOTH
        ));
    };
    $before = $pluginCaps();
    expect($before)->not->toBeEmpty(); // otherwise the assertion below proves nothing

    glsr(CLI::class)->repair([], ['force' => true]);

    expect(WP_CLI::successes())->toBe([
        'Assigned meta values have been repaired.',
        'Review relationships have been repaired.',
        'User permissions have been reset.',
    ]);
    expect($pluginCaps())->toEqualCanonicalizing($before);
});
