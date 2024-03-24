<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewWidget extends Widget
{
    /**
     * @param array $instance
     *
     * @return string
     */
    public function form($instance)
    {
        $this->widgetArgs = $this->shortcode()->normalize($instance)->args;
        $this->renderField('text', [
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
        ]);
        $this->renderField('text', [
            'label' => _x('Enter any custom CSS classes here', 'admin-text', 'site-reviews'),
            'name' => 'class',
        ]);
        $this->renderField('checkbox', [
            'name' => 'hide',
            'options' => $this->shortcode()->getHideOptions(),
        ]);
        return ''; // WP_Widget::form should return a string
    }

    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }

    protected function widgetDescription(): string
    {
        return _x('Site Reviews: Display a single review.', 'admin-text', 'site-reviews');
    }

    protected function widgetName(): string
    {
        return _x('Single Review', 'admin-text', 'site-reviews');
    }
}
