<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormWidget extends Widget
{
    /**
     * @param array $instance
     * @return void
     */
    public function form($instance)
    {
        $this->widgetArgs = $this->shortcode()->normalizeAtts($instance)->toArray();
        $terms = glsr(Database::class)->terms();
        $this->renderField('text', [
            'class' => 'widefat',
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
        ]);
        $this->renderField('textarea', [
            'class' => 'widefat',
            'label' => _x('Description', 'admin-text', 'site-reviews'),
            'name' => 'description',
        ]);
        if (!empty($terms)) {
            $this->renderField('select', [
                'class' => 'widefat',
                'label' => _x('Automatically assign a category', 'admin-text', 'site-reviews'),
                'name' => 'category',
                'options' => Arr::prepend($terms, _x('Do not assign a category', 'admin-text', 'site-reviews'), ''),
            ]);
        }
        $this->renderField('text', [
            'class' => 'widefat',
            'default' => '',
            'description' => sprintf(_x('You may also enter %s to assign to the current post.', 'admin-text', 'site-reviews'), '<code>post_id</code>'),
            'label' => _x('Assign reviews to a custom page/post ID', 'admin-text', 'site-reviews'),
            'name' => 'assign_to',
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
     * @inheritDoc
     */
    protected function shortcode()
    {
        return glsr(SiteReviewsFormShortcode::class);
    }
}
