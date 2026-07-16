<?php

use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\FlyoutController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Tests\FakeLicense;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The review editor screen, and the help menu that floats over the plugin's admin pages.
 *
 * A review is not a post, and the editor must be talked out of treating it like one: the media
 * library, visual editor and shortcode buttons do not belong when moderating a stranger's opinion,
 * and the stock "Post published"/"Post updated" messages say the wrong word.
 *
 * Everything turns on WHICH SCREEN we are on — easy to get subtly wrong, and the cost is a filter
 * firing on someone else's editor:
 *
 *   isReviewEditor()    base === 'post' AND id === 'site-review' AND post_type === 'site-review'
 *   the flyout          base !== 'post', and the post type starts with 'site-review'
 *
 * glsr_current_screen() falls back to an object of empty strings when there is no screen, which
 * makes all of this safe to call from a front-end request.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front');
    unset($GLOBALS['post']);
    glsr(Notice::class)->clear();
    // The container is on the Application singleton, so a binding made in a test outlives it —
    // the transaction cannot roll back an object graph. Put the real License back.
    FakeLicense::$isPremium = false;
    glsr()->bind(License::class, License::class, $shared = true);
});

/**
 * Put us on the review editor: editing one review, on the review post type.
 */
function onReviewEditor(): \WP_Post
{
    $review = createReview();
    set_current_screen(glsr()->post_type);
    get_current_screen()->base = 'post';
    $GLOBALS['post'] = get_post($review->ID);

    return $GLOBALS['post'];
}

function onPostEditor(): \WP_Post
{
    $post = get_post(createPost());
    set_current_screen('post');
    get_current_screen()->base = 'post';
    $GLOBALS['post'] = $post;

    return $post;
}

/**
 * A licence that says what the test needs it to say, without a licence server.
 */
function fakeLicense(bool $isPremium): void
{
    FakeLicense::$isPremium = $isPremium;
    glsr()->bind(License::class, FakeLicense::class, $shared = true);
}

/*
 * What the editor is stripped down to.
 */

test('the review editor has no media buttons, no tinymce and no quicktags', function () {
    // Somebody moderating a review does not need a media library, and a visual editor would
    // let them silently rewrite what a customer said in rich text.
    onReviewEditor();

    expect(glsr(EditorController::class)->filterEditorSettings([]))->toBe([
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 12,
        'tinymce' => false,
    ]);
});

test('everybody else\'s editor is left exactly as it was', function () {
    // wp_editor_settings fires for EVERY editor on the site. A plugin that does not check the
    // screen takes TinyMCE away from the page editor.
    onPostEditor();
    $settings = ['media_buttons' => true, 'tinymce' => true];

    expect(glsr(EditorController::class)->filterEditorSettings($settings))->toBe($settings);
});

test('the review textarea gets a toolbar anchor, and nobody else\'s does', function () {
    // The div is what the `editor-expand` script hangs autosizing off. Without it the textarea
    // does not grow; with it in the wrong editor, somebody else's does something odd.
    onReviewEditor();
    expect(glsr(EditorController::class)->filterEditorTextarea('<textarea id="content"></textarea>'))
        ->toBe('<div id="ed_toolbar"></div><textarea id="content"></textarea>');

    onPostEditor();
    expect(glsr(EditorController::class)->filterEditorTextarea('<textarea id="content"></textarea>'))
        ->toBe('<textarea id="content"></textarea>');

    // and it survives being handed nothing at all, which `the_editor` may do
    expect(glsr(EditorController::class)->filterEditorTextarea(null))->toBe('');
});

/*
 * The messages. WordPress says "Post published." — for a review, every one of those words is
 * wrong, and two of the states (approved, unapproved) are the plugin's own.
 */

test('a saved review says review, not post', function () {
    $post = onReviewEditor();
    $messages = glsr(EditorController::class)->filterUpdateMessages(['post' => ['1' => 'Post updated.']]);

    expect($messages)->toHaveKey(glsr()->post_type)
        ->and($messages['post'])->toBe(['1' => 'Post updated.']); // and nobody else's are touched

    $ours = $messages[glsr()->post_type];
    expect($ours[1])->toContain('Review')       // "Review updated." — not "Post updated."
        ->and($ours[6])->toContain('Review')    // published
        ->and($ours[50])->toContain('approved') // the plugin's own states, which WordPress has no word for
        ->and($ours[51])->not->toBeEmpty()
        ->and($ours[52])->not->toBeEmpty();
    expect($ours[9])->toContain(date_i18n('M j, Y', strtotime($post->post_date))); // scheduled for…
});

test('the messages are not built when there is no post to build them for', function () {
    // post_updated_messages fires on screens with no post — and get_post() then returns null.
    unset($GLOBALS['post']);
    set_current_screen('edit-'.glsr()->post_type);
    $messages = ['post' => ['1' => 'Post updated.']];

    expect(glsr(EditorController::class)->filterUpdateMessages($messages))->toBe($messages);
});

/*
 * Custom fields. The plugin's meta is `_`-prefixed, which WordPress treats as protected and
 * hides — but the whole point of the custom fields feature is that a site owner can SEE it.
 */

test('the plugin\'s own meta is shown on a review, and stays hidden everywhere else', function () {
    onReviewEditor();
    $controller = glsr(EditorController::class);

    expect($controller->filterIsProtectedMeta(true, '_custom_favourite_colour', 'post'))->toBeFalse()
        ->and($controller->filterIsProtectedMeta(true, '_glsr_something', 'post'))->toBeFalse();

    // somebody else's protected meta stays protected, on the review editor too
    expect($controller->filterIsProtectedMeta(true, '_edit_lock', 'post'))->toBeTrue();

    // and on an ordinary post, so does ours — those fields belong to the review screen
    onPostEditor();
    expect($controller->filterIsProtectedMeta(true, '_custom_favourite_colour', 'post'))->toBeTrue();

    // and meta that is not post meta is never ours to unprotect
    onReviewEditor();
    expect($controller->filterIsProtectedMeta(true, '_custom_favourite_colour', 'user'))->toBeTrue();
});

/*
 * The notice on a review that came from somewhere else.
 */

test('a third-party review says the response cannot be published', function () {
    // A review imported from Google or Yelp is a copy of something hosted elsewhere. Writing a
    // public reply to it here would put a reply on the site that the customer will never see,
    // and that the third party does not have — so the response box is disabled, and this is
    // the notice that explains why.
    $review = createReview();
    glsr(ReviewManager::class)->updateRating($review->ID, ['type' => 'google']);
    glsr(\GeminiLabs\SiteReviews\Database\Cache::class)->delete($review->ID, 'reviews');
    set_current_screen(glsr()->post_type);
    get_current_screen()->base = 'post';
    $post = get_post($review->ID);

    ob_start();
    glsr(EditorController::class)->renderReviewNotice($post);
    $html = (string) ob_get_clean();

    expect($html)->toContain('disabled');
});

test('an ordinary local review gets no notice at all', function () {
    $post = onReviewEditor(); // type is '' — written on this site

    ob_start();
    glsr(EditorController::class)->renderReviewNotice($post);

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The flyout.
 */

test('the flyout appears on the plugin\'s screens and nowhere else', function () {
    set_current_screen('edit-'.glsr()->post_type);
    ob_start();
    glsr(FlyoutController::class)->renderFlyout();
    expect((string) ob_get_clean())->not->toBe('');

    // not on the review EDITOR, where the screen is already full
    onReviewEditor();
    ob_start();
    glsr(FlyoutController::class)->renderFlyout();
    expect((string) ob_get_clean())->toBe('');

    // and not on anybody else's list table
    set_current_screen('edit-post');
    ob_start();
    glsr(FlyoutController::class)->renderFlyout();
    expect((string) ob_get_clean())->toBe('');
});

test('the flyout can be switched off', function () {
    set_current_screen('edit-'.glsr()->post_type);
    add_filter('site-reviews/flyoutmenu/enabled', '__return_false');

    ob_start();
    glsr(FlyoutController::class)->renderFlyout();

    expect((string) ob_get_clean())->toBe('');
});

test('a premium licence is not asked to upgrade to premium', function () {
    // The first item is "Upgrade to Premium". Showing it to somebody who has already paid is
    // the kind of thing that gets a plugin uninstalled.
    //
    // The licence is faked through the container rather than by setting up a real one, because
    // License::isPremium() reads License::status(), which asks the licence server — and the
    // subject here is the MENU, not the licensing.
    set_current_screen('edit-'.glsr()->post_type);
    $render = function () {
        ob_start();
        glsr(FlyoutController::class)->renderFlyout();

        return (string) ob_get_clean();
    };

    fakeLicense($isPremium = false);
    expect($render())->toContain('Upgrade to Premium');

    fakeLicense($isPremium = true);
    expect($render())->not->toContain('Upgrade to Premium')
        ->and($render())->toContain('Ask for Help'); // and the rest of the menu is still there
});
