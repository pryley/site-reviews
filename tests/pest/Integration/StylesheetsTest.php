<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Assets\Stylesheets;
use GeminiLabs\SiteReviews\Modules\Style;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The stylesheets a root prints beside itself.
 *
 * There is no page-wide stylesheet. The first root on the page prints the shared components inline
 * and each component it needs as a link; a later root prints only what the page has not seen. A
 * page without a root prints nothing.
 */

function useStyle(string $style): void
{
    glsr(OptionManager::class)->set('settings.general.style', $style);
    glsr()->bind(Style::class, null, true); // Style reads the setting when it is built
}

beforeEach(function () {
    resetPluginState();
    glsr(Stylesheets::class)->reset();
});

test('the first root prints the shared styles inline and its components as links, later roots only what is new', function () {
    createReview();

    $list = do_shortcode('[site_reviews]');
    expect($list)->toContain('<style id="glsr-css-shared">')
        ->toContain('--glsr-star-full:url(') // the star images, substituted
        ->toContain('<link rel="stylesheet" id="glsr-css-review" href="')
        ->toContain('/assets/styles/base/review.css?ver=')
        ->toContain('id="glsr-css-star-rating"')
        ->toContain('id="glsr-css-pagination"')
        ->not->toContain('id="glsr-css-form"');
    expect(strpos($list, '<style'))->toBeLessThan(strpos($list, 'glsr-reviews-wrap')); // before the root
    expect($list)->toContain('data-glsr-styles="button flags pagination review star-rating"')
        ->not->toContain('data-glsr-styles="star-rating"') // the root declares for the ratings inside it
        ->not->toContain('data-glsr-styles=""');
    expect(strpos($list, 'glsr-css-review"'))->toBeLessThan(strpos($list, 'glsr-css-button"')); // printed in the manifest's order

    $summary = do_shortcode('[site_reviews_summary]');
    expect($summary)->toContain('id="glsr-css-summary"')
        ->not->toContain('<style') // the shared styles are on the page
        ->not->toContain('id="glsr-css-star-rating"'); // the list printed it

    $form = do_shortcode('[site_reviews_form]');
    expect($form)->toContain('id="glsr-css-form"')
        ->toContain('id="glsr-css-field-rating"')
        ->not->toContain('id="glsr-css-review"');
    expect(substr_count($form, 'id="glsr-css-button"'))->toBe(0); // the list's load-more printed it
});

test('a component the style changes comes from the style, the others from the base', function () {
    useStyle('default');
    createReview();

    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('/assets/styles/default/form.css?ver=')
        ->toContain('/assets/styles/default/field-input.css?ver=')
        ->toContain('/assets/styles/base/field-rating.css?ver=');
});

test('a component the style leaves out is not printed', function () {
    useStyle('bootstrap');

    $html = do_shortcode('[site_reviews_form]');
    expect($html)->toContain('/assets/styles/bootstrap/form.css?ver=')
        ->not->toContain('glsr-css-field-input') // Bootstrap's inputs are Bootstrap's
        ->not->toContain('glsr-css-field-choice');
});

test('a style with no files of its own takes the base for every component', function () {
    useStyle('my-theme'); // a custom style, from the config filter

    expect(glsr(Stylesheets::class)->file('review'))->toBe('base/review.css')
        ->and(glsr(Stylesheets::class)->file('form'))->toBe('base/form.css');
    expect(do_shortcode('[site_reviews_form]'))->toContain('/assets/styles/base/form.css?ver=');
});

test('a custom style can name its own file for a component', function () {
    add_filter('site-reviews/stylesheet/url', function (string $url, string $component, string $style) {
        return 'form' === $component ? 'https://example.org/my-theme/reviews-form.css' : $url;
    }, 10, 3);

    expect(do_shortcode('[site_reviews_form]'))->toContain('href="https://example.org/my-theme/reviews-form.css"')
        ->toContain('/assets/styles/base/field-label.css?ver=');
});

test('the assets/css filter turns every stylesheet off, the shared ones included', function () {
    add_filter('site-reviews/assets/css', '__return_false');
    createReview();

    $html = do_shortcode('[site_reviews]');
    expect($html)->not->toContain('<style')
        ->not->toContain('<link')
        ->toContain('glsr-reviews-wrap'); // the markup is untouched
});

test('a star rating on its own prints the shared styles and its component once', function () {
    $first = glsr_star_rating(4, 2);
    $second = glsr_star_rating(3, 1);

    expect($first)->toContain('<style id="glsr-css-shared">')
        ->toContain('id="glsr-css-star-rating"')
        ->toContain('glsr-star-rating glsr-stars')
        ->toContain('data-glsr-styles="star-rating"');
    expect($second)->not->toContain('<style')
        ->not->toContain('<link')
        ->toContain('glsr-star-rating glsr-stars');
});

test('the debug stylesheet prints with the debug output, which only a shortcode renders', function () {
    createReview();

    expect(do_shortcode('[site_reviews debug=1]'))->toContain('id="glsr-css-debug"')
        ->not->toContain('data-glsr-styles="button flags pagination review star-rating debug"');
    glsr(Stylesheets::class)->reset();
    expect(glsr()->shortcode('site_reviews')->build(['debug' => true], 'function'))->not->toContain('glsr-css-debug');
});

test('a fragment rendered for admin-ajax or REST names its stylesheets and prints none', function () {
    createReview();
    add_filter('wp_doing_ajax', '__return_true');

    $html = do_shortcode('[site_reviews]');
    expect($html)->not->toContain('<style')
        ->not->toContain('<link')
        ->toContain('data-glsr-styles="button flags pagination review star-rating"');
    expect(glsr_star_rating(4))->not->toContain('<link')
        ->toContain('data-glsr-styles="star-rating"');
    remove_filter('wp_doing_ajax', '__return_true');
    // REST_REQUEST cannot be defined in this suite; wp_is_serving_rest_request() reads only that constant
});

test('a shortcode can add a component to its root through the attributes filter, like a module', function () {
    createReview();
    add_filter('site-reviews/shortcode/attributes/site_reviews', function (array $attributes) {
        $attributes['data-glsr-styles'] .= ' modal';
        return $attributes;
    });

    expect(do_shortcode('[site_reviews]'))->toContain('id="glsr-css-modal"');
    expect(do_shortcode('[site_reviews_form]'))->not->toContain('id="glsr-css-modal"');
});

test('the whole css of a style is every component in order, for a canvas that takes one string', function () {
    $css = glsr(Stylesheets::class)->css('default');

    expect($css)->toStartWith('body{--glsr-white') // the variables first
        ->toContain('--glsr-star-full:url(')
        ->toContain('.glsr-default form.glsr-form'); // Default's own rules
    expect(strpos($css, '.glsr form.glsr-form{'))->toBeLessThan(strpos($css, '.glsr-default form.glsr-form'));
    expect(glsr(Stylesheets::class)->css('minimal'))->not->toContain('.glsr-minimal');
});

test('the urls of a style name every component it has, with the version', function () {
    $urls = glsr(Stylesheets::class)->urls('bootstrap');

    expect(array_keys($urls))->not->toContain('field-input')
        ->toContain('form');
    expect($urls['form'])->toEndWith('/assets/styles/bootstrap/form.css?ver='.glsr()->version);
    expect($urls['review'])->toEndWith('/assets/styles/base/review.css?ver='.glsr()->version);
});
