<?php

use GeminiLabs\SiteReviews\Controllers\DashboardController;
use GeminiLabs\SiteReviews\Controllers\MetaboxController;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Metaboxes\AssignedPostsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AssignedUsersMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AuthorMetabox;
use GeminiLabs\SiteReviews\Metaboxes\DashboardMetabox;
use GeminiLabs\SiteReviews\Metaboxes\DetailsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * wp_add_dashboard_widget() lives in wp-admin/includes/dashboard.php, which — unlike the rest of the
 * admin API — is loaded only by wp-admin/index.php. So the dashboard is the one admin screen whose
 * functions a process not rendering it lacks. require_once is idempotent and the file registers no
 * hooks at load.
 */
require_once ABSPATH.'wp-admin/includes/dashboard.php';

/*
 * The boxes on the review editor, and the widget on the dashboard.
 *
 * A review is not an ordinary post: written by a non-WordPress-user, about a page they were looking
 * at, and edited by someone moderating rather than authoring. So WordPress's author and slug boxes
 * are removed and five of the plugin's own put there. Each registers only for a review —
 * add_meta_boxes_{post_type} fires with a post, and a box that does not check lands on whatever post
 * type shares the hook. The nonces are the point here: each box that SAVES has its own, separate
 * from the post's, because each is written back by a different part of ReviewController::updateReview().
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen(glsr()->post_type); // the review editor
    $GLOBALS['wp_meta_boxes'] = [];
});

afterEach(function () {
    set_current_screen('front');
    unset($GLOBALS['wp_meta_boxes']);
});

/**
 * The boxes registered for a screen, by id.
 *
 * @return string[]
 */
function registeredMetaboxes(string $screen): array
{
    global $wp_meta_boxes;
    $ids = [];
    foreach ($wp_meta_boxes[$screen] ?? [] as $context) {
        foreach ($context as $priority) {
            foreach ((array) $priority as $id => $box) {
                if (!empty($box)) {
                    $ids[] = $id;
                }
            }
        }
    }

    return $ids;
}

function renderedMetabox(object $metabox, WP_Post $post): string
{
    ob_start();
    $metabox->render($post);

    return (string) ob_get_clean();
}

/*
 * Which boxes a review gets.
 */

test('a review gets the plugin\'s five boxes', function () {
    $review = createReview();

    glsr(MetaboxController::class)->registerMetaBoxes(get_post($review->ID));

    $ids = registeredMetaboxes(glsr()->post_type);

    foreach (['detailsdiv', 'responsediv', 'postsdiv', 'usersdiv', 'authordiv'] as $box) {
        expect($ids)->toContain(glsr()->post_type.'-'.$box);
    }
});

test('an ordinary post does not get them, even though it shares the hook', function () {
    // add_meta_boxes_{$post_type} is fired with the post, and every one of the five
    // checks Review::isReview() before registering. Without that, a plugin that fires the
    // hook for its own post type would get five boxes it never asked for.
    glsr(MetaboxController::class)->registerMetaBoxes(get_post(createPost()));

    expect(registeredMetaboxes(glsr()->post_type))->toBe([]);
});

test('wordpress\'s own author and slug boxes are taken off the review editor', function () {
    global $wp_meta_boxes;
    add_meta_box('authordiv', 'Author', '__return_null', glsr()->post_type, 'normal');
    add_meta_box('slugdiv', 'Slug', '__return_null', glsr()->post_type, 'normal');
    add_meta_box('authordiv', 'Author', '__return_null', 'post', 'normal');

    glsr(MetaboxController::class)->removeMetaBoxes(glsr()->post_type);

    expect(registeredMetaboxes(glsr()->post_type))->toBe([]);
    expect(registeredMetaboxes('post'))->toContain('authordiv'); // and nobody else's are touched

    glsr(MetaboxController::class)->removeMetaBoxes('post'); // called for every post type
    expect(registeredMetaboxes('post'))->toContain('authordiv');
});

/*
 * What is in them.
 */

test('the details box holds everything about a review that is not a post', function () {
    // NOT the title and the content — those are a post's own, and WordPress edits them
    // with its own title field and editor. This box is for the things a review has and a
    // post does not: who wrote it, how they can be reached, what they rated it, and where
    // they were (config/forms/metabox-fields.php).
    $review = createReview([
        'email' => 'jane@example.org',
        'ip_address' => '127.0.0.1',
        'name' => 'Jane Doe',
        'rating' => 4,
    ]);

    $html = renderedMetabox(glsr(DetailsMetabox::class), get_post($review->ID));

    expect($html)->toContain('name="site-reviews[rating]"')
        ->toContain('name="site-reviews[name]"')
        ->toContain('name="site-reviews[email]"')
        ->toContain('value="Jane Doe"')
        ->toContain('value="jane@example.org"');
});

test('the assigned pages box lists the pages, and can be posted back', function () {
    // post_ids[] is what ReviewController::updateReview() reads to work out what the
    // review is assigned to now.
    $postId = createPost(['post_title' => 'The Reviewed Page']);
    $review = createReview(['assigned_posts' => $postId]);

    $html = renderedMetabox(glsr(AssignedPostsMetabox::class), get_post($review->ID));

    expect($html)->toContain('The Reviewed Page')
        ->toContain('post_ids[]')
        ->toContain('_nonce-assigned-posts');
});

test('the assigned users box lists the users', function () {
    $userId = createUser(['display_name' => 'Jane Doe']);
    $review = createReview(['assigned_users' => $userId]);

    $html = renderedMetabox(glsr(AssignedUsersMetabox::class), get_post($review->ID));

    expect($html)->toContain('Jane Doe')
        ->toContain('user_ids[]')
        ->toContain('_nonce-assigned-users');
});

test('the author box is the wordpress user the review belongs to, not the name on it', function () {
    // The two are different things and the box is easy to misread: the "author" here is
    // the WordPress user the review is attributed to, which for a review left by a
    // stranger is nobody at all. The name they typed is in the details box.
    $userId = createUser(['display_name' => 'Jane Doe']);
    $review = createReview(['author_id' => $userId, 'name' => 'Somebody Else']);

    $html = renderedMetabox(glsr(AuthorMetabox::class), get_post($review->ID));

    expect($html)->toContain('post_author_override');
});

test('the response box carries a nonce of its own', function () {
    // Because the response is the one thing on the screen that is published to the front
    // end as the site owner, and it is saved by its own code path.
    $review = createReview(['response' => 'Sorry to hear that.']);

    $html = renderedMetabox(glsr(ResponseMetabox::class), get_post($review->ID));

    expect($html)->toContain('Sorry to hear that.')
        ->toContain('_nonce-response');
});

test('a pinned review says so in the publish box', function () {
    // post_submitbox_misc_actions fires for every post type there is, so the first thing
    // it does is check this one is a review.
    $review = createReview(['is_pinned' => true]);

    ob_start();
    glsr(MetaboxController::class)->renderPinnedAction(get_post($review->ID));
    $pinned = (string) ob_get_clean();

    expect($pinned)->toContain('is_pinned');

    ob_start();
    glsr(MetaboxController::class)->renderPinnedAction(get_post(createPost()));

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The dashboard widget.
 */

test('the dashboard widget is registered', function () {
    global $wp_meta_boxes;
    set_current_screen('dashboard');

    glsr(DashboardController::class)->registerMetaBoxes();

    expect(registeredMetaboxes('dashboard'))->toContain(glsr()->prefix.'dashboard_widget');
});

test('the dashboard widget counts what has come in', function () {
    // Three numbers, and each one is a link to the reviews it counts — the whole point of
    // the widget is that somebody who has just logged in can see there is something
    // waiting and click straight through to it.
    createReview(['rating' => 5]);                       // published
    createReview(['rating' => 1, 'is_approved' => false]); // awaiting approval

    ob_start();
    glsr(DashboardMetabox::class)->render();
    $html = (string) ob_get_clean();

    expect($html)->toContain('in total')
        ->toContain('this month')
        ->toContain('awaiting approval')
        ->toContain('count-1')  // one published, and one awaiting
        ->toContain('count-2')  // and both of them arrived this month
        ->toContain('post_status=pending'); // the link through to the ones to moderate
});

test('the dashboard count is recalculated when a review changes status', function () {
    // The monthly count is cached, because it is a GROUP BY over every review on the site
    // and the dashboard is the first page an administrator sees. Approving a review has to
    // drop that cache, or the number stays wrong until the cache expires.
    $review = createReview(['is_approved' => false]);
    glsr(Cache::class)->store('monthly', 'count', ['stale' => true]);

    wp_update_post(['ID' => $review->ID, 'post_status' => 'publish']);

    expect(glsr(Cache::class)->get('monthly', 'count'))->toBeFalse();
});
