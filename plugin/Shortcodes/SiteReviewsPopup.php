<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsPopup extends TinymcePopupGenerator
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
        ], [
            'label' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'maxLength' => 5,
            'name' => 'display',
            'size' => 3,
            'text' => '10',
            'tooltip' => esc_attr_x('How many reviews would you like to display (default: 10)?', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
            'name' => 'rating',
            'options' => [
                '5' => esc_attr(sprintf(_nx('%s star', '%s stars', 5, 'admin-text', 'site-reviews'), 5)),
                '4' => esc_attr(sprintf(_nx('%s star', '%s stars', 4, 'admin-text', 'site-reviews'), 4)),
                '3' => esc_attr(sprintf(_nx('%s star', '%s stars', 3, 'admin-text', 'site-reviews'), 3)),
                '2' => esc_attr(sprintf(_nx('%s star', '%s stars', 2, 'admin-text', 'site-reviews'), 2)),
                '1' => esc_attr(sprintf(_nx('%s star', '%s stars', 1, 'admin-text', 'site-reviews'), 1)),
                '0' => esc_attr_x('Unrated', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => esc_attr_x('What is the minimum rating to display (default: 1 star)?', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => esc_html_x('Pagination', 'admin-text', 'site-reviews'),
            'name' => 'pagination',
            'options' => [
                'true' => esc_attr_x('Enable', 'admin-text', 'site-reviews'),
                'ajax' => esc_attr_x('Enable (using ajax)', 'admin-text', 'site-reviews'),
                'false' => esc_attr_x('Disable', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => esc_attr_x('When using pagination this shortcode can only be used once on a page. (default: disable)', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ],
        $this->getTypes(_x('Which type of review would you like to display?', 'admin-text', 'site-reviews')),
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
            'tooltip' => _x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => esc_html_x('Classes', 'admin-text', 'site-reviews'),
            'name' => 'class',
            'tooltip' => _x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'layout' => 'grid',
            'spacing' => 5,
            'type' => 'container',
        ], [
            'hidden' => true,
            'name' => 'id',
            'type' => 'textbox',
        ], ];
    }
}
