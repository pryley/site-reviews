<?php

use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Modules\Translation;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Teaching WordPress to say "Approved" instead of "Published".
 *
 * A review is a post, so WordPress calls it published and pending — which is the wrong vocabulary
 * for a review, where the question is whether the site owner has APPROVED it. This controller
 * rewrites those words, in the five places WordPress puts them:
 *
 *   the post-state badge next to a title in the list table   (display_post_states)
 *   the Publish metabox                                      (gettext_default)
 *   the status counts above the list table                   ($wp_post_statuses, on current_screen)
 *   the javascript that redraws the metabox                  (the `post` script's l10n blob)
 *   the bulk-action messages                                 (bulk_post_updated_messages)
 *
 * Every one of them is a GLOBAL WordPress string, and the whole of this file is really one
 * assertion: it must happen on the review screens and NOWHERE ELSE. `gettext_default` fires for
 * every string on every admin page there is — so a controller that rewrote "Published" without
 * checking the screen would rename the button on every post, page and product on the site.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    set_current_screen('front');
});

function onTheReviewsScreen(): void
{
    set_current_screen('edit-'.glsr()->post_type);
}

function onSomebodyElsesScreen(): void
{
    set_current_screen('edit-post');
}

/*
 * The words themselves.
 */

test('a review is not "published", it is approved', function () {
    onTheReviewsScreen();
    $controller = glsr(TranslationController::class);

    expect($controller->filterPostStatusLabels('Published', 'Published'))->toBe('Approved')
        ->and($controller->filterPostStatusLabels('Pending', 'Pending'))->toBe('Unapproved')
        ->and($controller->filterPostStatusLabels('Pending Review', 'Pending Review'))->toBe('Unapproved')
        ->and($controller->filterPostStatusLabels('Save as Pending', 'Save as Pending'))->toBe('Save as Unapproved');
});

test('and every OTHER string on the review screen is left exactly as it was', function () {
    // gettext_default fires for every string WordPress renders. Only the four are replaced; the
    // rest are handed straight back, and a controller that got this wrong would be rewriting the
    // whole admin.
    onTheReviewsScreen();

    expect(glsr(TranslationController::class)->filterPostStatusLabels('Move to Trash', 'Move to Trash'))
        ->toBe('Move to Trash');
});

test('a post that is not a review keeps WordPress\'s own words', function () {
    // THE assertion. This filter runs on every admin page of every site. Renaming the Publish
    // button on somebody's pages, products and posts because they installed a reviews plugin would
    // be an act of vandalism.
    onSomebodyElsesScreen();

    expect(glsr(TranslationController::class)->filterPostStatusLabels('Published', 'Published'))
        ->toBe('Published');
});

test('a null translation does not become the string "null"', function () {
    // The filter is typed `?string` on both arguments, because WordPress passes null for a string
    // it has no translation for. Casting that to a string without thinking gives ''.
    onSomebodyElsesScreen();

    expect(glsr(TranslationController::class)->filterPostStatusLabels(null, null))->toBe('');
});

/*
 * The badge next to the title.
 */

test('a review awaiting approval is badged "Unapproved" in the list', function () {
    onTheReviewsScreen();

    expect(glsr(TranslationController::class)->filterPostStates(['pending' => 'Pending']))
        ->toBe(['pending' => 'Unapproved']);
});

test('and any other state is left alone, on any other screen', function () {
    onSomebodyElsesScreen();
    expect(glsr(TranslationController::class)->filterPostStates(['pending' => 'Pending']))
        ->toBe(['pending' => 'Pending']);

    onTheReviewsScreen();
    expect(glsr(TranslationController::class)->filterPostStates(['sticky' => 'Sticky']))
        ->toBe(['sticky' => 'Sticky']);
});

/*
 * The status counts above the list table — which are a GLOBAL, and are put back afterwards.
 */

test('the status counts say Approved and Unapproved', function () {
    // $wp_post_statuses is WordPress's own registry, shared by every post type on the site. This
    // rewrites it in place, which is why it is only done on the review screens — and why this test
    // puts it back. Nothing in the suite's teardown resets that global.
    global $wp_post_statuses;
    $original = [
        'pending' => clone $wp_post_statuses['pending'],
        'publish' => clone $wp_post_statuses['publish'],
    ];

    try {
        onTheReviewsScreen();
        glsr(TranslationController::class)->translatePostStatusLabels();

        expect($wp_post_statuses['publish']->label)->toBe('Approved')
            ->and($wp_post_statuses['pending']->label)->toBe('Unapproved')
            ->and($wp_post_statuses['publish']->label_count['singular'])->toContain('Approved');
    } finally {
        $wp_post_statuses['pending'] = $original['pending'];
        $wp_post_statuses['publish'] = $original['publish'];
    }
});

test('the status counts are NOT rewritten on somebody else\'s screen', function () {
    global $wp_post_statuses;
    $before = $wp_post_statuses['publish']->label;

    onSomebodyElsesScreen();
    glsr(TranslationController::class)->translatePostStatusLabels();

    expect($wp_post_statuses['publish']->label)->toBe($before);
});

/*
 * The javascript.
 */

test('the metabox javascript is taught the same words', function () {
    // The Publish metabox is redrawn by WordPress's `post` script from an l10n blob, so relabelling
    // the PHP is only half the job: the moment somebody changes the status dropdown, the javascript
    // writes "Published" straight back over it.
    onTheReviewsScreen();
    wp_scripts()->add('post', '/post.js');
    wp_scripts()->add_data('post', 'data', 'var postL10n = '.wp_json_encode([
        'publish' => 'Publish',
        'published' => 'Published',
        'savePending' => 'Save as Pending',
    ]).';');

    glsr(TranslationController::class)->translatePostStatusLabelsInScripts();

    $script = wp_scripts()->registered['post']->extra['data'];
    expect($script)->toContain('Approve')
        ->toContain('Approved')
        ->toContain('Save as Unapproved');
});

test('and somebody else\'s post screen keeps its own javascript', function () {
    onSomebodyElsesScreen();
    wp_scripts()->add('post', '/post.js');
    wp_scripts()->add_data('post', 'data', 'var postL10n = {"published":"Published"};');

    glsr(TranslationController::class)->translatePostStatusLabelsInScripts();

    expect(wp_scripts()->registered['post']->extra['data'])->toContain('Published')
        ->not->toContain('Approved');
});

/*
 * The bulk-action messages.
 */

test('bulk messages count reviews, not posts, and say so in the plural', function () {
    // _nx() with the real count: "1 review updated" and "3 reviews updated" are different strings,
    // and a message that said "3 review updated" is the kind of thing people screenshot.
    $messages = glsr(TranslationController::class)->filterBulkUpdateMessages([], [
        'deleted' => 1,
        'locked' => 1,
        'trashed' => 1,
        'untrashed' => 1,
        'updated' => 3,
    ]);

    expect($messages)->toHaveKey(glsr()->post_type);
    expect($messages[glsr()->post_type]['updated'])->toContain('reviews updated')
        ->and($messages[glsr()->post_type]['trashed'])->toContain('review moved to the Trash')
        ->and($messages[glsr()->post_type]['locked'])->toContain('somebody is editing');
});

/*
 * The gettext filters, which are what the custom-strings feature hangs off.
 */

test('a string the site owner has not customised comes back untouched', function () {
    $controller = glsr(TranslationController::class);

    expect($controller->filterGettext('Submit your review', 'Submit your review'))
        ->toBe('Submit your review');
    expect($controller->filterNgettext('1 review', '%s review', '%s reviews', 2))
        ->toBe('1 review');
});

test('an ADMIN string is never handed to the custom-strings translator', function () {
    // Every admin-facing string is registered with an `admin-text` context precisely so that it can
    // be skipped here. The Translations settings screen is for the words a VISITOR sees — and a
    // site owner who renamed "Rating" on their review form must not find their own admin menu
    // renamed with it.
    $controller = glsr(TranslationController::class);
    $context = Translation::CONTEXT_ADMIN_KEY;

    expect($controller->filterGettextWithContext('Settings', 'Settings', $context))->toBe('Settings');
    expect($controller->filterNgettextWithContext('1 review', '%s review', '%s reviews', 2, $context))
        ->toBe('1 review');
});

test('a visitor-facing string with a context IS handed to the translator', function () {
    $controller = glsr(TranslationController::class);

    expect($controller->filterGettextWithContext('Anonymous', 'Anonymous', 'the reviewer'))
        ->toBe('Anonymous');
    expect($controller->filterNgettextWithContext('1 star', '%s star', '%s stars', 3, 'rating'))
        ->toBe('1 star');
});
