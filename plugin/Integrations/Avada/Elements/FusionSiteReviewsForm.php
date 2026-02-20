<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FusionSiteReviewsForm extends FusionElement
{
    public function add_css_files()
    {
        FusionBuilder()->add_element_css(glsr()->path('assets/blocks/site_reviews_form/style-index.css'));
    }

    public function elementIcon(): string
    {
        return 'fusion-glsr-form';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
        ];
    }
}
