<?php

use GeminiLabs\SiteReviews\Modules\Rating;
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
 * Each is a thin wrapper around a shortcode: it renders the form in the admin and hands whatever was
 * typed straight to the shortcode. So little of its own to go wrong, and one thing that is easy to —
 * normalizeInstance(). WordPress hands a widget only the values that were SAVED, and a widget saved
 * before a new option existed does not have it in its instance. shortcode_atts() keeps the shape of
 * what was saved rather than filling from the shortcode's defaults, so the widget does not quietly
 * acquire settings its owner never chose.
 *
 * Marked legacy in the admin (the block is the replacement), they still have to work — a widget that
 * stops rendering takes a live sidebar with it.
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

/*
 * The summary widget's own form.
 *
 * The other three widgets wrap a shortcode whose options are mostly about which reviews to show.
 * The summary's are about which reviews to COUNT — the average, the star bars, the "based on 12
 * reviews" — and getting that wrong is quieter than getting a list wrong: the number is still a
 * number, it is just the wrong one, and nobody looking at a sidebar can tell.
 */

test('the summary widget form offers the things a summary is counted from', function () {
    $html = renderedWidgetForm(new SiteReviewsSummaryWidget());

    expect($html)->toContain('Widget Title')                        // prepended to every widget
        ->toContain('Limit Reviews by Assigned Pages')
        ->toContain('Limit Reviews by Assigned Categories')
        ->toContain('Limit Reviews by Assigned Users')
        ->toContain('Limit Reviews by Accepted Terms')
        ->toContain('Minimum Rating')
        ->toContain('Custom ID')                                    // appended to every widget
        ->toContain('Additional CSS classes');
});

test('the minimum rating cannot be set to zero, whatever the rating scale says', function () {
    // Rating::min() is 0 — that is the floor of the SCALE, not a rating anybody gives. The widget
    // floors the field at 1 (`max(1, Rating::min())`), because a summary "limited" to ratings of
    // at least zero is a summary with no limit at all, dressed up as one.
    expect(Rating::min())->toBe(0); // …which is exactly why the max(1, …) is there

    $html = renderedWidgetForm(new SiteReviewsSummaryWidget());

    expect($html)->toContain('min="1"')
        ->toContain('max="'.Rating::max().'"')
        ->and($html)->not->toContain('min="0"');
});

test('a site that has changed its rating scale gets a form that agrees with it', function () {
    // MAX_RATING is filterable, and some sites run a ten-point scale. A widget still offering a
    // maximum of 5 would make the top half of that site's reviews unreachable from this field.
    add_filter('site-reviews/const/MAX_RATING', fn () => 10);

    expect(renderedWidgetForm(new SiteReviewsSummaryWidget()))->toContain('max="10"');
});

test('the summary widget hides the things a summary is made of, not the things a review is', function () {
    // The `hide` checkboxes come from the SUMMARY shortcode, and they are its own: the bars and
    // the star rating, not the author and the date. A widget that offered the review shortcode's
    // list here would be offering to hide fields that are not on the screen.
    $html = renderedWidgetForm(new SiteReviewsSummaryWidget());

    expect($html)->toContain('Hide the percentage bars')
        ->toContain('Hide if no reviews are found')
        ->and($html)->not->toContain('Hide the author'); // that is the reviews widget's
});

test('the review type dropdown is not drawn on a site that only has one kind of review', function () {
    // Widget::form() skips a `type` field with no options. On a site with no review-importing
    // addon there is only "local", and a dropdown with one entry is not a choice.
    expect(renderedWidgetForm(new SiteReviewsSummaryWidget()))->not->toContain('[type]');
});

test('the summary widget counts only the reviews it was told to', function () {
    // The whole point of the assigned_* fields, end to end: two reviews on the site, one of them
    // on this page, and the average must be the one review's — not the average of both.
    $postId = createPost();
    createReview(['assigned_posts' => $postId, 'rating' => 2]);
    createReview(['rating' => 5]); // assigned to nothing

    $html = renderedWidget(new SiteReviewsSummaryWidget(), ['assigned_posts' => (string) $postId]);

    expect($html)->toContain('2.0 out of 5 stars (based on 1 review)');
});

test('a summary widget form shows what was saved in it', function () {
    $html = renderedWidgetForm(new SiteReviewsSummaryWidget(), [
        'assigned_posts' => 'post_id',
        'rating' => 4,
        'title' => 'What people say',
    ]);

    expect($html)->toContain('value="What people say"')
        ->toContain('value="post_id"')
        ->toContain('value="4"');
});

/*
 * The form widget's form, and the single review widget's.
 */

test('the form widget ASSIGNS reviews, where the others LIMIT them', function () {
    // The same three keys — assigned_posts, assigned_terms, assigned_users — mean the opposite
    // thing here, and the labels are the only thing that says so. On a reviews or summary widget
    // they FILTER what is shown; on the form they decide what a new review is attached to. A
    // person who read "Limit Reviews by Assigned Pages" above a submission form would reasonably
    // conclude the field did nothing.
    $html = renderedWidgetForm(new SiteReviewsFormWidget());

    expect($html)->toContain('Assign Reviews to Pages')
        ->toContain('Assign Reviews to Categories')
        ->toContain('Assign Reviews to Users')
        ->and($html)->not->toContain('Limit Reviews by');
});

test('the form widget offers to hide the fields a form has', function () {
    // From the FORM shortcode's own hide options — the fields of the form, not of a review. The
    // `hide` field is a checkbox GROUP, so it has to post back as an array or only the last box
    // ticked would survive the save.
    $widget = new SiteReviewsFormWidget();
    $widget->number = 3;

    $html = renderedWidgetForm($widget);

    expect($html)->toContain('Widget Title')
        ->toContain('Custom ID')
        ->and($html)->toContain('name="widget-'.$widget->id_base.'[3][hide][]"')
        ->and($html)->toContain('value="rating"')   // the form's own fields…
        ->and($html)->toContain('value="terms"');   // …not a review's
});

test('the single review widget asks for the one thing it cannot work without', function () {
    // It shows ONE review, by id, and there is no default that could stand in for a missing one.
    $html = renderedWidgetForm(new SiteReviewWidget());

    expect($html)->toContain('Review Post ID')
        ->toContain('Enter the Post ID of the review you want to display.');
});

test('the single review widget form shows the review id it was saved with', function () {
    $review = createReview();

    expect(renderedWidgetForm(new SiteReviewWidget(), ['post_id' => $review->ID]))
        ->toContain('value="'.$review->ID.'"');
});

test('every widget offers a title, an id and a class, whatever else it offers', function () {
    // Widget::form() prepends `title` and appends `id` and `class` to whatever widgetConfig()
    // returned. A widget that lost them would lose the only heading its sidebar has.
    foreach ([SiteReviewsWidget::class, SiteReviewWidget::class, SiteReviewsFormWidget::class, SiteReviewsSummaryWidget::class] as $class) {
        $html = renderedWidgetForm(new $class());

        expect($html)->toContain('Widget Title')
            ->and($html)->toContain('Custom ID')
            ->and($html)->toContain('Additional CSS classes')
            ->and($html)->toContain('legacy widget'); // and each says it is the old way
    }
});
