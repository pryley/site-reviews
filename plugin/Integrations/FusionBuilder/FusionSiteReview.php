<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FusionSiteReview extends FusionElement
{
    public static function elementParameters(): array
    {
        return [
            'post_id' => [
                'heading' => esc_attr_x('Review Post ID', 'admin-text', 'site-reviews'),
                'param_name' => 'post_id',
                'type' => 'textfield',
                'value' => '',
            ],
            'hide' => [
                'default' => '',
                'heading' => esc_html_x('Hide Fields', 'admin-text', 'site-reviews'),
                'param_name' => 'hide',
                'placeholder_text' => esc_attr_x('Select Fields to Hide', 'admin-text', 'site-reviews'),
                'type' => 'multiple_select',
                'value' => static::feShortcode()->getHideOptions(),
            ],
            'class' => [
                'heading' => esc_attr_x('CSS Class', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Add a class to the wrapping HTML element.', 'admin-text', 'site-reviews'),
                'param_name' => 'class',
                'type' => 'textfield',
                'value' => '',
            ],
            'id' => [
                'heading' => esc_attr_x('CSS ID', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Add an ID to the wrapping HTML element.', 'admin-text', 'site-reviews'),
                'param_name' => 'id',
                'type' => 'textfield',
                'value' => '',
            ],
        ];
    }

    protected static function feIcon(): string
    {
        return 'fusion-glsr-review';
    }

    protected static function feShortcode(): ?ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
