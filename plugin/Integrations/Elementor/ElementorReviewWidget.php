<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class ElementorReviewWidget extends ElementorWidget
{
    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-glsr-review';
    }

    /**
     * @return string
     */
    public function get_shortcode()
    {
        return SiteReviewShortcode::class;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return _x('Single Review', 'admin-text', 'site-reviews');
    }

    protected function print_content()
    {
        if (Review::isReview($this->get_settings_for_display('post_id'))) {
            parent::print_content();
        }
    }

    protected function settings_basic()
    {
        $options = [
            'post_id' => [
                'default' => '',
                'label' => _x('Review Post ID', 'admin-text', 'site-reviews'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
        ];
        $hideOptions = $this->get_shortcode_instance()->getHideOptions();
        foreach ($hideOptions as $key => $label) {
            $separator = $key === key(array_slice($hideOptions, 0, 1)) ? 'before' : 'default';
            $options['hide-'.$key] = [
                'label' => $label,
                'separator' => $separator,
                'return_value' => '1',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ];
        }
        return $options;
    }
}
