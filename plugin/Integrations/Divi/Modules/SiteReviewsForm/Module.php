<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReviewsForm;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\Divi\Modules\DiviModule;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class Module extends DiviModule
{
    public static function blockName(): string
    {
        return 'glsr-divi/form';
    }

    public static function shortcodeInstance(): ShortcodeContract
    {
        static $shortcode;
        if (empty($shortcode)) {
            $shortcode = glsr(SiteReviewsFormShortcode::class);
        }
        return $shortcode;
    }
}
