<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class VcSiteReview extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/wpbakery-review.svg');
    }

    public static function vcShortcodeSettings(): array
    {
        return [
            'post_id' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Review ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter the Post ID of the review you want to display.', 'admin-text', 'site-reviews'),
                'param_name' => 'post_id',
                'save_always' => true,
            ],
            'hide' => [
                'type' => 'checkbox',
                'heading' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'value' => array_flip(static::vcShortcode()->getHideOptions()),
                'param_name' => 'hide',
            ],
            'id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'param_name' => 'id',
                'group' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            ],
            'class' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'param_name' => 'class',
                'group' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            ],
        ];
    }
}
