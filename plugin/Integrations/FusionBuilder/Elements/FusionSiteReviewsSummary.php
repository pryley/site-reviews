<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FusionSiteReviewsSummary extends FusionElement
{
    public function add_css_files()
    {
        FusionBuilder()->add_element_css(glsr()->path('assets/blocks/site_reviews_summary/style-index.css'));
    }

    public function elementIcon(): string
    {
        return 'fusion-glsr-summary';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
