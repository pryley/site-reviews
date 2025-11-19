<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Defaults\PaginationDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Style
{
    public array $classes;
    public string $style;
    public array $pagination;
    public array $validation;

    public function __construct()
    {
        $styleName = glsr_get_option('general.style', 'default');
        $config = shortcode_atts(
            array_fill_keys(['classes', 'pagination', 'validation'], []),
            glsr()->config("styles/{$styleName}")
        );
        $this->style = $styleName;
        $this->classes = glsr(StyleClassesDefaults::class)->restrict($config['classes']);
        $this->pagination = glsr(PaginationDefaults::class)->restrict($config['pagination']);
        $this->validation = glsr(StyleValidationDefaults::class)->restrict($config['validation']);
    }

    public function classes(string $key): string
    {
        return $this->classes[$key] ?? '';
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
        if (!array_key_exists($tag, $this->classes)) {
            return $field->class;
        }
        $specific = "{$tag}_{$field->type}";
        $fallback = $this->classes[$tag] ?? '';
        $custom = Arr::getAs('string', $this->classes, $specific, $fallback);
        return trim("{$custom} {$field->class}");
    }

    /**
     * This allows us to override the pagination config in /config/styles instead of using a filter hook.
     */
    public function paginationArgs(array $args): array
    {
        return wp_parse_args($args, $this->pagination);
    }

    public function styleClasses(string $additional = ''): string
    {
        $style = glsr()->filterString('style', "glsr-{$this->style}");
        $classes = ['glsr', $style, $additional];
        return glsr(Sanitizer::class)->sanitizeAttrClass(implode(' ', $classes));
    }

    public function stylesheetUrl(?string $suffix = ''): string
    {
        if ($suffix) {
            $format = 'assets/styles/%1$s/%2$s-%1$s.css';
            $path = sprintf($format, $suffix, $this->style);
            $fallback = sprintf($format, $suffix, 'default');
        } else {
            $format = 'assets/styles/%s.css';
            $path = sprintf($format, $this->style);
            $fallback = sprintf($format, 'default');
        }
        return file_exists(glsr()->path($path))
            ? glsr()->url($path)
            : glsr()->url($fallback);
    }

    public function validation(string $key): string
    {
        return $this->validation[$key] ?? '';
    }

    public function view(string $view): string
    {
        static $allowed = null;
        if (null === $allowed) {
            $allowed = glsr()->filterArray('style/templates', [
                'templates/form/field',
                'templates/form/response',
                'templates/form/submit-button',
                'templates/form/type-checkbox',
                'templates/form/type-radio',
                'templates/form/type-range',
                'templates/form/type-toggle',
                'templates/load-more-button',
                'templates/pagination',
                'templates/reviews-form',
            ]);
            $allowed = '~('.implode('|', $allowed).')~';
        }
        if (!preg_match($allowed, $view)) {
            return $view;
        }
        foreach ($this->possibleViews($view) as $candidate) {
            if (file_exists(glsr()->file($candidate))) {
                return Str::removePrefix($candidate, 'views/');
            }
        }
        return $view;
    }

    protected function possibleViews(string $view): array
    {
        $filename = basename($view);
        $dirname = dirname($view).'/';
        $shortName = strstr($filename, '_', true) ?: $filename; // before first _ or full
        $styledPath = "views/styles/{$this->style}/";
        $candidates = [
            $styledPath.$filename, // styled exact
            $styledPath.$shortName, // styled base
            $view, // default exact
            $dirname.$shortName, // default base
        ];
        return array_filter(glsr()->filterArray('style/views', $candidates, $view));
    }
}
