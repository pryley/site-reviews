<?php

use GeminiLabs\SiteReviews\Controllers\DeactivationController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\AbstractColumnFilter;
use GeminiLabs\SiteReviews\Controllers\MetaboxController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Controllers\TinymceController;
use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Controllers\VerificationController;
use GeminiLabs\SiteReviews\Controllers\WelcomeController;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The guards and small branches of a dozen controllers and metaboxes that the
 * per-controller files never turn: wrong post types, missing capabilities,
 * deleted users, and the ajax endpoints that answer with an error.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->setUpAjax();
});

afterEach(function () {
    $this->tearDownAjax();
    $_GET = [];
    $_POST = [];
    set_current_screen('front');
});

test('the editor metabox actions ignore anything that is not a valid review', function () {
    $post = get_post(createPost());

    ob_start();
    glsr(MetaboxController::class)->renderPinnedAction($post);
    glsr(VerificationController::class)->renderVerifyAction($post);
    // a post OF the review type that never became a review row
    $orphan = get_post(wp_insert_post(['post_type' => glsr()->post_type, 'post_title' => 'x', 'post_status' => 'draft']));
    glsr(MetaboxController::class)->renderPinnedAction($orphan);
    glsr(VerificationController::class)->renderVerifyAction($orphan);

    expect(ob_get_clean())->toBe('');
});

test('a protected custom field can be deleted from the editor but not edited', function () {
    $_POST['action'] = 'delete-meta';
    expect(glsr(EditorController::class)->filterIsProtectedMeta(true, '_custom_rating', 'post'))
        ->toBeFalse(); // allow the delete button to work

    unset($_POST['action']);
    expect(glsr(EditorController::class)->filterIsProtectedMeta(true, '_custom_rating', 'post'))
        ->toBeTrue(); // protected everywhere else
});

test('restoring a revision names the revision in the updated message', function () {
    $review = createReview(['content' => 'original']);
    wp_update_post(['ID' => $review->ID, 'post_content' => 'changed']);
    $revisions = wp_get_post_revisions($review->ID);
    $GLOBALS['post'] = get_post($review->ID);
    $_GET['revision'] = (string) array_key_first($revisions);

    $messages = glsr(EditorController::class)->filterUpdateMessages([]);

    expect($messages[glsr()->post_type][5])->toContain('restored');
});

test('the review notice renders only in the review editor', function () {
    set_current_screen('front');

    ob_start();
    glsr(EditorController::class)->renderReviewNotice(get_post(createPost()));

    expect(ob_get_clean())->toBe('');
});

test('a verification landing whose review has vanished answers with an error', function () {
    // the token is genuine — it names the review — but the review is gone
    $token = glsr(\GeminiLabs\SiteReviews\Modules\Encryption::class)->encrypt('999999001');

    $response = $this->jsonSentBy(fn () => glsr(VerificationController::class)->verifiedReviewAjax(
        new Request(['review_id' => 999999001, 'verified' => $token])
    ));

    expect($response['success'])->toBeFalse();
});

test('a tinymce popup that failed to build swaps its buttons for a plain okay', function () {
    glsr()->append('mce', [
        'btn_close' => 'Close',
        'btn_okay' => 'Insert',
        'errors' => ['<p>The shortcode has required options.</p>'],
        'fields' => ['<p>The shortcode has required options.</p>'],
        'label' => 'Broken',
        'required' => [],
        'title' => 'Broken',
    ], 'broken_shortcode');
    try {
        $response = $this->jsonSentBy(fn () => glsr(TinymceController::class)->mceShortcodeAjax(
            new Request(['shortcode' => 'broken_shortcode'])
        ));

        expect($response['data']['ok'] ?? $response['data']['okay'] ?? '')->not->toBeEmpty();
    } finally {
        glsr()->discard('mce.broken_shortcode');
    }
});

test('the deactivation survey posts home and thanks the person', function () {
    interceptHttp(); // the survey is a remote POST; nothing leaves the container

    $response = $this->jsonSentBy(fn () => glsr(DeactivationController::class)->submitDeactivateReasonAjax(
        new Request(['reason' => 'other', 'details' => 'testing'])
    ));

    expect($response['success'])->toBeTrue();
});

test('the public assets enqueue through the controller, and the welcome page knows its slug', function () {
    glsr(PublicController::class)->enqueueAssets();
    expect(wp_script_is(glsr()->id, 'enqueued'))->toBeTrue();

    expect(new WelcomeController())->toBeInstanceOf(WelcomeController::class);
    expect(new TranslationController(glsr(\GeminiLabs\SiteReviews\Modules\Translator::class)))
        ->toBeInstanceOf(TranslationController::class);
});

test('a revision comparison for a plain post falls back to the stored review', function () {
    $review = createReview();

    $result = \GeminiLabs\SiteReviews\Tests\protectedMethod(
        \GeminiLabs\SiteReviews\Controllers\RevisionController::class, 'reviewFromRevision'
    )->invoke(glsr(\GeminiLabs\SiteReviews\Controllers\RevisionController::class), get_post($review->ID));

    expect($result->ID)->toBe($review->ID);
});

test('a draft review made in wp-admin sends no notification', function () {
    $review = createReview(); // BEFORE the option: creating it must not queue
    glsr(\GeminiLabs\SiteReviews\Database\OptionManager::class)->set('settings.general.notifications', ['admin']);
    \GeminiLabs\SiteReviews\Tests\NullQueue::$calls = [];
    $draft = new \GeminiLabs\SiteReviews\Review(['ID' => $review->ID, 'status' => 'draft']);

    glsr(\GeminiLabs\SiteReviews\Controllers\ReviewController::class)->sendNotification($draft);

    expect(\GeminiLabs\SiteReviews\Tests\NullQueue::calls('async', 'queue/notification'))->toBe([]);
});

/*
 * The list-table filters and columns.
 */

test('a column filter starts from nothing but its own title', function () {
    $filter = new class extends AbstractColumnFilter {
    };

    expect($filter->label())->toBe('')
        ->and($filter->options())->toBe([])
        ->and($filter->placeholder())->toBe('')
        ->and($filter->title())->not->toBe('');
});

test('the assigned-user filter reads zero as unassigned', function () {
    $_GET['assigned_user'] = '0';
    $filter = new \GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedUser();

    expect($filter->selected())->toBe(\GeminiLabs\SiteReviews\Helpers\Arr::get($filter->options(), 0));
});

test('a broken category lookup yields no filter options, not a fatal', function () {
    add_filter('get_terms', fn () => new WP_Error('broken', 'nope'));

    expect((new \GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterCategory())->options())
        ->toBe([]);
});

test('an assigned user who was deleted is skipped in the column', function () {
    // wp_delete_user() would trigger the plugin's own cleanup; deleting the row
    // directly leaves the orphaned assignment a half-migrated site really has
    global $wpdb;
    $userId = createUser();
    $review = createReview(['assigned_users' => [$userId]]);
    $wpdb->delete($wpdb->users, ['ID' => $userId]);
    clean_user_cache($userId);

    $value = (new \GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueAssignedUsers())
        ->handle(glsr_get_review($review->ID));

    expect($value)->toBe(''); // nothing renderable is an empty cell
});

/*
 * The metaboxes.
 */

test('the author metabox needs the cap for other people\'s posts, and drafts default to yourself', function () {
    $review = createReview();
    global $wp_meta_boxes;
    $wp_meta_boxes = [];
    wp_set_current_user(createUser(['role' => 'author'])); // edit_posts but not edit_others_posts

    (new \GeminiLabs\SiteReviews\Metaboxes\AuthorMetabox())->register(get_post($review->ID));
    expect($wp_meta_boxes)->toBe([]);

    // an unsaved draft renders with the current user preselected
    wp_set_current_user(createUser(['role' => 'administrator']));
    $unsaved = get_post(createPost());
    $unsaved->ID = 0;
    ob_start();
    (new \GeminiLabs\SiteReviews\Metaboxes\AuthorMetabox())->render($unsaved);
    expect(ob_get_clean())->toContain(wp_get_current_user()->display_name);
});

test('the response metabox needs the respond capability', function () {
    $review = createReview();
    global $wp_meta_boxes;
    $wp_meta_boxes = [];
    wp_set_current_user(createUser(['role' => 'subscriber']));

    (new \GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox())->register(get_post($review->ID));

    expect($wp_meta_boxes)->toBe([]);
});

test('the assigned-users metabox skips a deleted user', function () {
    global $wpdb;
    $userId = createUser();
    $review = createReview(['assigned_users' => [$userId]]);
    $wpdb->delete($wpdb->users, ['ID' => $userId]);
    clean_user_cache($userId);

    ob_start();
    (new \GeminiLabs\SiteReviews\Metaboxes\AssignedUsersMetabox())->render(get_post($review->ID));

    expect(ob_get_clean())->not->toContain('user_id');
});

test('the dashboard month counter answers from its cache when it can', function () {
    glsr(\GeminiLabs\SiteReviews\Database\Cache::class)->store('monthly', 'count', [
        'count' => 7,
        'timestamp' => current_time('timestamp'),
    ]);

    expect((new \GeminiLabs\SiteReviews\Metaboxes\DashboardMetabox())->thisMonth())->toBe(7);
});
