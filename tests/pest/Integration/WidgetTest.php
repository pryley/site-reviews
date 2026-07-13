<?php

use GeminiLabs\SiteReviews\Widgets\SiteReviewsFormWidget;
use GeminiLabs\SiteReviews\Widgets\SiteReviewsSummaryWidget;
use GeminiLabs\SiteReviews\Widgets\SiteReviewsWidget;
use GeminiLabs\SiteReviews\Widgets\SiteReviewWidget;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The four legacy widgets.
 *
 * Each one is a thin wrapper around a shortcode: the widget renders the form in the
 * admin, and hands whatever was typed into it straight to the shortcode to build. So
 * there is very little of its own to go wrong, and exactly one thing that is easy to get
 * wrong — normalizeInstance().
 *
 * WordPress hands a widget the values that were SAVED, and nothing else. A widget saved
 * before a new option existed does not have that option in its instance. shortcode_atts()
 * is used to keep the shape of what was saved rather than filling it in from the
 * shortcode's defaults — which is why the widget does not quietly acquire settings its
 * owner never chose.
 *
 * They are marked legacy in the admin (the block is the replacement) and they still have
 * to keep working, because a widget that stops rendering takes a live sidebar with it.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

/**
 * What the widget printed on the front end, sidebar wrapper and all.
 */
function renderedWidget(WP_Widget $widget, array $instance = [], array $args = []): string
{
    ob_start();
    $widget->widget(wp_parse_args($args, [
        'before_widget' => '<aside class="widget">',
        'after_widget' => '</aside>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ]), $instance);

    return (string) ob_get_clean();
}

/**
 * What the widget printed in the admin.
 */
function renderedWidgetForm(WP_Widget $widget, array $instance = []): string
{
    ob_start();
    $widget->form($instance);

    return (string) ob_get_clean();
}

/*
 * Each widget is its shortcode.
 */

test('a widget takes its name and its id from the shortcode it wraps', function (string $class, string $tag) {
    $widget = new $class();

    expect($widget->shortcode->tag)->toBe($tag);
    expect($widget->id_base)->toBe(glsr()->prefix.str_replace('_', '-', $tag));
    expect($widget->name)->toBe($widget->shortcode->name);
})->with([
    [SiteReviewsWidget::class, 'site_reviews'],
    [SiteReviewWidget::class, 'site_review'],
    [SiteReviewsFormWidget::class, 'site_reviews_form'],
    [SiteReviewsSummaryWidget::class, 'site_reviews_summary'],
]);

test('the reviews widget renders the reviews', function () {
    createReview(['content' => 'The pizza was excellent.']);

    $html = renderedWidget(new SiteReviewsWidget(), ['display' => 2]);

    expect($html)->toContain('The pizza was excellent.')
        ->toContain('<aside class="widget">')     // wrapped by the sidebar
        ->toContain('data-from="widget"');        // and it knows where it came from
});

test('a widget with a title says it, and one without does not print an empty heading', function () {
    // The title is NOT a shortcode argument — the shortcodes had one, it was removed, and
    // Shortcode::normalize() runs its arguments through restrict(), which keeps only the
    // keys the Defaults class declares. So the widget reads it from the instance it was
    // saved with, which is the only place it lives (Widget::widgetTitle).
    //
    // An empty <h2></h2> is a gap in the sidebar nobody asked for; a title that was typed
    // in and then never shown is worse.
    createReview();

    expect(renderedWidget(new SiteReviewsWidget(), ['title' => 'What people say']))
        ->toContain('<h2>What people say</h2>');

    expect(renderedWidget(new SiteReviewsWidget(), []))->not->toContain('<h2>');
});

test('a widget title cannot put anything into the sidebar but words', function () {
    // Nothing else sanitizes it: `title` is in no shortcode Defaults, so it is in no
    // $sanitize map either, and a widget saved before this existed still holds whatever
    // was typed into it. So it is sanitized on the way OUT, where it can protect a site
    // that was configured years ago.
    createReview();

    $html = renderedWidget(new SiteReviewsWidget(), [
        'title' => '<script>alert(1)</script>What people say',
    ]);

    expect($html)->toContain('What people say')
        ->not->toContain('<script');
});

test('a theme can filter a widget title, as it can any other', function () {
    // widget_title is WordPress's own hook, and a theme that rewrites every widget heading
    // on the site expects this one to come through it as well.
    createReview();
    add_filter('widget_title', fn ($title) => strtoupper($title));

    expect(renderedWidget(new SiteReviewsWidget(), ['title' => 'What people say']))
        ->toContain('<h2>WHAT PEOPLE SAY</h2>');
});

test('the single review widget renders the review it was given', function () {
    $review = createReview(['content' => 'The single review.']);

    expect(renderedWidget(new SiteReviewWidget(), ['post_id' => $review->ID]))
        ->toContain('The single review.');
});

test('the summary widget renders the summary', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 3]);

    expect(renderedWidget(new SiteReviewsSummaryWidget(), []))
        ->toContain('4.0 out of 5 stars (based on 2 reviews)');
});

test('the form widget renders the form', function () {
    expect(renderedWidget(new SiteReviewsFormWidget(), []))
        ->toContain('<form')
        ->toContain('name="site-reviews[rating]"');
});

test('a widget passes what it was configured with to the shortcode', function () {
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'content' => 'Assigned to the page.']);
    createReview(['content' => 'Assigned to nothing.']);

    $html = renderedWidget(new SiteReviewsWidget(), ['assigned_posts' => (string) $postId]);

    expect($html)->toContain('Assigned to the page.')
        ->not->toContain('Assigned to nothing.');
});

/*
 * The admin form.
 */

test('the widget form is drawn from the shortcode\'s own settings', function () {
    $html = renderedWidgetForm(new SiteReviewsWidget());

    expect($html)->toContain('Widget Title')       // prepended to every widget
        ->toContain('Reviews Per Page')
        ->toContain('Minimum Rating')
        ->toContain('Custom ID')                   // appended to every widget
        ->toContain('Additional CSS classes');
});

test('the widget form says out loud that it is the old way of doing this', function () {
    // Somebody opening it should be told there is a block, rather than discovering later
    // that the widget has fewer options than the thing that replaced it.
    expect(renderedWidgetForm(new SiteReviewsWidget()))
        ->toContain('legacy widget')
        ->toContain('notice-warning');
});

test('a widget form field is named the way wordpress will post it back', function () {
    // get_field_name() is what makes the instance arrive at update() — a field the widget
    // names itself is a field whose value is silently dropped on save.
    $widget = new SiteReviewsWidget();
    $widget->id_base = 'glsr-site-reviews';
    $widget->number = 3;

    $html = renderedWidgetForm($widget);

    expect($html)->toContain('name="widget-glsr-site-reviews[3][display]"')
        ->toContain('id="widget-glsr-site-reviews-3-display"');
});

test('a widget form shows what was saved in it', function () {
    $html = renderedWidgetForm(new SiteReviewsWidget(), [
        'title' => 'What people say',
        'display' => 7,
    ]);

    expect($html)->toContain('value="What people say"')
        ->toContain('value="7"');
});

test('a dropdown a site has nothing to put in is not drawn', function () {
    // The review TYPE dropdown, on a site with only local reviews. One entry is not a
    // choice, and Widget::form() skips a `type` field with no options.
    expect(renderedWidgetForm(new SiteReviewsWidget()))->not->toContain('[type]');
});

/*
 * Saving.
 */

test('saving a widget keeps what was typed into it, and nothing else', function () {
    // shortcode_atts() against the keys that were POSTED, not against the shortcode's
    // defaults. A widget saved today must not quietly acquire every option the shortcode
    // has ever had, or its owner ends up with settings they never chose.
    $saved = (new SiteReviewsWidget())->update([
        'title' => 'What people say',
        'display' => '5',
    ], []);

    expect(array_keys($saved))->toBe(['title', 'display']);
    expect($saved['display'])->toEqual(5); // and the ones the Defaults name are sanitized
});

test('a saved widget value that the shortcode does not know about is kept as it is', function () {
    // `title` is not one of the shortcode's arguments — no Defaults class declares it — so
    // it is in no $sanitize map either, and unguardedMerge() carries it through untouched.
    // Which is exactly why Widget::widgetTitle() sanitizes it on the way OUT: what is
    // already saved on a live site cannot be un-saved.
    $saved = (new SiteReviewsWidget())->update(['title' => '<b>Bold</b>'], []);

    expect($saved['title'])->toBe('<b>Bold</b>');
});
