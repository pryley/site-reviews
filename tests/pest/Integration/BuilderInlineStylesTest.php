<?php

use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Bricks\Controller as BricksController;
use GeminiLabs\SiteReviews\Integrations\Elementor\Controller as ElementorController;

/*
 * The inline styles of the Bricks and Elementor builders: the built stylesheet, then one mask rule per icon.
 */

function inlineStyle(string $handle): string
{
    return implode('', (array) wp_styles()->get_data($handle, 'after'));
}

afterEach(function () {
    foreach (['bricks-admin', 'bricks-builder', 'elementor-admin', 'elementor-editor', 'elementor-frontend'] as $handle) {
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

test('elementor gets the built stylesheet and a mask rule for each icon, and the frontend its icon size', function () {
    wp_register_style('elementor-admin', false);
    wp_register_style('elementor-editor', false);
    wp_register_style('elementor-frontend', false);

    glsr(ElementorController::class)->registerInlineStyles();

    $built = file_get_contents(glsr()->path('assets/styles/integrations/elementor-inline.css'));
    $form = Svg::encoded('assets/images/icons/elementor/icon-form.svg');
    expect($built)->toContain('i[class^=eicon-glsr-]:before{')
        ->and($form)->toStartWith('data:image/svg+xml')
        ->and(inlineStyle('elementor-admin'))->toStartWith($built)
        ->and(inlineStyle('elementor-admin'))->toContain("i.eicon-glsr-form::before{mask-image:url(\"{$form}\")}")
        ->and(inlineStyle('elementor-admin'))->toContain('i.eicon-glsr-review::before{mask-image:url("data:')
        ->and(inlineStyle('elementor-admin'))->toContain('i.eicon-glsr-reviews::before{mask-image:url("data:')
        ->and(inlineStyle('elementor-admin'))->toContain('i.eicon-glsr-summary::before{mask-image:url("data:')
        ->and(inlineStyle('elementor-editor'))->toBe(inlineStyle('elementor-admin'))
        ->and(inlineStyle('elementor-frontend'))->toBe(inlineStyle('elementor-admin').'[class*="eicon-glsr-"]::before{font-size:28px;margin:0 auto}');
});
