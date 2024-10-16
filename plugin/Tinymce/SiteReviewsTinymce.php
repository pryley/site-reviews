<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewsTinymce extends TinymceGenerator
{
    public function fields(): array
    {
        return [
            [
                'html' => sprintf('<p class="strong">%s</p>', _x('All settings are optional.', 'admin-text', 'site-reviews')),
                'minWidth' => 320,
                'type' => 'container',
            ],
            [
                'label' => _x('Title', 'admin-text', 'site-reviews'),
                'name' => 'title',
                'tooltip' => _x('Enter a custom shortcode title.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Display', 'admin-text', 'site-reviews'),
                'maxLength' => 5,
                'name' => 'display',
                'size' => 3,
                'text' => '10',
                'tooltip' => _x('How many reviews would you like to display (default: 10)?', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Rating', 'admin-text', 'site-reviews'),
                'name' => 'rating',
                'options' => [
                    '5' => sprintf(_nx('%s star', '%s stars', 5, 'admin-text', 'site-reviews'), 5),
                    '4' => sprintf(_nx('%s star', '%s stars', 4, 'admin-text', 'site-reviews'), 4),
                    '3' => sprintf(_nx('%s star', '%s stars', 3, 'admin-text', 'site-reviews'), 3),
                    '2' => sprintf(_nx('%s star', '%s stars', 2, 'admin-text', 'site-reviews'), 2),
                    '1' => sprintf(_nx('%s star', '%s stars', 1, 'admin-text', 'site-reviews'), 1),
                    '0' => _x('Unrated', 'admin-text', 'site-reviews'),
                ],
                'tooltip' => _x('What is the minimum rating to display (default: 1 star)?', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => _x('Pagination Type', 'admin-text', 'site-reviews'),
                'name' => 'pagination',
                'options' => [
                    '' => esc_attr_x('No Pagination', 'admin-text', 'site-reviews'),
                    'loadmore' => esc_attr_x('Load More Button', 'admin-text', 'site-reviews'),
                    'ajax' => esc_attr_x('Pagination (AJAX)', 'admin-text', 'site-reviews'),
                    'true' => esc_attr_x('Pagination (with page reload)', 'admin-text', 'site-reviews'),
                ],
                'tooltip' => _x('When using pagination this shortcode can only be used once on a page. (default: disable)', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            $this->fieldTypes(_x('Which type of review would you like to display?', 'admin-text', 'site-reviews')),
            $this->fieldCategories(_x('Limit reviews to this category.', 'admin-text', 'site-reviews')),
            [
                'label' => _x('Assigned Posts', 'admin-text', 'site-reviews'),
                'name' => 'assigned_posts',
                'tooltip' => sprintf(_x('Limit reviews to those assigned to a Post ID. You may also enter "%s" to use the Post ID of the current page.', 'admin-text', 'site-reviews'), 'post_id'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Assigned Users', 'admin-text', 'site-reviews'),
                'name' => 'assigned_users',
                'tooltip' => sprintf(_x('Limit reviews to those assigned to a User ID. You may also enter "%s" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), 'user_id'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Schema', 'admin-text', 'site-reviews'),
                'name' => 'schema',
                'options' => [
                    'true' => _x('Enable rich snippets', 'admin-text', 'site-reviews'),
                    'false' => _x('Disable rich snippets', 'admin-text', 'site-reviews'),
                ],
                'tooltip' => _x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => _x('Classes', 'admin-text', 'site-reviews'),
                'name' => 'class',
                'tooltip' => _x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'columns' => 2,
                'items' => $this->hideOptions(),
                'label' => _x('Hide', 'admin-text', 'site-reviews'),
                'layout' => 'grid',
                'spacing' => 5,
                'type' => 'container',
            ],
            [
                'hidden' => true,
                'name' => 'id',
                'type' => 'textbox',
            ],
        ];
    }

    public function title(): string
    {
        return _x('Latest Reviews', 'admin-text', 'site-reviews');
    }
}
