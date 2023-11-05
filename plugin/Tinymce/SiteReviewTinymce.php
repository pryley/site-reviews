<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewTinymce extends TinymceGenerator
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
                'label' => _x('Post ID', 'admin-text', 'site-reviews'),
                'name' => 'post_id',
                'tooltip' => _x('Enter a the Post ID of the review to display.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
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
        return _x('Single Review', 'admin-text', 'site-reviews');
    }
}
