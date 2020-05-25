<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeLabelDefaults extends Defaults
{
    protected $plural;
    protected $singular;

    public function __construct()
    {
        $this->plural = _x('Reviews', 'admin-text', 'site-reviews');
        $this->singular = _x('Review', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'add_new_item' => sprintf(_x('Add New %s', 'Add New Post (admin-text)', 'site-reviews'), $this->plural),
            'all_items' => sprintf(_x('All %s', 'All Posts (admin-text)', 'site-reviews'), $this->plural),
            'archives' => sprintf(_x('%s Archives', 'Post Archives (admin-text)', 'site-reviews'), $this->singular),
            'edit_item' => sprintf(_x('Edit %s', 'Edit Post (admin-text)', 'site-reviews'), $this->singular),
            'insert_into_item' => sprintf(_x('Insert into %s', 'Insert into Post (admin-text)', 'site-reviews'), $this->singular),
            'menu_name' => glsr()->name,
            'name' => $this->plural,
            'new_item' => sprintf(_x('New %s', 'New Post (admin-text)', 'site-reviews'), $this->singular),
            'not_found' => sprintf(_x('No %s found', 'No Posts found (admin-text)', 'site-reviews'), $this->plural),
            'not_found_in_trash' => sprintf(_x('No %s found in Trash', 'No Posts found in Trash (admin-text)', 'site-reviews'), $this->plural),
            'search_items' => sprintf(_x('Search %s', 'Search Posts (admin-text)', 'site-reviews'), $this->plural),
            'singular_name' => $this->singular,
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Uploaded to this Post (admin-text)', 'site-reviews'), $this->singular),
            'view_item' => sprintf(_x('View %s', 'View Post (admin-text)', 'site-reviews'), $this->singular),
        ];
    }
}
