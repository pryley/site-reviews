<?php

namespace GeminiLabs\SiteReviews\Defaults;

class PostTypeLabelDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'add_new' => _x('Add New Review', 'Add New Post (admin-text)', 'site-reviews'),
            'add_new_item' => _x('Add New Review', 'Add New Post (admin-text)', 'site-reviews'),
            'all_items' => _x('All Reviews', 'All Posts (admin-text)', 'site-reviews'),
            'archives' => _x('Review Archives', 'Post Archives (admin-text)', 'site-reviews'),
            'edit_item' => _x('Edit Review', 'Edit Post (admin-text)', 'site-reviews'),
            'insert_into_item' => _x('Insert into Review', 'Insert into Post (admin-text)', 'site-reviews'),
            'menu_name' => glsr()->name,
            'name' => _x('Reviews', 'admin-text', 'site-reviews'),
            'new_item' => _x('New Review', 'New Post (admin-text)', 'site-reviews'),
            'not_found' => _x('No Reviews found', 'No Posts found (admin-text)', 'site-reviews'),
            'not_found_in_trash' => _x('No Reviews found in Trash', 'No Posts found in Trash (admin-text)', 'site-reviews'),
            'search_items' => _x('Search Reviews', 'Search Posts (admin-text)', 'site-reviews'),
            'singular_name' => _x('Review', 'admin-text', 'site-reviews'),
            'uploaded_to_this_item' => _x('Uploaded to this Review', 'Uploaded to this Post (admin-text)', 'site-reviews'),
            'view_item' => _x('View Review', 'View Post (admin-text)', 'site-reviews'),
        ];
    }
}
