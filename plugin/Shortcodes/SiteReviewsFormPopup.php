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
            'html' => '<p class="strong">'.esc_html__('All settings are optional.', 'site-reviews').'</p>',
        ], [
            'label' => esc_html__('Title', 'site-reviews'),
            'name' => 'title',
            'tooltip' => __('Enter a custom shortcode heading.', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => esc_html__('Description', 'site-reviews'),
            'minHeight' => 60,
            'minWidth' => 240,
            'multiline' => true,
            'name' => 'description',
            'tooltip' => __('Enter a custom shortcode description.', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getCategories(__('Automatically assign a category to reviews submitted with this shortcode.', 'site-reviews')),
        [
            'label' => esc_html__('Assign To', 'site-reviews'),
            'name' => 'assign_to',
            'tooltip' => __('Assign submitted reviews to a custom page/post ID. You can also enter "post_id" to assign reviews to the ID of the current page.', 'site-reviews'),
            'type' => 'textbox',
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
