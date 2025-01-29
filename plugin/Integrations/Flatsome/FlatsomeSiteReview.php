<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FlatsomeSiteReview extends FlatsomeShortcode
{
    public function options(): array
    {
        return [
            'post_id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Review ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter the Post ID of the review you want to display.', 'admin-text', 'site-reviews'),
                'full_width' => true,
            ],
            'glsr_group_hide' => [
                'type' => 'group',
                'heading' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'options' => $this->hideOptions(),
            ],
            'glsr_group_advanced' => [
                'type' => 'group',
                'heading' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
                'options' => [
                    'id' => [
                        'type' => 'textfield',
                        'heading' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                        'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                        'full_width' => true,
                    ],
                    'class' => [
                        'type' => 'textfield',
                        'heading' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                        'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                        'full_width' => true,
                    ],
                    'visibility' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Visibility', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'options' => [
                            '' => esc_html_x('Visible', 'admin-text', 'site-reviews'),
                            'hidden' => esc_html_x('Hidden', 'admin-text', 'site-reviews'),
                            'hide-for-medium' => esc_html_x('Only for Desktop', 'admin-text', 'site-reviews'),
                            'show-for-small' => esc_html_x('Only for Mobile', 'admin-text', 'site-reviews'),
                            'show-for-medium hide-for-small' => esc_html_x('Only for Tablet', 'admin-text', 'site-reviews'),
                            'show-for-medium' => esc_html_x('Hide for Desktop', 'admin-text', 'site-reviews'),
                            'hide-for-small' => esc_html_x('Hide for Mobile', 'admin-text', 'site-reviews'),
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-review.svg');
    }

    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
