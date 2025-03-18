<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class BricksSiteReview extends BricksElement
{
    public function render()
    {
        if (empty($this->settings['post_id'])) {
            $this->render_element_placeholder([
                'title' => esc_html_x('Please select a Review.', 'admin-text', 'site-reviews'),
            ]);
            return;
        }
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
        return SiteReviewShortcode::class;
    }
}
