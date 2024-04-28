<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FusionSingleReview extends FusionElement
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
                'value' => glsr(SiteReviewShortcode::class)->getHideOptions(),
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

    public static function registerElement(): void
    {
        $parameters = static::elementParameters();
        $parameters = glsr()->filterArray('fusion-builder/controls/site_review', $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'name' => esc_attr_x('Single Review', 'admin-text', 'site-reviews'),
            'shortcode' => 'site_review',
            'icon' => 'fusiona-af-rating',
            'params' => $parameters,
        ]));
    }
}
