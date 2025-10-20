<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/reviews';
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(SiteReviewsShortcode::class);
        }
        return $shortcode;
    }
}
