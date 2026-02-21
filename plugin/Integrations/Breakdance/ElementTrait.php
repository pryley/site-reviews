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
        return glsr()->filterArray('breakdance/actions',
            static::bdActions(),
            static::bdShortcode()
        );
    }

    /**
     * @return array<array{name: string, template: string}>|false
     */
    public static function additionalClasses()
    {
        $classesFile = static::bdFile('classes.php');
        $classes = file_exists($classesFile) ? include $classesFile : [];
        $classes = glsr()->filterArray('breakdance/additional_classes', $classes, static::bdShortcode());
        return empty($classes) ? false : $classes;
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
                'title' => 'Prevent pointer events on buttons in the builder',
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

    public static function cssTemplate()
    {
        return glsr()->filterString('breakdance/css_template',
            static::bdFileContents('css.twig'),
            static::bdShortcode()
        );
    }

    public static function className()
    {
        return 'breakdance-'.Str::dashCase(static::bdShortcode()->tag);
    }

    public static function defaultCss()
    {
        $parts = explode('_', static::bdShortcode()->tag);
        $suffix = end($parts);
        $handle = sprintf('%s-%s-style', glsr()->ID, $suffix);
        $path = wp_styles()->get_data($handle, 'path');
        $css = ($path && file_exists($path)) ? file_get_contents($path) : '';
        return glsr()->filterString('breakdance/default_css',
            static::bdFileContents('default.css').$css,
            static::bdShortcode()
        );
    }

    public static function dependencies()
    {
        return glsr()->filterArray('breakdance/dependencies',
            static::bdDependencies(),
            static::bdShortcode()
        );
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

    protected static function bdFile(string $file): string
    {
        $reflector = new \ReflectionClass(static::class);
        $dir = dirname($reflector->getFileName());
        return "{$dir}/$file";
    }

    protected static function bdFileContents(string $file): string
    {
        $filePath = static::bdFile($file);
        return file_exists($filePath) ? file_get_contents($filePath) : '';
    }
}
