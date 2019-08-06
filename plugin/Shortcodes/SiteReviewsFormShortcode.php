<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsFormShortcode extends Shortcode
{
    protected function hideOptions()
    {
        return [
            'rating' => __('Hide the rating field', 'site-reviews'),
            'title' => __('Hide the title field', 'site-reviews'),
            'content' => __('Hide the review field', 'site-reviews'),
            'name' => __('Hide the name field', 'site-reviews'),
            'email' => __('Hide the email field', 'site-reviews'),
            'terms' => __('Hide the terms field', 'site-reviews'),
        ];
    }

    /**
     * @param array|string $atts
     * @param string $type
     * @return array
     */
    public function normalizeAtts($atts, $type = 'shortcode')
    {
        $atts = parent::normalizeAtts($atts, $type);
        if (empty($atts['id'])) {
            $atts['id'] = substr(md5(serialize($atts)), 0, 8);
        }
        return $atts;
    }
}
