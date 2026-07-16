<?php

use GeminiLabs\SiteReviews\Controllers\TinymceController;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The shortcode button in the classic editor.
 *
 * The "Site Reviews" button above the toolbar: click it, pick a shortcode, fill in a dialog, and it
 * inserts the shortcode. It exists so nobody has to remember the attribute is `assigned_posts` not
 * `assigned_post`, and it is how most people discover the shortcodes have options at all.
 *
 * Everything hangs off ONE register — glsr()->retrieve('mce') — which RegisterTinymcePopups fills on
 * admin_init with a dialog per shortcode. Empty, all three parts of this controller quietly do
 * nothing: no button, no shortcodes handed to the JS, the dialog comes back `false`. So the register
 * is the precondition for every test, set the way production sets it rather than by hand.
 *
 * The classic editor is not dead: plenty of sites run it, and the block editor's shortcode block
 * puts a classic editor inside itself.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->setUpAjax();
    // `mce` is a container register filled on admin_init. It needs no cleanup here — the whole
    // storage is snapshotted after boot and restored after every test (see snapshotStorage()).
});

afterEach(function () {
    $this->tearDownAjax();
    set_current_screen('front');
});

/*
 * Registering the dialogs.
 */

test('every shortcode gets a dialog', function () {
    glsr(TinymceController::class)->registerTinymcePopups();

    $mce = glsr()->retrieveAs('array', 'mce', []);

    expect($mce)->toHaveKeys([
        'site_review',
        'site_reviews',
        'site_reviews_form',
        'site_reviews_summary',
    ]);
    expect($mce['site_reviews'])->toHaveKeys(['btn_close', 'btn_okay', 'fields', 'label', 'title']);
});

/*
 * The button.
 */

test('the button is drawn above the editor', function () {
    // media_buttons fires for every editor on the screen. This one belongs above the post content.
    set_current_screen('post');
    glsr(TinymceController::class)->registerTinymcePopups();

    ob_start();
    glsr(TinymceController::class)->renderTinymceButton('content');
    $rendered = (string) ob_get_clean();

    expect($rendered)->not->toBeEmpty()
        ->and($rendered)->toContain('site_reviews');
});

test('and not above every OTHER editor on the page', function () {
    // A page can have a dozen editors on it — an excerpt, a WooCommerce product description, an
    // ACF wysiwyg field, a theme option. media_buttons fires for all of them, and a button above
    // each is a plugin shouting.
    set_current_screen('post');
    glsr(TinymceController::class)->registerTinymcePopups();

    ob_start();
    glsr(TinymceController::class)->renderTinymceButton('excerpt');

    expect((string) ob_get_clean())->toBe('');
});

test('an addon can put the button above an editor of its own', function () {
    // `tinymce/editor-ids`. An addon that renders its own editor wants the same button over it.
    set_current_screen('post');
    glsr(TinymceController::class)->registerTinymcePopups();
    add_filter('site-reviews/tinymce/editor-ids', fn ($ids) => [...$ids, 'my_addon_editor']);

    ob_start();
    glsr(TinymceController::class)->renderTinymceButton('my_addon_editor');

    expect((string) ob_get_clean())->not->toBeEmpty();
});

test('the button is not drawn outside the post editor at all', function () {
    // media_buttons also fires on the comments screen, on widget screens, and wherever else a theme
    // or plugin renders wp_editor().
    set_current_screen('edit-comments');
    glsr(TinymceController::class)->registerTinymcePopups();

    ob_start();
    glsr(TinymceController::class)->renderTinymceButton('content');

    expect((string) ob_get_clean())->toBe('');
});

test('and it is not drawn on a site that has no dialogs registered', function () {
    // The register is empty until admin_init has run. Drawing a button that opens nothing is worse
    // than drawing no button.
    set_current_screen('post');

    ob_start();
    glsr(TinymceController::class)->renderTinymceButton('content');

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The dialog itself, over ajax.
 */

test('opening a dialog returns the fields it should show', function () {
    glsr(TinymceController::class)->registerTinymcePopups();

    $response = $this->jsonSentBy(fn () => glsr(TinymceController::class)->mceShortcodeAjax(
        new Request(['shortcode' => 'site_reviews'])
    ));

    expect($response['success'])->toBeTrue()
        ->and($response['data']['shortcode'])->toBe('site_reviews')
        ->and($response['data'])->toHaveKeys(['body', 'close', 'hideOptions', 'ok', 'title']);
});

test('a shortcode nobody registered opens nothing', function () {
    // The shortcode name comes from the BROWSER. Anything the register does not know about is
    // `false`, and the javascript shows no dialog — rather than an empty one.
    glsr(TinymceController::class)->registerTinymcePopups();

    $response = $this->jsonSentBy(fn () => glsr(TinymceController::class)->mceShortcodeAjax(
        new Request(['shortcode' => 'not_a_shortcode'])
    ));

    expect($response['success'])->toBeTrue()  // the REQUEST succeeded…
        ->and($response['data'])->toBeFalse(); // …and there is nothing to show
});

/*
 * What the admin javascript is told.
 */

test('the javascript is given the shortcode plugin, and no required fields — because there are none', function () {
    // `required` is what the dialog refuses to insert without. NONE of the four core shortcodes
    // declares any: TinymceGenerator::$required is [] and not one of SiteReviewTinymce,
    // SiteReviewsTinymce, SiteReviewsFormTinymce or SiteReviewsSummaryTinymce overrides it. So on
    // a stock install this list is empty, every time, and the loop that builds it never runs a
    // second iteration.
    //
    // That is worth saying out loud rather than passing over: the feature exists for the ADDONS,
    // and a test that asserted a core shortcode appeared here would be asserting a fiction.
    glsr(TinymceController::class)->registerTinymcePopups();

    $variables = glsr(TinymceController::class)->filterAdminVariables([]);

    expect($variables['tinymce']['glsr_shortcode'])->toContain('mce-plugin.js');
    expect($variables['shortcodes'])->toBe([]);
});

test('and an addon whose shortcode DOES require a field has it validated', function () {
    // The other half, and the only half that has ever run: an addon registers a dialog with a
    // required field, and the javascript refuses to insert the shortcode without it — because a
    // shortcode missing its required attribute renders nothing at all on the front end, silently.
    //
    // user_can_richedit() is FALSE in a CLI process — WordPress computes it from browser globals
    // ($is_gecko, $is_safari…) that only exist during a real request, so it defaults to false and
    // this method returns early. Saying so is the difference between this test asserting the
    // feature and asserting the absence of a browser.
    add_filter('user_can_richedit', '__return_true');
    glsr()->append('mce', [
        'fields' => [],
        'required' => ['id' => 'The review ID is required.'],
    ], 'an_addon_shortcode');

    $variables = glsr(TinymceController::class)->filterAdminVariables([]);

    expect($variables['shortcodes'])->toHaveKey('an_addon_shortcode')
        ->and($variables['shortcodes']['an_addon_shortcode'])->toHaveKey('id');
});

test('and a person who cannot use the rich editor is given no shortcodes at all', function () {
    // Somebody with the visual editor switched off in their profile has no TinyMCE to put a button
    // in, so there is nothing for the dialogs to insert into.
    //
    // The addon shortcode is registered FIRST, and rich editing is switched on and then off — so
    // this asserts the guard rather than an empty register, which is what it would otherwise be
    // doing (and was).
    glsr()->append('mce', [
        'fields' => [],
        'required' => ['id' => 'Required.'],
    ], 'an_addon_shortcode');

    add_filter('user_can_richedit', '__return_true');
    expect(glsr(TinymceController::class)->filterAdminVariables([])['shortcodes'])
        ->toHaveKey('an_addon_shortcode');

    remove_filter('user_can_richedit', '__return_true');
    add_filter('user_can_richedit', '__return_false');

    $variables = glsr(TinymceController::class)->filterAdminVariables([]);

    expect($variables['shortcodes'])->toBe([])
        ->and($variables['tinymce'])->not->toBeEmpty(); // …though the plugin script is still named
});
