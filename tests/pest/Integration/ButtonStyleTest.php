<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\Partials\Pagination;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

/*
 * WordPress's button block stylesheet, for the buttons that fall back to block styling.
 * Only the form block asks for it in block.json, so a rendered button has to load it.
 */

function useButtonTestStyle(string $style): void
{
    glsr(OptionManager::class)->set('settings.general.style', $style);
    glsr()->bind(Style::class, null, true);
}

function isButtonStyleQueued(): bool
{
    return in_array('wp-block-button', wp_styles()->queue, true);
}

function addThemeButtonClasses(): void
{
    add_filter('render_block_core/buttons', function ($content) {
        $button = str_contains($content, 'test-block-class') ? 'theme-custom' : 'theme-fill';
        $content = str_replace('wp-block-button__link', "wp-block-button__link {$button}", $content);
        return str_replace('<div class="wp-block-button', '<div class="theme-wrapper wp-block-button', $content);
    });
}

beforeEach(function () {
    wp_register_style('wp-block-button', false); // because the suite's isolation resets wp_styles()
    wp_dequeue_style('wp-block-button');
});

afterEach(function () {
    remove_all_filters('render_block_core/buttons');
    remove_all_filters('site-reviews/assets/css');
    remove_all_filters('site-reviews/config/styles/custom');
    remove_all_filters('site-reviews/defaults/style-classes');
    remove_all_filters('site-reviews/interpolate/form/submit-button');
    useButtonTestStyle('default');
    wp_dequeue_style('wp-block-button');
});

it('loads the stylesheet when the form shortcode renders', function () {
    useButtonTestStyle('default');
    glsr(SiteReviewsFormShortcode::class)->build();
    expect(isButtonStyleQueued())->toBeTrue();
});

it('loads the stylesheet when the load more button renders', function () {
    useButtonTestStyle('default');
    (new Pagination())->build(['current' => 1, 'total' => 2, 'type' => 'loadmore']);
    expect(isButtonStyleQueued())->toBeTrue();
});

it('leaves the stylesheet out when the load more button does not render', function () {
    useButtonTestStyle('default');
    (new Pagination())->build(['current' => 2, 'total' => 2, 'type' => 'loadmore']);
    expect(isButtonStyleQueued())->toBeFalse();
});

it('leaves the stylesheet out for a style with its own button classes', function () {
    useButtonTestStyle('divi');
    glsr(SiteReviewsFormShortcode::class)->build();
    expect(isButtonStyleQueued())->toBeFalse();
});

it('loads the stylesheet for the WPForms button', function () {
    useButtonTestStyle('wpforms');
    glsr(SiteReviewsFormShortcode::class)->build();
    expect(isButtonStyleQueued())->toBeTrue();
});

it('takes the stylesheet off a button whose block classes a filter strips', function () {
    add_filter('site-reviews/defaults/style-classes', function (array $values) {
        $values['button'] = 'glsr-button';
        return $values;
    });
    useButtonTestStyle('default');
    glsr(SiteReviewsFormShortcode::class)->build();
    expect(isButtonStyleQueued())->toBeFalse();
});

it('loads the stylesheet for a custom style with the block classes', function () {
    add_filter('site-reviews/config/styles/custom', fn () => ['classes' => ['button' => 'wp-block-button__link wp-element-button']]);
    expect((new Style('custom'))->usesBlockButton())->toBeTrue();
});

it('leaves the stylesheet out when the plugin css is turned off', function () {
    useButtonTestStyle('default');
    add_filter('site-reviews/assets/css', '__return_false');
    glsr(SiteReviewsFormShortcode::class)->build();
    expect(isButtonStyleQueued())->toBeFalse();
});

it('gets the classes a theme adds to a button block', function () {
    useButtonTestStyle('default');
    addThemeButtonClasses();
    expect(glsr(Style::class)->classes('button', 'test-block-class'))->toBe('glsr-button test-block-class theme-custom wp-block-button__link wp-element-button')
        ->and(glsr(Style::class)->classes('button_wrapper', 'test-block-class'))->toBe('theme-wrapper');
});

it('puts a block style class on the wrapper and any other on the button', function () {
    useButtonTestStyle('default');
    expect(glsr(Style::class)->classes('button', 'is-style-fill my-class'))->toBe('glsr-button my-class wp-block-button__link wp-element-button')
        ->and(glsr(Style::class)->classes('button_wrapper', 'is-style-fill my-class'))->toBe('is-style-fill');
});

it('adds the sanitized additional classes to a slot that is not a button', function () {
    useButtonTestStyle('default');
    $renders = 0;
    add_filter('render_block_core/buttons', function ($content) use (&$renders) {
        ++$renders;
        return $content;
    });
    expect(glsr(Style::class)->classes('form', 'my<b>-class'))->toBe(trim(glsr(Style::class)->classes['form'].' myb-class'))
        ->and($renders)->toBe(0);
});

it('renders one button block per class name', function () {
    useButtonTestStyle('default');
    $renders = 0;
    add_filter('render_block_core/buttons', function ($content) use (&$renders) {
        ++$renders;
        return $content;
    });
    glsr(Style::class)->classes('button', 'test-block-class');
    glsr(Style::class)->classes('button_wrapper', 'test-block-class');
    glsr(Style::class)->classes('button');
    expect($renders)->toBe(2);
});

it('leaves its own classes out of the button block', function () {
    useButtonTestStyle('default');
    $renders = 0;
    add_filter('render_block_core/buttons', function ($content) use (&$renders) {
        ++$renders;
        return $content;
    });
    expect(glsr(Style::class)->classes('button', 'glsr-my-class'))->toContain('glsr-my-class');
    glsr(Style::class)->classes('button');
    expect($renders)->toBe(1);
});

it('renders no button block for a style with its own button classes', function () {
    useButtonTestStyle('divi');
    addThemeButtonClasses();
    expect(glsr(Style::class)->classes('button', 'test-block-class'))->toBe('et_pb_button glsr-button test-block-class')
        ->and(glsr(Style::class)->classes('button_wrapper', 'test-block-class'))->toBe('');
});

it('leaves the stylesheet out until a button\'s classes are read', function () {
    useButtonTestStyle('default');
    glsr(Style::class)->name;
    expect(isButtonStyleQueued())->toBeFalse();
});

it('keeps the config\'s value in the property', function () {
    useButtonTestStyle('default');
    addThemeButtonClasses();
    expect(glsr(Style::class)->classes['button'])->toBe('glsr-button wp-block-button__link wp-element-button');
});

it('reads a button\'s classes again once the button block is registered', function () {
    useButtonTestStyle('default');
    addThemeButtonClasses();
    $registry = \WP_Block_Type_Registry::get_instance();
    $block = $registry->unregister('core/button');
    $unregistered = glsr(Style::class)->classes('button');
    $registry->register($block);
    expect($unregistered)->toBe('glsr-button wp-block-button__link wp-element-button')
        ->and(glsr(Style::class)->classes('button'))->toContain('theme-fill');
});

it('passes the form to the filters of the submit button template', function () {
    useButtonTestStyle('default');
    add_filter('site-reviews/interpolate/form/submit-button', function (array $context, string $template, array $data) {
        $context['class'] = $data['form']->formName();
        return $context;
    }, 10, 3);
    expect(glsr(SiteReviewsFormShortcode::class)->build())->toContain('<button type="submit" class="review-form"');
});

it('adds the theme classes to the load more button', function () {
    useButtonTestStyle('default');
    addThemeButtonClasses();
    $button = (new Pagination())->build(['current' => 1, 'total' => 2, 'type' => 'loadmore']);
    expect($button)->toContain('glsr-button-loadmore')
        ->and($button)->toContain('theme-fill');
});
