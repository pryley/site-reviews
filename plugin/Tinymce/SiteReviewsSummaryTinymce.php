<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewsSummaryTinymce extends TinymceGenerator
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
            $this->fieldTypes(_x('Which type of review would you like to use?', 'admin-text', 'site-reviews')),
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
        ];
    }

    public function title(): string
    {
        return _x('Rating Summary', 'admin-text', 'site-reviews');
    }
}
