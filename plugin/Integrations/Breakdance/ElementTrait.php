<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

trait ElementTrait
{
    /**
     * @return array
     */
    public static function actions()
    {
        return glsr()->filterArray('breakdance/element/actions', static::bdActions(), static::bdShortcode()->tag);
    }

    /**
     * @return array
     */
    public static function badge()
    {
        return [
            'backgroundColor' => '',
            'label' => ' ', // empty because we use this to display an icon
            'textColor' => '',
        ];
    }

    public static function bdActions(): array
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

    public static function bdDependencies(): array
    {
        return [
            [
                'builderCondition' => 'return true;',
                'frontendCondition' => 'return false;',
                'inlineStyles' => [
                    '%%SELECTOR%% a, %%SELECTOR%% button {pointer-events: none}',
                ],
            ],
        ];
    }

    abstract public static function bdShortcode(): ShortcodeContract;

    abstract public static function bdShortcodeClass(): string;

    /**
     * @return string
     */
    public static function category()
    {
        return glsr()->id;
    }

    static function className()
    {
        return 'breakdance-'.Str::dashCase(static::bdShortcode()->tag);
    }

    public static function dependencies()
    {
        return glsr()->filterArray('breakdance/element/dependencies', static::bdDependencies(), static::bdShortcode()->tag);
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
            // 'design',
        ];
    }

    /**
     * @return array
     */
    public static function settings()
    {
        return [
            'bypassPointerEvents' => true,
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
        return static::bdShortcode()->build($args, 'breakdance', false);
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
