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
            'html' => sprintf('<p class="strong">%s</p>', esc_html__('All settings are optional.', 'site-reviews')),
            'minWidth' => 320,
            'type' => 'container',
        ], [
            'label' => esc_html__('Title', 'site-reviews'),
            'name' => 'title',
            'tooltip' => __('Enter a custom shortcode heading.', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html__('Display', 'site-reviews'),
            'maxLength' => 5,
            'name' => 'display',
            'size' => 3,
            'text' => '10',
            'tooltip' => __('How many reviews would you like to display (default: 10)?', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html__('Rating', 'site-reviews'),
            'name' => 'rating',
            'options' => [
                '5' => esc_html(sprintf(_n('%s star', '%s stars', 5, 'site-reviews'), 5)),
                '4' => esc_html(sprintf(_n('%s star', '%s stars', 4, 'site-reviews'), 4)),
                '3' => esc_html(sprintf(_n('%s star', '%s stars', 3, 'site-reviews'), 3)),
                '2' => esc_html(sprintf(_n('%s star', '%s stars', 2, 'site-reviews'), 2)),
                '1' => esc_html(sprintf(_n('%s star', '%s stars', 1, 'site-reviews'), 1)),
                '0' => esc_html(__('Unrated', 'site-reviews')),
            ],
            'tooltip' => __('What is the minimum rating to display (default: 1 star)?', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => esc_html__('Pagination', 'site-reviews'),
            'name' => 'pagination',
            'options' => [
                'true' => esc_html__('Enable', 'site-reviews'),
                'ajax' => esc_html__('Enable (using ajax)', 'site-reviews'),
                'false' => esc_html__('Disable', 'site-reviews'),
            ],
            'tooltip' => __('When using pagination this shortcode can only be used once on a page. (default: disable)', 'site-reviews'),
            'type' => 'listbox',
        ],
        $this->getTypes(__('Which type of review would you like to display?', 'site-reviews')),
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
        ], [
            'hidden' => true,
            'name' => 'id',
            'type' => 'textbox',
        ], ];
    }
}
