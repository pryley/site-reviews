<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Arr;

trait ElementTrait
{
    /**
     * @return array
     */
    public static function actions()
    {
        return [
            'onMountedElement' => [[
                'script' => 'GLSR_init();',
            ]],
            // 'onPropertyChange' => [[
            //     'script' => 'GLSR_init();',
            // ]],
        ];
    }

    /**
     * @return array
     */
    public static function badge()
    {
        return [
            'backgroundColor' => '#E8FF5E',
            'label' => 'SR',
            'textColor' => '#201E1D',
        ];
    }

    abstract public static function bdShortcode(): ShortcodeContract;

    /**
     * @return string
     */
    public static function category()
    {
        return glsr()->id;
    }

    static function dependencies()
    {
        return [
            [
                'inlineStyles' => [
                    '%%SELECTOR%% a, %%SELECTOR%% button {pointer-events: none}',
                ],
                'frontendCondition' => "return false;",
            ],
        ];
    }

    /**
     * @return string
     */
    public static function name()
    {
        return static::bdShortcode()->name();
    }

    /**
     * @return array
     */
    public static function nestingRule()
    {
        return [
            'type' => 'final',
        ];
    }

    /**
     * @return string[]
     */
    public static function propertyPathsToSsrElementWhenValueChanges()
    {
        return [
            'content',
        ];
    }

    /**
     * @return array
     */
    public static function settings()
    {
        return [
            'disableAI' => true,
        ];
    }

    /**
     * @return array[]
     */
    public static function settingsControls()
    {
        return [];
    }

    /**
     * @param mixed $propertiesData
     * @param mixed $parentPropertiesData
     * @param bool  $isBuilder
     * @param int   $repeaterItemNodeId
     *
     * @return string
     */
    public static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
    {
        $args = static::ssrArgs(Arr::consolidate($propertiesData));
        return static::bdShortcode()->build($args, 'breakdance');
    }

    abstract public static function ssrArgs(array $propertiesData): array;

    /**
     * @return string
     */
    public static function slug()
    {
        return __CLASS__;
    }

    /**
     * @return string
     */
    public static function template()
    {
        return '%%SSR%%';
    }
}
