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
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
        ]);
        $this->renderField('number', [
            'class' => 'small-text',
            'default' => 10,
            'label' => _x('How many reviews would you like to display?', 'admin-text', 'site-reviews'),
            'max' => 100,
            'name' => 'display',
        ]);
        $this->renderField('select', [
            'label' => _x('What is the minimum rating to display?', 'admin-text', 'site-reviews'),
            'name' => 'rating',
            'options' => [
                '0' => esc_attr(sprintf(_nx('%s star', '%s stars', 0, 'admin-text', 'site-reviews'), 0)),
                '1' => esc_attr(sprintf(_nx('%s star', '%s stars', 1, 'admin-text', 'site-reviews'), 1)),
                '2' => esc_attr(sprintf(_nx('%s star', '%s stars', 2, 'admin-text', 'site-reviews'), 2)),
                '3' => esc_attr(sprintf(_nx('%s star', '%s stars', 3, 'admin-text', 'site-reviews'), 3)),
                '4' => esc_attr(sprintf(_nx('%s star', '%s stars', 4, 'admin-text', 'site-reviews'), 4)),
                '5' => esc_attr(sprintf(_nx('%s star', '%s stars', 5, 'admin-text', 'site-reviews'), 5)),
            ],
        ]);
        if (count(glsr()->reviewTypes) > 1) {
            $this->renderField('select', [
                'class' => 'widefat',
                'label' => _x('Which type of review would you like to display?', 'admin-text', 'site-reviews'),
                'name' => 'type',
                'options' => ['' => esc_attr_x('All Reviews', 'admin-text', 'site-reviews')] + glsr()->reviewTypes,
            ]);
        }
        if (!empty($terms)) {
            $this->renderField('select', [
                'class' => 'widefat',
                'label' => _x('Limit reviews to this category', 'admin-text', 'site-reviews'),
                'name' => 'category',
                'options' => ['' => esc_attr_x('All Categories', 'admin-text', 'site-reviews')] + $terms,
            ]);
        }
        $this->renderField('text', [
            'class' => 'widefat',
            'default' => '',
            'description' => sprintf(esc_html_x("Separate multiple ID's with a comma. You may also enter %s to automatically represent the current page/post ID.", 'admin-text', 'site-reviews'), '<code>post_id</code>'),
            'label' => _x('Limit reviews to those assigned to this page/post ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_to',
        ]);
        $this->renderField('text', [
            'class' => 'widefat',
            'label' => _x('Enter any custom CSS classes here', 'admin-text', 'site-reviews'),
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
