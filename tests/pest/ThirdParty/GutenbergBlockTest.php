<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsFormBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsSummaryBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Hooks as GutenbergHooks;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Gutenberg blocks.
 *
 * Each is a thin wrapper over the shortcode that does the work. The block puts that output into the
 * page with the wrapper WordPress expects, and looks different in the editor from how a visitor sees
 * it. That split is the whole of Block::render(), turning on `?context=edit`, how WordPress asks a
 * block to render for the editor's server-side preview:
 *
 *   in the EDITOR    unwrapped, so the editor can add its own wrapper — and if every field is off, a
 *                    warning instead of an empty block, which in the editor looks like a bug.
 *   on the SITE      wrapped in a div carrying WordPress's block supports (alignment, colours,
 *                    spacing) plus whatever the block adds.
 *
 * None of it could be reached until the suite shadowed filter_input(): `?context=edit` is read from
 * the SAPI request table, which a CLI process lacks.
 *
 * EVERYTHING HERE GOES THROUGH render_block(). Calling the callback directly does not work:
 * Block::wrapperAttributes() asks WP_Block_Supports for the block's classes, and WP_Block_Supports
 * only knows which block it is rendering because render_block() told it (`self::$block_to_render`) —
 * call the callback yourself and it reads null. That cannot happen in production, where WordPress is
 * always the caller, so the test has to be WordPress.
 */

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    $_GET = [];
});

function inTheEditor(): void
{
    $_GET['context'] = 'edit';
}

/**
 * A block on a page, rendered the way WordPress renders it.
 */
function renderBlock(string $name, array $attributes = []): string
{
    return render_block([
        'attrs' => $attributes,
        'blockName' => $name,
        'innerBlocks' => [],
        'innerContent' => [],
        'innerHTML' => '',
    ]);
}

function reviewsBlock(array $attributes = []): string
{
    return renderBlock('site-reviews/reviews', $attributes);
}

/**
 * Every field the reviews shortcode is willing to hide — asked of the shortcode rather than
 * listed here, because the list is its own and it changes.
 */
function everyHideableField(): string
{
    $options = protectedMethod(SiteReviewsShortcode::class, 'options')
        ->invoke(glsr(SiteReviewsShortcode::class), 'hide');

    return implode(',', array_keys($options));
}

/*
 * What a visitor gets.
 */

test('a block renders the shortcode it wraps, inside a div', function () {
    createReview(['content' => 'A very good hotel.']);

    $html = reviewsBlock(['count' => 5]);

    expect($html)->toStartWith('<div')
        ->and($html)->toContain('A very good hotel.')
        ->and($html)->toContain('data-shortcode="site_reviews"');
});

test('the summary block renders a summary', function () {
    createReview(['rating' => 5]);

    expect(renderBlock('site-reviews/summary'))->toContain('data-shortcode="site_reviews_summary"');
});

test('the form block renders a form to somebody who may write a review', function () {
    // Both of these say, out loud, who is logged in and whether the site requires it. Neither
    // is inherited. The first version of this test asserted `<form` and passed on its own
    // (because the test before it had left somebody logged in) and failed the moment the whole
    // suite ran — which is the definition of a test that is not testing what it says.
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    wp_set_current_user(createUser());

    expect(renderBlock('site-reviews/form'))->toContain('<form');
});

test('a visitor who must log in first is told so, rather than shown a form they cannot use', function () {
    // A guest on a site that requires a login gets an explanation and a link — not a form that
    // would reject them after they had written their review.
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    wp_set_current_user(0);

    $html = renderBlock('site-reviews/form');

    expect($html)->not->toContain('<form')
        ->and($html)->toContain('logged in')
        ->and($html)->toContain('wp-login.php');
});

test('a site that does not require a login shows a guest the form', function () {
    glsr(OptionManager::class)->set('settings.general.require.login', 'no');
    wp_set_current_user(0);

    expect(renderBlock('site-reviews/form'))->toContain('<form');
});

test('a rating colour on the form block marks its wrapper', function () {
    glsr(OptionManager::class)->set('settings.general.require.login', 'no');
    wp_set_current_user(0);

    $html = renderBlock('site-reviews/form', ['style_rating_color' => 'vivid-red']);

    expect($html)->toContain('has-rating-color')
        ->and($html)->toContain('--glsr-form-star-bg:var(--wp--preset--color--vivid-red, currentColor)');
});

/*
 * What the person building the page gets.
 */

test('in the editor the block is not wrapped, because the editor wraps it', function () {
    createReview();
    inTheEditor();

    $html = reviewsBlock(['count' => 5]);

    // The shortcode's own root div, and nothing around it.
    expect($html)->toStartWith('<div class="glsr ');
});

test('a block with every field switched off says so, rather than rendering nothing', function () {
    // An empty block in the editor looks like a bug, and the person who caused it is one
    // checkbox away from fixing it — if they are told.
    inTheEditor();

    $html = reviewsBlock(['hide' => everyHideableField()]);

    expect($html)->toContain('block-editor-warning')
        ->and($html)->toContain('hidden all of the fields');
});

test('the warning is only for the editor — a visitor is shown reviews, not admin text', function () {
    // "You have hidden all of the fields for this block" is an admin string. It must never
    // reach the front end of somebody's site.
    createReview();

    expect(reviewsBlock(['hide' => everyHideableField()]))
        ->not->toContain('hidden all of the fields')
        ->not->toContain('block-editor-warning');
});

/*
 * The wrapper.
 */

test('the block\'s own classes and styles reach the wrapper', function () {
    createReview();

    $html = reviewsBlock([
        'count' => 5,
        'style_align' => 'center',
        'style_rating_color_custom' => '#ff0000',
    ]);

    expect($html)->toContain('items-justified-center')
        ->and($html)->toContain('has-rating-color')
        ->and($html)->toContain('--glsr-review-star-bg:#ff0000');
});

test('a theme colour is used by name, not copied by value', function () {
    // A preset colour must come out as the CSS custom property, so that it follows the theme —
    // including when the visitor switches to dark mode.
    createReview();

    expect(reviewsBlock(['count' => 5, 'style_rating_color' => 'vivid-red']))
        ->toContain('var(--wp--preset--color--vivid-red, currentColor)');
});

test('a class name from the post content cannot break out of the attribute', function () {
    // `className` is a block attribute: it is written into the post, and anybody who can edit a
    // post can write it. It ends up inside a class="..." attribute in the page.
    //
    // What matters is not that the words disappear — a class called `onmouseover` is a perfectly
    // legal, useless class — but that the QUOTES are escaped, so it cannot close the attribute
    // and start a new one.
    createReview();

    $html = reviewsBlock([
        'className' => 'ok-class" onmouseover="alert(1)',
        'count' => 5,
    ]);

    expect($html)->not->toContain('" onmouseover="alert(1)"')  // the breakout
        ->and($html)->not->toContain('<script');
});

/*
 * The mapping. Getting this wrong renders the wrong thing entirely.
 */

test('the blocks are registered with wordpress, and their render callback is ours', function () {
    // Registered by the plugin on `init`, from the block.json beside each one — which is why
    // the tests above do not register anything, and why registering them again in a beforeEach
    // earns a "Block type is already registered" notice.
    //
    // The render_callback is the whole point: without it WordPress renders the saved HTML from
    // the post, which for a dynamic block is nothing at all.
    $registry = WP_Block_Type_Registry::get_instance();

    foreach (['site-reviews/reviews', 'site-reviews/summary', 'site-reviews/form'] as $name) {
        $block = $registry->get_registered($name);
        expect($block)->not->toBeNull()
            ->and($block->render_callback)->toBeCallable();
    }
});

test('each block wraps its own shortcode', function () {
    expect(SiteReviewsBlock::shortcodeClass())->toBe(SiteReviewsShortcode::class)
        ->and(SiteReviewsSummaryBlock::shortcodeClass())->toBe(SiteReviewsSummaryShortcode::class)
        ->and(SiteReviewsFormBlock::shortcodeClass())->toBe(SiteReviewsFormShortcode::class);

    expect(glsr(SiteReviewsSummaryBlock::class)->shortcodeInstance())
        ->toBeInstanceOf(SiteReviewsSummaryShortcode::class);
});

/*
 * The single-review block, which is the only one with a rule of its own.
 *
 * It shows ONE review, chosen by id. In the editor that is a problem the other blocks do not
 * have: a person who drops it onto a page before they have any reviews gets a block with nothing
 * in it and no explanation, which reads as a broken plugin rather than an empty site. So it
 * checks the review count first and says so.
 *
 * The count is asked of wp_count_posts(), not of the plugin, and it is the PUBLISHED count — a
 * site whose only reviews are pending approval has nothing this block can show, and is told so.
 */

function reviewBlock(array $attributes = []): string
{
    return renderBlock('site-reviews/review', $attributes);
}

test('in the editor, with no published reviews, the block says so instead of rendering nothing', function () {
    inTheEditor();

    $html = reviewBlock();

    expect($html)->toContain('block-editor-warning')
        ->toContain('No reviews found.');
});

test('a review that is only pending does not count — it is the published ones that can be shown', function () {
    // The qualifier that makes the message true. wp_count_posts()->publish, not ->pending: a
    // pending review cannot be displayed, so a site that has only those still has nothing to show.
    inTheEditor();
    createReview(['is_approved' => false]);

    expect(reviewBlock())->toContain('No reviews found.');
});

test('in the editor, with a review to show, it renders the review rather than the warning', function () {
    inTheEditor();
    $review = createReview(['content' => 'The single review.']);

    $html = reviewBlock(['post_id' => $review->ID]);

    expect($html)->not->toContain('block-editor-warning')
        ->and($html)->toContain('The single review.');
});

test('on the site, the count is never asked for at all', function () {
    // The check is deliberately inside the `context=edit` branch. A visitor is not shown a
    // "no reviews found" warning, and — more to the point — every page with this block on it does
    // not run a COUNT query it has no use for.
    $review = createReview(['content' => 'The single review.']);

    $html = reviewBlock(['post_id' => $review->ID]);

    expect($html)->toStartWith('<div')
        ->and($html)->toContain('The single review.')
        ->and($html)->not->toContain('block-editor-warning');
});

test('on the site, a block with no reviews shows the visitor nothing, not the warning', function () {
    // The distinguishing fixture for the branch above: ZERO published reviews, front end. If the
    // count check ever moved out of the context=edit branch, THIS is the case that would start
    // showing visitors the editor's "No reviews found" warning — the previous test cannot see
    // that, because its published review keeps the hoisted check quiet too.
    $html = reviewBlock();

    expect($html)->not->toContain('No reviews found.')
        ->and($html)->not->toContain('block-editor-warning');
});

test('a rating colour chosen from the theme palette becomes a css variable, not a hardcoded colour', function () {
    // The star colour is set on the wrapper as a custom property, which the stylesheet reads. A
    // PRESET colour has to stay a var(--wp--preset--color--…) reference so that it keeps tracking
    // the theme — resolving it to a hex here would freeze it, and a person who changed their
    // palette would find the stars unchanged.
    $review = createReview();

    $html = reviewBlock(['post_id' => $review->ID, 'style_rating_color' => 'vivid-red']);

    expect($html)->toContain('has-rating-color')
        ->toContain('--glsr-review-star-bg:var(--wp--preset--color--vivid-red, currentColor)');
});

test('a preset rating colour falls back to the colour it was picked as', function () {
    // The preset variable is defined by the theme, so it stops existing the moment somebody
    // switches to a theme whose palette does not use that slug. The declaration would then be
    // invalid at computed-value time, `background` would reset to its initial value, and the
    // masked stars would be transparent — the block would look empty rather than mis-coloured.
    //
    // ColorControl stores the resolved value in the _custom attribute alongside the slug, so
    // the colour the person actually chose is available as the fallback.
    $review = createReview();

    $html = reviewBlock([
        'post_id' => $review->ID,
        'style_rating_color' => 'vivid-red',
        'style_rating_color_custom' => '#cf2e2e',
    ]);

    expect($html)->toContain('--glsr-review-star-bg:var(--wp--preset--color--vivid-red, #cf2e2e)');
});

test('a custom rating colour is used as given', function () {
    $review = createReview();

    $html = reviewBlock(['post_id' => $review->ID, 'style_rating_color_custom' => '#ff9900']);

    expect($html)->toContain('has-rating-color')
        ->toContain('--glsr-review-star-bg:#ff9900');
});

test('no rating colour means no class and no custom property', function () {
    // array_filter() on the styles, and an empty $classes. A block that emitted an empty
    // `--glsr-review-star-bg:` would override the stylesheet's default with nothing.
    $review = createReview();

    $html = reviewBlock(['post_id' => $review->ID]);

    expect($html)->not->toContain('has-rating-color')
        ->and($html)->not->toContain('--glsr-review-star-bg');
});

test('the single-review block is registered, and wraps the single-review shortcode', function () {
    expect(SiteReviewBlock::shortcodeClass())->toBe(SiteReviewShortcode::class);

    $block = WP_Block_Type_Registry::get_instance()->get_registered('site-reviews/review');

    expect($block)->not->toBeNull()
        ->and($block->render_callback)->toBeCallable();
});

/*
 * The wiring.
 *
 * Integrations/Gutenberg/Hooks is nine lines that attach the controller to seven WordPress hooks,
 * and it runs ONCE — on plugins_loaded, before any test does anything. That is why it is worth a
 * test of its own: a hook that stops being attached does not fail, it simply stops happening, and
 * the block editor quietly loses a feature (the category, the assets, the classname) with nothing
 * anywhere to say so.
 */

test('the gutenberg integration attaches the controller to every hook the editor needs', function () {
    $hooks = [
        'allowed_block_types_all',              // the blocks a person is allowed to insert
        'block_categories_all',                 // the "Site Reviews" category in the inserter
        'block_default_classname',              // the wp-block-… class on each block
        'enqueue_block_editor_assets',          // the editor javascript, without which no block works
        'init',                                 // registerBlocks
        'use_block_editor_for_post_type',       // whether a review is edited in Gutenberg at all
        'widget_types_to_hide_from_legacy_widget_block',
    ];
    foreach ($hooks as $hook) {
        remove_all_filters($hook); // start from nothing, so a core callback cannot stand in for ours
    }

    glsr(GutenbergHooks::class)->run();

    foreach ($hooks as $hook) {
        expect(has_filter($hook))->not->toBeFalse("nothing is hooked to {$hook}");
    }
});

test('and the category the blocks are filed under really reaches the inserter', function () {
    // One of the seven, end to end — because "a callback is attached" is not the same claim as
    // "the callback does the thing". Without this category the four blocks are unfindable.
    remove_all_filters('block_categories_all');

    glsr(GutenbergHooks::class)->run();

    $categories = apply_filters('block_categories_all', [], null);

    expect(array_column($categories, 'slug'))->toContain(glsr()->id);
});
