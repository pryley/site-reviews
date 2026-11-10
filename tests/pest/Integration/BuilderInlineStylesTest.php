<?php

use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Bricks\Controller as BricksController;

/*
 * The inline styles of the Bricks builder: the built stylesheet, then one mask rule per icon.
 */

function inlineStyle(string $handle): string
{
    return implode('', (array) wp_styles()->get_data($handle, 'after'));
}

afterEach(function () {
    foreach (['bricks-admin', 'bricks-builder'] as $handle) {
        wp_deregister_style($handle);
    }
    remove_all_filters('site-reviews/bricks/icons');
    set_current_screen('front');
});

test('bricks gets the built stylesheet and a mask rule for each icon, the filtered ones included', function () {
    wp_register_style('bricks-admin', false);
    wp_register_style('bricks-builder', false);
    add_filter('site-reviews/bricks/icons', fn (array $icons) => $icons + ['site_probe' => 'data:image/svg+xml,probe']);
    set_current_screen('bricks_page_bricks-elements');

    glsr(BricksController::class)->printInlineStyles();

    $built = file_get_contents(glsr()->path('assets/styles/integrations/bricks-inline.css'));
    $form = Svg::encoded('assets/images/icons/bricks/icon-form.svg');
    expect($built)->toContain('i[class*=ti-site_review]:before{')
        ->and($form)->toStartWith('data:image/svg+xml')
        ->and(inlineStyle('bricks-admin'))->toStartWith($built)
        ->and(inlineStyle('bricks-admin'))->toContain("i.ti-site_reviews_form::before{mask-image:url(\"{$form}\")}")
        ->and(inlineStyle('bricks-admin'))->toContain('i.ti-site_review::before{mask-image:url("data:')
        ->and(inlineStyle('bricks-admin'))->toContain('i.ti-site_reviews::before{mask-image:url("data:')
        ->and(inlineStyle('bricks-admin'))->toContain('i.ti-site_reviews_summary::before{mask-image:url("data:')
        ->and(inlineStyle('bricks-admin'))->toContain('i.ti-site_probe::before{mask-image:url("data:image/svg+xml,probe")}')
        ->and(inlineStyle('bricks-builder'))->toBe(inlineStyle('bricks-admin'));
});

test('bricks prints nothing outside its elements screen and its builder', function () {
    wp_register_style('bricks-admin', false);
    set_current_screen('dashboard');

    glsr(BricksController::class)->printInlineStyles();

    expect(inlineStyle('bricks-admin'))->toBe('');
});
