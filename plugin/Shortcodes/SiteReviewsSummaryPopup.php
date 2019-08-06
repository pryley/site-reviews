<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsSummaryPopup extends SiteReviewsPopup
{
    /**
     * @return array
     */
    public function fields()
    {
        return [[
            'html' => sprintf('<p class="strong">%s</p>', esc_html__('All settings are optional.', 'site-reviews')),
            'minWidth' => 320,
            'type' => 'container',
        ], [
            'label' => esc_html__('Title', 'site-reviews'),
            'name' => 'title',
            'tooltip' => __('Enter a custom shortcode heading.', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getTypes(__('Which type of review would you like to use?', 'site-reviews')),
        $this->getCategories(__('Limit reviews to this category.', 'site-reviews')),
        [
            'label' => esc_html__('Assigned To', 'site-reviews'),
            'name' => 'assigned_to',
            'tooltip' => __('Limit reviews to those assigned to this post ID (separate multiple IDs with a comma). You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html__('Schema', 'site-reviews'),
            'name' => 'schema',
            'options' => [
                'true' => esc_html__('Enable rich snippets', 'site-reviews'),
                'false' => esc_html__('Disable rich snippets', 'site-reviews'),
            ],
            'tooltip' => __('Rich snippets are disabled by default.', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => esc_html__('Classes', 'site-reviews'),
            'name' => 'class',
            'tooltip' => __('Add custom CSS classes to the shortcode.', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => esc_html__('Hide', 'site-reviews'),
            'layout' => 'grid',
            'spacing' => 5,
            'type' => 'container',
        ], ];
    }
}
