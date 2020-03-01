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
            'html' => sprintf('<p class="strong">%s</p>', esc_html_x('All settings are optional.', 'admin-text', 'site-reviews')),
            'minWidth' => 320,
            'type' => 'container',
        ], [
            'label' => esc_html_x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
            'tooltip' => esc_attr_x('Enter a custom shortcode heading.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getTypes(_x('Which type of review would you like to use?', 'admin-text', 'site-reviews')),
        $this->getCategories(_x('Limit reviews to this category.', 'admin-text', 'site-reviews')),
        [
            'label' => esc_html_x('Assigned To', 'admin-text', 'site-reviews'),
            'name' => 'assigned_to',
            'tooltip' => esc_attr_x('Limit reviews to those assigned to this post ID (separate multiple IDs with a comma). You can also enter "post_id" to use the ID of the current page, or "parent_id" to use the ID of the parent page.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'name' => 'schema',
            'options' => [
                'true' => esc_attr_x('Enable rich snippets', 'admin-text', 'site-reviews'),
                'false' => esc_attr_x('Disable rich snippets', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => esc_attr_x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => esc_html_x('Classes', 'admin-text', 'site-reviews'),
            'name' => 'class',
            'tooltip' => esc_attr_x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'layout' => 'grid',
            'spacing' => 5,
            'type' => 'container',
        ], ];
    }
}
