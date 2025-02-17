<?php

namespace GLSR_Breakdance;

use Breakdance\Elements\Element;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementControlsTrait;
use GeminiLabs\SiteReviews\Integrations\Breakdance\ElementTrait;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

use function Breakdance\Elements\PresetSections\getPresetSection;

class SiteReviewsForm extends Element
{
    use ElementTrait;
    use ElementControlsTrait;

    public static function bdShortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsFormShortcode::class);
    }

    public static function cssTemplate()
    {
        return file_get_contents(__DIR__.'/css.twig');
    }

    public static function dependencies()
    {
        return [
            [
                'styles' => [
                    '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public static function designControls()
    {
        // ray(getPresetSection("EssentialElements\\AtomV1FormDesign", 'Form', 'form', ['type' => 'popout']));
        return [
            getPresetSection("EssentialElements\\form-container", "Container", "container", ['type' => 'popout']),
            getPresetSection("GLSR\\FormDesign", 'Form', 'form', ['type' => 'popout']),
            getPresetSection("EssentialElements\\spacing_margin_y", "Spacing", "spacing", ['type' => 'popout']),
        ];
    }

    /**
     * @return string
     */
    public static function uiIcon()
    {
        return Helper::svg('assets/images/icons/bricks/icon-form.svg');
    }
}
