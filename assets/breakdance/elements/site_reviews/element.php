<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviews extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsShortcode::class);
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
        return Svg::get('assets/images/icons/breakdance/icon-reviews.svg');
    }
}
