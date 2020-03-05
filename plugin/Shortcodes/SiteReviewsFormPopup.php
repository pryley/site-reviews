<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsFormPopup extends TinymcePopupGenerator
{
    /**
     * @return array
     */
    public function fields()
    {
        return [[
            'type' => 'container',
            'html' => '<p class="strong">'._x('All settings are optional.', 'admin-text', 'site-reviews').'</p>',
        ], [
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
            'tooltip' => esc_attr_x('Enter a custom shortcode heading.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => _x('Description', 'admin-text', 'site-reviews'),
            'minHeight' => 60,
            'minWidth' => 240,
            'multiline' => true,
            'name' => 'description',
            'tooltip' => esc_attr_x('Enter a custom shortcode description.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getCategories(_x('Automatically assign a category to reviews submitted with this shortcode.', 'admin-text', 'site-reviews')),
        [
            'label' => _x('Assign To', 'admin-text', 'site-reviews'),
            'name' => 'assign_to',
            'tooltip' => esc_attr_x('Assign submitted reviews to a custom page/post ID. You can also enter "post_id" to assign reviews to the ID of the current page.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => _x('Classes', 'admin-text', 'site-reviews'),
            'name' => 'class',
            'tooltip' => esc_attr_x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => _x('Hide', 'admin-text', 'site-reviews'),
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
