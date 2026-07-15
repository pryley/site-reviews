<?php

use GeminiLabs\SiteReviews\Commands\ApproveReview;
use GeminiLabs\SiteReviews\Commands\AssignPosts;
use GeminiLabs\SiteReviews\Commands\AssignTerms;
use GeminiLabs\SiteReviews\Commands\AssignUsers;
use GeminiLabs\SiteReviews\Commands\ChangeLogLevel;
use GeminiLabs\SiteReviews\Commands\ClearConsole;
use GeminiLabs\SiteReviews\Commands\MigratePlugin;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RepairPermissions;
use GeminiLabs\SiteReviews\Commands\RepairReviewRelations;
use GeminiLabs\SiteReviews\Commands\ResetAssignedMeta;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Commands\ToggleVerified;
use GeminiLabs\SiteReviews\Commands\UnassignPosts;
use GeminiLabs\SiteReviews\Commands\UnassignTerms;
use GeminiLabs\SiteReviews\Commands\UnassignUsers;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createTerm;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The commands, executed directly — which is what a controller does with them
 * (AbstractController::execute() simply calls handle() and hands the command
 * back). Each one is checked on the state it changed, not just on its own
 * successful() flag, so that a command which quietly does nothing cannot pass.
 *
 * Most of them are capability-gated (glsr()->can('edit_post'), 'publish_post',
 * 'assign_post'…), so a permitted user is the precondition for the happy path
 * and a logged-out one is the failure case.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

test('approves a pending review', function () {
    $review = createReview(['is_approved' => false]);
    expect($review->status)->toBe('pending');
    $command = new ApproveReview(glsr_get_review($review->ID));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(get_post_status($review->ID))->toBe('publish');
});

test('refuses to approve a review that is already approved', function () {
    $review = createReview(['is_approved' => true]);
    $command = new ApproveReview(glsr_get_review($review->ID));
    $command->handle();
    expect($command->successful())->toBeFalse();
});

test('refuses to approve a review the user may not edit', function () {
    $review = createReview(['is_approved' => false]);
    wp_set_current_user(createUser(['role' => 'subscriber'])); // cannot edit others' reviews

    $command = new ApproveReview(glsr_get_review($review->ID));
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(get_post_status($review->ID))->toBe('pending'); // not approved
});

test('reports failure when WordPress refuses the status update', function () {
    // The is_wp_error guard: wp_update_post can fail for reasons the command cannot foresee, and a
    // failure must be reported, not swallowed into a false success. Forcing the empty-content check
    // makes wp_update_post return a WP_Error without depending on anything else going wrong.
    $review = createReview(['is_approved' => false]);
    add_filter('wp_insert_post_empty_content', '__return_true');

    $command = new ApproveReview(glsr_get_review($review->ID));
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(get_post_status($review->ID))->toBe('pending'); // the update did not take
});

test('toggles a review status to approved', function () {
    // ToggleStatusDefaults::finalize() maps approve/publish to publish, and
    // anything else to pending.
    $review = createReview(['is_approved' => false]);
    $command = new ToggleStatus(new Request([
        'post_id' => $review->ID,
        'status' => 'approve',
    ]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(get_post_status($review->ID))->toBe('publish');
});

test('toggles a review status to unapproved', function () {
    $review = createReview(['is_approved' => true]);
    $command = new ToggleStatus(new Request([
        'post_id' => $review->ID,
        'status' => 'unapprove',
    ]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(get_post_status($review->ID))->toBe('pending');
});

test('refuses to toggle the status of a review that does not exist', function () {
    $command = new ToggleStatus(new Request([
        'post_id' => 999999001,
        'status' => 'approve',
    ]));
    $command->handle();
    expect($command->successful())->toBeFalse();
});

test('refuses to toggle a status without permission', function () {
    $review = createReview(['is_approved' => false]);
    wp_set_current_user(0);
    $command = new ToggleStatus(new Request([
        'post_id' => $review->ID,
        'status' => 'approve',
    ]));
    $command->handle();
    expect($command->successful())->toBeFalse();
    expect(get_post_status($review->ID))->toBe('pending'); // unchanged
});

test('pins and unpins a review', function () {
    $review = createReview();
    expect($review->is_pinned)->toBeFalse();

    $command = new TogglePinned(new Request(['post_id' => $review->ID, 'pinned' => 1]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(glsr_get_review($review->ID)->is_pinned)->toBeTrue();

    $command = new TogglePinned(new Request(['post_id' => $review->ID, 'pinned' => 0]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(glsr_get_review($review->ID)->is_pinned)->toBeFalse();
});

test('toggles the pinned state when no value is given', function () {
    // pinned defaults to -1, which means "the opposite of what it is now".
    $review = createReview();
    $command = new TogglePinned(new Request(['post_id' => $review->ID]));
    $command->handle();
    expect(glsr_get_review($review->ID)->is_pinned)->toBeTrue();
});

test('refuses to pin a review that does not exist', function () {
    $command = new TogglePinned(new Request(['post_id' => 999999001, 'pinned' => 1]));
    $command->handle();
    expect($command->successful())->toBeFalse();
});

test('verifies a review when verification is enabled', function () {
    // ToggleVerified bails unless the verification/enabled filter says otherwise.
    add_filter('site-reviews/verification/enabled', '__return_true');
    $review = createReview();
    expect($review->is_verified)->toBeFalse();
    $command = new ToggleVerified(new Request(['post_id' => $review->ID, 'verified' => 1]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(glsr_get_review($review->ID)->is_verified)->toBeTrue();
});

test('refuses to verify a review when verification is disabled', function () {
    $review = createReview();
    $command = new ToggleVerified(new Request(['post_id' => $review->ID, 'verified' => 1]));
    $command->handle();
    expect($command->successful())->toBeFalse();
    expect(glsr_get_review($review->ID)->is_verified)->toBeFalse();
});

test('assigns and unassigns posts', function () {
    $review = createReview();
    $postId = createPost();

    (new AssignPosts(glsr_get_review($review->ID), [$postId]))->handle();
    expect(do_shortcode("[site_reviews assigned_posts={$postId}]"))
        ->toContain('id="review-'.$review->ID.'"');

    (new UnassignPosts(glsr_get_review($review->ID), [$postId]))->handle();
    expect(do_shortcode("[site_reviews assigned_posts={$postId}]"))
        ->not->toContain('id="review-'.$review->ID.'"');
});

test('assigns and unassigns categories', function () {
    $review = createReview();
    $termId = createTerm(['taxonomy' => glsr()->taxonomy]);

    (new AssignTerms(glsr_get_review($review->ID), [$termId]))->handle();
    expect(do_shortcode("[site_reviews assigned_terms={$termId}]"))
        ->toContain('id="review-'.$review->ID.'"');

    (new UnassignTerms(glsr_get_review($review->ID), [$termId]))->handle();
    expect(do_shortcode("[site_reviews assigned_terms={$termId}]"))
        ->not->toContain('id="review-'.$review->ID.'"');
});

test('assigns and unassigns users', function () {
    $review = createReview();
    $userId = createUser();

    (new AssignUsers(glsr_get_review($review->ID), [$userId]))->handle();
    expect(do_shortcode("[site_reviews assigned_users={$userId}]"))
        ->toContain('id="review-'.$review->ID.'"');

    (new UnassignUsers(glsr_get_review($review->ID), [$userId]))->handle();
    expect(do_shortcode("[site_reviews assigned_users={$userId}]"))
        ->not->toContain('id="review-'.$review->ID.'"');
});

test('changes the console log level', function () {
    // Console levels: DEBUG 0, INFO 1, NOTICE 2, WARNING 4 — note 3 is not one.
    $command = new ChangeLogLevel(new Request(['level' => Console::NOTICE]));
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::NOTICE);
});

test('refuses to set a log level that does not exist', function () {
    $command = new ChangeLogLevel(new Request(['level' => 3]));
    $command->handle();
    expect($command->successful())->toBeFalse();
});

test('refuses to change the console level without permission', function () {
    // hasPermission() only asks the question on an admin screen; a subscriber there cannot.
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $command = new ChangeLogLevel(new Request(['level' => Console::NOTICE]));
    $command->handle();

    expect($command->successful())->toBeFalse();
    set_current_screen('front');
});

test('clears the console', function () {
    glsr_log()->error('Something went wrong.');
    expect(glsr(Console::class)->get())->toContain('Something went wrong.');
    $command = new ClearConsole();
    $command->handle();
    expect($command->successful())->toBeTrue();
    expect(glsr(Console::class)->get())->not->toContain('Something went wrong.');
});

test('refuses to clear the console without permission', function () {
    glsr_log()->error('A logged error.');
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $command = new ClearConsole();
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(glsr(Console::class)->get())->toContain('A logged error.'); // not cleared
    set_current_screen('front');
});

test('refuses to migrate the plugin without permission', function () {
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $command = new MigratePlugin(new Request());
    $command->handle();

    expect($command->successful())->toBeFalse();
    set_current_screen('front');
});

test('the register commands fail rather than fatal when their directory is missing', function () {
    // Each scans a plugin subdirectory for classes to register; a build that shipped without one of
    // those directories must degrade, not crash. The path is filtered to somewhere that is not there.
    $missDir = fn (string $dir) => add_filter('site-reviews/path', function ($path, $file) use ($dir) {
        return $dir === $file ? '/no/such/directory' : $path;
    }, 10, 2);

    $missDir('plugin/Shortcodes');
    $shortcodes = new RegisterShortcodes();
    $shortcodes->handle();

    $missDir('plugin/Tinymce');
    $tinymce = new RegisterTinymcePopups();
    $tinymce->handle();

    $missDir('plugin/Widgets');
    $widgets = new RegisterWidgets();
    $widgets->handle();

    expect($shortcodes->successful())->toBeFalse()
        ->and($tinymce->successful())->toBeFalse()
        ->and($widgets->successful())->toBeFalse();
});

test('the alt flag makes a repair a hard reset of every role', function () {
    // The plain repair tops up the review capabilities; the "alt" hard reset rebuilds them from
    // scratch, which is what a site reaches for when a role's caps have been mangled beyond a top-up.
    get_role('editor')->remove_cap('edit_others_site-reviews');

    $command = new RepairPermissions(new Request(['alt' => 1]));
    $command->handle();

    expect($command->successful())->toBeTrue()
        ->and(get_role('editor')->has_cap('edit_others_site-reviews'))->toBeTrue();
});

test('repairing the review relations needs permission', function () {
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $command = new RepairReviewRelations();
    $command->handle();

    expect($command->successful())->toBeFalse();
    set_current_screen('front');
});

test('resetting the assigned meta needs permission', function () {
    set_current_screen('site-review_page_'.glsr()->id.'-tools');
    wp_set_current_user(createUser(['role' => 'subscriber']));

    $command = new ResetAssignedMeta();
    $command->handle();

    expect($command->successful())->toBeFalse();
    set_current_screen('front');
});
