<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class RatingField extends FieldBase
{
    public const HIGH_RATED = 'high-rated';
    public const LOW_RATED = 'low-rated';

    public function field_type()
    {
        return 'profile-rating';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-star-filled"></span>';
    }

    public function field_title()
    {
        return esc_html_x('Rating', 'admin-text', 'site-reviews');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'label' => [
                    'label' => esc_html_x('Title', 'admin-text', 'site-reviews'),
                    'field' => self::INPUT_FIELD
                ]
            ]
        ];
    }
}
