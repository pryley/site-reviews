<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummary extends Element
{
    use ElementTrait, ElementControlsTrait;

    public static function bdShortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }

    /**
     * @return array[]
     */
    public static function designControls()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Helper::svg('assets/images/icons/bricks/icon-summary.svg');
    }
}
