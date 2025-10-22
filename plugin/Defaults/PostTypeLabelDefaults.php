<?php

namespace GeminiLabs\SiteReviews\Defaults;

class PostTypeLabelDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'add_new' => _x('Add Review', 'Add Post (admin-text)', 'site-reviews'),
            'add_new_item' => _x('Add Review', 'Add Post (admin-text)', 'site-reviews'),
            'all_items' => _x('All Reviews', 'All Posts (admin-text)', 'site-reviews'),
            'archives' => _x('Review Archives', 'Post Archives (admin-text)', 'site-reviews'),
            'attributes' => _x('Review Attributes', 'Post Attributes (admin-text)', 'site-reviews'),
            'edit_item' => _x('Edit Review', 'Edit Post (admin-text)', 'site-reviews'),
            'filter_items_list' => _x('Filter reviews list', 'Filter posts list (admin-text)', 'site-reviews'),
            'insert_into_item' => _x('Insert into Review', 'Insert into Post (admin-text)', 'site-reviews'),
            'item_link' => _x('Review Link', 'Post Link (admin-text)', 'site-reviews'),
            'item_link_description' => _x('A link to a review.', 'A link to a post. (admin-text)', 'site-reviews'),
            'item_published' => _x('Review approved.', 'Post published. (admin-text)', 'site-reviews'),
            'item_published_privately' => _x('Review published privately.', 'Post published privately. (admin-text)', 'site-reviews'),
            'item_reverted_to_draft' => _x('Review reverted to draft.', 'Post reverted to draft. (admin-text)', 'site-reviews'),
            'item_scheduled' => _x('Review scheduled.', 'Post scheduled. (admin-text)', 'site-reviews'),
            'item_trashed' => _x('Review trashed.', 'Post trashed. (admin-text)', 'site-reviews'),
            'item_updated' => _x('Review updated.', 'Post updated. (admin-text)', 'site-reviews'),
            'items_list' => _x('Reviews list', 'Posts list (admin-text)', 'site-reviews'),
            'items_list_navigation' => _x('Reviews list navigation', 'Posts list navigation (admin-text)', 'site-reviews'),
            'menu_name' => glsr()->name,
            'name' => _x('Reviews', 'admin-text', 'site-reviews'),
            'new_item' => _x('New Review', 'New Post (admin-text)', 'site-reviews'),
            'not_found' => _x('No Reviews found', 'No Posts found (admin-text)', 'site-reviews'),
            'not_found_in_trash' => _x('No Reviews found in Trash', 'No Posts found in Trash (admin-text)', 'site-reviews'),
            'search_items' => _x('Search Reviews', 'Search Posts (admin-text)', 'site-reviews'),
            'singular_name' => _x('Review', 'admin-text', 'site-reviews'),
            'uploaded_to_this_item' => _x('Uploaded to this Review', 'Uploaded to this Post (admin-text)', 'site-reviews'),
            'view_item' => _x('View Review', 'View Post (admin-text)', 'site-reviews'),
            'view_items' => _x('View Reviews', 'View Posts (admin-text)', 'site-reviews'),
        ];
    }
}
