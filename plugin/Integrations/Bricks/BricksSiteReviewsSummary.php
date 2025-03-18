<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class BricksSiteReviewsSummary extends BricksElement
{
    public function render()
    {
        if (!$this->shortcodeInstance()->hasVisibleFields($this->settings)) {
            $this->render_element_placeholder([
                'title' => esc_html_x('You have hidden all of the fields.', 'admin-text', 'site-reviews'),
            ]);
            return;
        }
        parent::render();
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
