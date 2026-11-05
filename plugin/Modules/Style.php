<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * The classes, stylesheet, and templates of a plugin style.
 */
class Style
{
    public readonly array $classes;
    public readonly string $name;
    public readonly array $validation;

    /** @var array<string, int>|null */
    protected ?array $styleableViews = null;
    /** @var array<string, array{button: string, wrapper: string}> */
    protected array $themeButtonClasses = [];

    public function __construct(string $name = '')
    {
        $name = $name ?: glsr_get_option('general.style', 'default', 'string');
        $config = glsr()->config("styles/{$name}");
        $this->name = $name;
        $this->classes = glsr(StyleClassesDefaults::class)->merge($config['classes'] ?? []);
        $this->validation = glsr(StyleValidationDefaults::class)->restrict($config['validation'] ?? []);
    }

    public function classes(string $key, string $additionalClasses = ''): string
    {
        $classes = match ($key) {
            'button', 'button_wrapper' => $this->buttonClasses($key, $additionalClasses),
            default => [$this->classes[$key] ?? '', $additionalClasses],
        };
        return glsr(Sanitizer::class)->sanitizeAttrClass(implode(' ', $classes));
    }

    public function defaultClasses(string $key): string
    {
        return glsr(StyleClassesDefaults::class)->defaults()[$key] ?? '';
    }

    public function defaultValidation(string $key): string
    {
        return glsr(StyleValidationDefaults::class)->defaults()[$key] ?? '';
    }

    public function fieldElementClass(FieldContract $field): string
    {
        $tag = $field->tag();
        return $this->classes["{$tag}_{$field->original_type}"]
            ?? $this->classes["{$tag}_{$field->type}"]
            ?? $this->classes[$tag]
            ?? '';
    }

    /**
     * Get the classes of the label beside a checkbox, radio, range or toggle input.
     */
    public function fieldElementLabelClass(FieldContract $field): string
    {
        return $this->classes["label_{$field->original_type}"]
            ?? $this->classes["label_{$field->type}"]
            ?? '';
    }

    /**
     * Return the HTML source of a view located in the plugin (i.e. ignore theme overrides).
     */
    public function source(string $view): string
    {
        $candidates = $this->isStyleable($view) ? $this->possibleViews($view) : [$view];
        foreach ($candidates as $candidate) {
            foreach ([ // order is intentional
                glsr()->path("{$candidate}.php"),
                glsr()->path("views/{$candidate}.php"),
            ] as $file) {
                if (file_exists($file)) {
                    return (string) file_get_contents($file);
                }
            }
        }
        return '';
    }

    public function styleClasses(string $additional = ''): string
    {
        $style = glsr()->filterString('style', "glsr-{$this->name}");
        return glsr(Sanitizer::class)->sanitizeAttrClass(
            "glsr {$style} {$additional}"
        );
    }

    public function usesBlockButton(): bool
    {
        if (!glsr()->filterBool('assets/css', true)) {
            return false;
        }
        return str_contains($this->classes['button'], 'wp-block-button');
    }

    public function validation(string $key): string
    {
        return $this->validation[$key] ?? '';
    }

    public function view(string $view): string
    {
        if (!$this->isStyleable($view)) {
            return $view;
        }
        foreach ($this->possibleViews($view) as $candidate) {
            if (file_exists(glsr()->file($candidate))) {
                return Str::removePrefix($candidate, 'views/');
            }
        }
        return $view;
    }

    protected function baseView(string $view): string
    {
        $filename = basename($view);
        $shortName = strstr($filename, '_', true) ?: $filename; // drop _{type} suffix
        return dirname($view).'/'.$shortName;
    }

    /**
     * @return string[]
     */
    protected function buttonClasses(string $key, string $additionalClasses): array
    {
        $additional = explode(' ', glsr(Sanitizer::class)->sanitizeAttrClass($additionalClasses));
        $blockStyles = preg_grep('/^is-style-/', $additional);
        $themeClasses = [
            'button' => '',
            'wrapper' => '',
        ];
        if ($this->usesBlockButton() && \WP_Block_Type_Registry::get_instance()->is_registered('core/button')) {
            $className = implode(' ', preg_grep('/^glsr-/', $additional, PREG_GREP_INVERT)); // exclude glsr-* classes
            $themeClasses = $this->themeButtonClasses[$className] ??= $this->renderedBlockButtonClasses($className);
        }
        if ('button_wrapper' === $key) {
            return [$themeClasses['wrapper'], ...$blockStyles];
        }
        return [$this->classes['button'], $themeClasses['button'], ...array_diff($additional, $blockStyles)];
    }

    protected function isStyleable(string $view): bool
    {
        $this->styleableViews ??= array_flip(glsr()->filterArray('style/templates', [
            'templates/form/field',
            'templates/form/field-description',
            'templates/form/field-errors',
            'templates/form/field-label',
            'templates/form/response',
            'templates/form/submit-button',
            'templates/form/type-checkbox',
            'templates/form/type-radio',
            'templates/form/type-range',
            'templates/form/type-toggle',
            'templates/load-more-button',
            'templates/pagination',
            'templates/reviews-form',
        ]));
        return isset($this->styleableViews[$this->baseView($view)]);
    }

    protected function possibleViews(string $view): array
    {
        $baseView = $this->baseView($view);
        $styledPath = "views/styles/{$this->name}/";
        $candidates = [ // order is intentional
            $styledPath.basename($view),
            $styledPath.basename($baseView),
            $view,
            $baseView,
        ];
        return array_filter(glsr()->filterArray('style/views', $candidates, $view));
    }

    /**
     * @return array{button: string, wrapper: string}
     */
    protected function renderedBlockButtonClasses(string $className): array
    {
        $classes = [
            'button' => '',
            'wrapper' => '',
        ];
        $blocks = parse_blocks(sprintf(
            // <a> because a theme's filter may look for one (Bootscore) and text because WordPress renders nothing for an empty button
            '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button %s --><div class="%s"><a class="wp-block-button__link wp-element-button">x</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
            $className ? wp_json_encode(['className' => $className]) : '',
            trim("wp-block-button {$className}")
        ));
        $tags = new \WP_HTML_Tag_Processor(render_block($blocks[0]));
        if ($tags->next_tag([
            'class_name' => 'wp-block-button',
            'tag_name' => 'div',
        ])) {
            $wrapper = array_diff(iterator_to_array($tags->class_list(), false), ['wp-block-button'], explode(' ', $className));
            $classes['wrapper'] = implode(' ', $wrapper);
        }
        if ($tags->next_tag('a')) {
            $button = array_diff(iterator_to_array($tags->class_list(), false), ['wp-block-button__link', 'wp-element-button']);
            $classes['button'] = implode(' ', $button);
        }
        return $classes;
    }
}
