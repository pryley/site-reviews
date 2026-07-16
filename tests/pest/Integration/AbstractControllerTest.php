<?php

use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * "Am I on the right screen?" — asked nine ways, by every controller.
 *
 * These predicates guard nearly every hook the plugin registers, and WordPress fires those on every
 * admin page. A predicate that answers `true` too readily makes the plugin act on someone else's
 * screen — how a reviews plugin ends up renaming the Publish button on a WooCommerce product.
 *
 * The distinction, and why there are nine not three:
 *
 *   str_starts_with(…, glsr()->post_type)   "a review screen OR AN ADDON'S" — addons register post
 *                                           types with the same prefix and want the assets/notices.
 *   glsr()->post_type === …                 "a review screen, and only that."
 *
 * Wrong way round is silent both ways: too loose, the plugin acts on an addon's screen it knows
 * nothing about; too tight, the addon's screen loses the styles, scripts and notices it relied on
 * the parent for. They also read the SCREEN and the QUERY STRING, which do not always agree —
 * isAdminPage() reads ?post_type= from the URL, isAdminScreen() reads the WP_Screen; a review's edit
 * page has no post_type in the URL, only ?post=123.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front');
    $_GET = [];
});

/**
 * A controller, with the predicates every controller inherits made reachable.
 */
function controller(): object
{
    return new class() extends AbstractController {
        public function __call(string $method, array $args)
        {
            return $this->{$method}(...$args);
        }
    };
}

/*
 * Executing a command.
 */

test('a controller executes the command it is given, and hands it back', function () {
    // Two lines, and every route in the plugin goes through them: the controller does not KNOW
    // what a command does, it just runs it and returns it so the route can read the response.
    $review = createReview(['is_approved' => false]);

    $command = controller()->execute(new ToggleStatus(new Request([
        'post_id' => $review->ID,
        'status' => 'publish',
    ])));

    expect($command)->toBeInstanceOf(ToggleStatus::class);
    expect(glsr_get_review($review->ID)->is_approved)->toBeTrue(); // it really ran
});

/*
 * The screens.
 */

test('a review list table is a review list table, and a post list table is not', function () {
    set_current_screen('edit-'.glsr()->post_type);
    expect(controller()->isListTable())->toBeTrue()
        ->and(controller()->isReviewListTable())->toBeTrue()
        ->and(controller()->isAdminScreen())->toBeTrue();

    set_current_screen('edit-post');
    expect(controller()->isListTable())->toBeFalse()
        ->and(controller()->isReviewListTable())->toBeFalse()
        ->and(controller()->isAdminScreen())->toBeFalse();
});

test('a review editor is a review editor, and a post editor is not', function () {
    set_current_screen(glsr()->post_type);
    expect(controller()->isEditor())->toBeTrue()
        ->and(controller()->isReviewEditor())->toBeTrue();

    set_current_screen('post');
    expect(controller()->isEditor())->toBeFalse()
        ->and(controller()->isReviewEditor())->toBeFalse();
});

test('the list table is not the editor, and the editor is not the list table', function () {
    // Both are "a review screen". They are not the same screen, and the metaboxes, assets and
    // notices that belong on one do not belong on the other.
    set_current_screen('edit-'.glsr()->post_type);
    expect(controller()->isListTable())->toBeTrue()
        ->and(controller()->isEditor())->toBeFalse();

    set_current_screen(glsr()->post_type);
    expect(controller()->isEditor())->toBeTrue()
        ->and(controller()->isListTable())->toBeFalse();
});

test('the dashboard counts as a notice screen, and nothing else does', function () {
    // The dashboard is where the retirement notices have to appear, because it is the one screen
    // every administrator opens — but it is not a review screen, and nothing else should treat it
    // as one.
    set_current_screen('dashboard');
    expect(controller()->isNoticeAdminScreen())->toBeTrue()
        ->and(controller()->isAdminScreen())->toBeFalse();

    set_current_screen('edit-post');
    expect(controller()->isNoticeAdminScreen())->toBeFalse();
});

/*
 * The query string, which is not the same thing as the screen.
 */

test('the admin page is read from ?post_type= in the url', function () {
    set_current_screen('edit-'.glsr()->post_type);
    $_GET['post_type'] = glsr()->post_type;

    expect(controller()->isAdminPage())->toBeTrue()
        ->and(controller()->isReviewAdminPage())->toBeTrue();
});

test('and a url for somebody else\'s post type is not the plugin\'s page', function () {
    set_current_screen('edit-post');
    $_GET['post_type'] = 'page';

    expect(controller()->isAdminPage())->toBeFalse()
        ->and(controller()->isReviewAdminPage())->toBeFalse();
});

test('none of it is true on the front end, whatever the query string says', function () {
    // isAdmin() first. Every one of these is a guard on an ADMIN hook, and the front end is where
    // the visitors are — a plugin that decided it was on its own admin page because a visitor put
    // ?post_type=site-review in the URL would be taking instructions from strangers.
    set_current_screen('front');
    $_GET['post_type'] = glsr()->post_type;

    expect(controller()->isAdminPage())->toBeFalse()
        ->and(controller()->isReviewAdminPage())->toBeFalse();
});

test('an ajax request is not an admin page either, though it runs in wp-admin', function () {
    // Every ajax request a VISITOR makes goes through wp-admin/admin-ajax.php, so is_admin() is
    // true for all of them. Treating one as an admin page would run admin-only code for a guest.
    set_current_screen('edit-'.glsr()->post_type);
    $_GET['post_type'] = glsr()->post_type;
    add_filter('wp_doing_ajax', '__return_true');

    expect(controller()->isAdminPage())->toBeFalse();
});

test('the post being edited is read from ?post= in the url', function () {
    $review = createReview();
    $_GET['post'] = (string) $review->ID;

    expect(controller()->getPostId())->toBe($review->ID);
});

test('and a url with no post at all is nothing, not a fatal', function () {
    // filter_input() returns null for a key that is not there, and intval(null) is 0 — which is a
    // review id nobody has. Every caller checks.
    expect(controller()->getPostId())->toBe(0);
});

/*
 * The main query.
 */

test('the reviews list query is the plugin\'s to modify, and only on the list screen', function () {
    // hasQueryPermission() is what lets the plugin add its filters, ordering and joins to the
    // reviews list — and it insists on all four conditions, because pre_get_posts fires for every
    // query on every page, including the sidebar widgets on the front end.
    global $pagenow;
    $pagenow = 'edit.php';
    set_current_screen('edit-'.glsr()->post_type);

    // is_main_query() is not a flag, it is an IDENTITY: WP_Query::is_main_query() returns
    // `$wp_the_query === $this`. Setting a property called is_main_query does nothing at all, which
    // is exactly the mistake somebody reading this code would make — and the reason the plugin's
    // own check is worth a test rather than an assumption.
    $query = new WP_Query();
    $query->set('post_type', glsr()->post_type);
    $GLOBALS['wp_the_query'] = $query;

    expect(controller()->hasQueryPermission($query))->toBeTrue();
});

test('and a query for somebody else\'s post type is not', function () {
    global $pagenow;
    $pagenow = 'edit.php';
    set_current_screen('edit-post');

    $query = new WP_Query();
    $query->set('post_type', 'post');
    $GLOBALS['wp_the_query'] = $query; // it IS the main query, and still not the plugin's

    expect(controller()->hasQueryPermission($query))->toBeFalse();
});

test('and a query that is not the MAIN query is not, even on the right screen', function () {
    // A secondary WP_Query is somebody's widget, shortcode or related-posts block. Rewriting it
    // because it happens to ask for reviews would change output the plugin was never asked about.
    global $pagenow;
    $pagenow = 'edit.php';
    set_current_screen('edit-'.glsr()->post_type);

    $query = new WP_Query();
    $query->set('post_type', glsr()->post_type);

    expect(controller()->hasQueryPermission($query))->toBeFalse();
});

test('and a query on any page other than edit.php is not', function () {
    global $pagenow;
    $pagenow = 'post.php';
    set_current_screen('edit-'.glsr()->post_type);

    $query = new WP_Query();
    $query->set('post_type', glsr()->post_type);
    $GLOBALS['wp_the_query'] = $query;

    expect(controller()->hasQueryPermission($query))->toBeFalse();
});

/*
 * Somebody else's post type, which is not a review.
 */

test('an ordinary post is not a review, however it is asked about', function () {
    $postId = createPost();
    $GLOBALS['post'] = get_post($postId);
    set_current_screen('post');

    expect(controller()->isReviewEditor())->toBeFalse()
        ->and(controller()->isReviewAdminPage())->toBeFalse()
        ->and(controller()->isAdminScreen())->toBeFalse();
});
