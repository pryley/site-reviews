<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReview extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    public static function cssTemplate()
    {
        return file_get_contents(__DIR__.'/css.twig');
    }

    public static function defaultCss()
    {
        return file_get_contents(__DIR__.'/default.css');
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
        return Svg::get('assets/images/icons/breakdance/icon-review.svg');
    }
}
