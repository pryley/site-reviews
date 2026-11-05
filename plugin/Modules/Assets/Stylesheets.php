<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Modules\Style;

/**
 * The plugin's stylesheets, one file per component of the site's plugin style.
 */
class Stylesheets
{
    /**
     * The components every other component's rules depend on.
     */
    public const SHARED = ['variables', 'keyframes', 'base', 'loaders'];

    /**
     * @var array{components: string[], styles: array<string, array<string, string>>} the components in page order, and the file of each component by style
     */
    protected array $manifest;

    /**
     * @var string[] the components printed in this request
     */
    protected array $printed = [];

    /**
     * @var int the number of roots that are being built
     */
    protected int $suspended = 0;

    public function __construct()
    {
        $this->manifest = include glsr()->path('assets/styles/manifest.php');
    }

    /**
     * The components in the order a page needs them.
     *
     * @return string[]
     */
    public function components(): array
    {
        return $this->manifest['components'];
    }

    /**
     * The data-glsr-styles value for the components.
     *
     * @param string[] $components
     */
    public function declare(array $components): string
    {
        if (0 < $this->suspended) {
            return '';
        }
        return implode(' ', array_values(array_diff($components, static::SHARED)));
    }

    /**
     * The CSS of every component of a style, in order.
     */
    public function css(string $style = ''): string
    {
        $css = $this->shared($style);
        foreach ($this->components() as $component) {
            if (!in_array($component, static::SHARED, true)) {
                $css .= $this->contents($component, $style);
            }
        }
        return $css;
    }

    /**
     * The file of a component for a style, relative to assets/styles.
     */
    public function file(string $component, string $style = ''): string
    {
        $style = $style ?: glsr(Style::class)->name;
        $files = $this->manifest['styles'][$style] ?? [];
        if (empty($this->manifest['styles'][$style])) {
            // a custom style added by a config filter has no files of its own
            $files = array_combine($this->components(), array_map(fn ($c) => "base/{$c}.css", $this->components()));
        }
        return $files[$component] ?? '';
    }

    /**
     * The HTML of the components' stylesheets: the shared components inline, the others as links.
     *
     * @param string[] $components
     */
    public function print(array $components): string
    {
        // an admin-ajax or REST fragment is inserted into a page that has the stylesheets or adds them from data-glsr-styles
        if (0 < $this->suspended || !$this->isPageRender() || !glsr()->filterBool('assets/css', true)) {
            return '';
        }
        $html = '';
        if (!$this->isPrinted('shared')) {
            $this->printed[] = 'shared';
            $html .= sprintf('<style id="%s">%s</style>', $this->id('shared'), $this->shared());
        }
        $components = [ // in the manifest's order, then the components of other plugins
            ...array_intersect($this->components(), $components),
            ...array_diff($components, $this->components()),
        ];
        foreach ($components as $component) {
            if ($this->isPrinted($component) || in_array($component, static::SHARED, true)) {
                continue;
            }
            $this->printed[] = $component;
            $url = $this->url($component);
            if ($url) {
                $html .= sprintf('<link rel="stylesheet" id="%s" href="%s">', $this->id($component), esc_url($url));
            }
        }
        return $html;
    }

    public function reset(): void
    {
        $this->printed = [];
        $this->suspended = 0;
    }

    /**
     * Undo one suspend().
     */
    public function resume(): void
    {
        $this->suspended = max(0, $this->suspended - 1);
    }

    /**
     * The CSS of the shared components and inline-styles.css.
     */
    public function shared(string $style = ''): string
    {
        $css = '';
        foreach (static::SHARED as $component) {
            $css .= $this->contents($component, $style);
        }
        $config = glsr()->config('inline-styles');
        $css .= str_replace(array_keys($config), array_values($config), $this->read('inline-styles.css'));
        return glsr()->filterString('enqueue/public/inline-styles', $css, $config);
    }

    /**
     * Stop print() from printing until the matching resume().
     */
    public function suspend(): void
    {
        ++$this->suspended;
    }

    /**
     * The URL of a component's file for a style, with the version.
     */
    public function url(string $component, string $style = ''): string
    {
        $style = $style ?: glsr(Style::class)->name;
        $file = $this->file($component, $style);
        $url = $file ? add_query_arg('ver', glsr()->version, glsr()->url("assets/styles/{$file}")) : '';
        return glsr()->filterString('stylesheet/url', $url, $component, $style);
    }

    /**
     * The URL of each component of a style, keyed by component.
     *
     * @return array<string, string>
     */
    public function urls(string $style = ''): array
    {
        $urls = [];
        foreach ($this->components() as $component) {
            $url = $this->url($component, $style);
            if ($url) {
                $urls[$component] = $url;
            }
        }
        return $urls;
    }

    protected function contents(string $component, string $style = ''): string
    {
        $file = $this->file($component, $style);
        return $file ? $this->read($file) : '';
    }

    protected function id(string $component): string
    {
        return 'glsr-css-'.$component;
    }

    protected function isPageRender(): bool
    {
        return !wp_doing_ajax() && !wp_is_serving_rest_request();
    }

    protected function isPrinted(string $component): bool
    {
        return in_array($component, $this->printed, true);
    }

    protected function read(string $file): string
    {
        $path = glsr()->path("assets/styles/{$file}");
        if (!file_exists($path)) {
            glsr_log()->error("Stylesheet is missing: {$path}");
            return '';
        }
        return trim((string) file_get_contents($path));
    }
}
