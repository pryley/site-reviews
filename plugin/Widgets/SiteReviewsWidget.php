<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsWidget extends Widget
{
    /**
     * @param array $instance
     * @return void
     */
    public function form($instance)
    {
        $this->widgetArgs = $this->shortcode()->normalizeAtts($instance);
        $terms = glsr(Database::class)->getTerms();
        $this->renderField('text', [
            'class' => 'widefat',
            'label' => __('Title', 'site-reviews'),
            'name' => 'title',
        ]);
        $this->renderField('number', [
            'class' => 'small-text',
            'default' => 10,
            'label' => __('How many reviews would you like to display?', 'site-reviews'),
            'max' => 100,
            'name' => 'display',
        ]);
        $this->renderField('select', [
            'label' => __('What is the minimum rating to display?', 'site-reviews'),
            'name' => 'rating',
            'options' => [
                '0' => sprintf(_n('%s star', '%s stars', 0, 'site-reviews'), 0),
                '1' => sprintf(_n('%s star', '%s stars', 1, 'site-reviews'), 1),
                '2' => sprintf(_n('%s star', '%s stars', 2, 'site-reviews'), 2),
                '3' => sprintf(_n('%s star', '%s stars', 3, 'site-reviews'), 3),
                '4' => sprintf(_n('%s star', '%s stars', 4, 'site-reviews'), 4),
                '5' => sprintf(_n('%s star', '%s stars', 5, 'site-reviews'), 5),
            ],
        ]);
        if (count(glsr()->reviewTypes) > 1) {
            $this->renderField('select', [
                'class' => 'widefat',
                'label' => __('Which type of review would you like to display?', 'site-reviews'),
                'name' => 'type',
                'options' => ['' => __('All Reviews', 'site-reviews')] + glsr()->reviewTypes,
            ]);
        }
        if (!empty($terms)) {
            $this->renderField('select', [
                'class' => 'widefat',
                'label' => __('Limit reviews to this category', 'site-reviews'),
                'name' => 'category',
                'options' => ['' => __('All Categories', 'site-reviews')] + $terms,
            ]);
        }
        $this->renderField('text', [
            'class' => 'widefat',
            'default' => '',
            'description' => sprintf(__("Separate multiple ID's with a comma. You may also enter %s to automatically represent the current page/post ID.", 'site-reviews'), '<code>post_id</code>'),
            'label' => __('Limit reviews to those assigned to this page/post ID', 'site-reviews'),
            'name' => 'assigned_to',
        ]);
        $this->renderField('text', [
            'class' => 'widefat',
            'label' => __('Enter any custom CSS classes here', 'site-reviews'),
            'name' => 'class',
        ]);
        $this->renderField('checkbox', [
            'name' => 'hide',
            'options' => $this->shortcode()->getHideOptions(),
        ]);
    }

    /**
     * @param array $newInstance
     * @param array $oldInstance
     * @return array
     */
    public function update($newInstance, $oldInstance)
    {
        if (!is_numeric($newInstance['display'])) {
            $newInstance['display'] = 10;
        }
        $newInstance['display'] = min(50, max(0, intval($newInstance['display'])));
        return parent::update($newInstance, $oldInstance);
    }

    /**
     * @inheritDoc
     */
    protected function shortcode()
    {
        return glsr(SiteReviewsShortcode::class);
    }
}
