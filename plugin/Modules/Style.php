<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Defaults\PaginationDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleClassesDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @method string classes(string $key)
 * @method string defaultClasses(string $key)
 * @method string defaultValidation(string $key)
 * @method string validation(string $key)
 */
class Style
{
    /**
     * The properties that are accessible.
     *
     * @var array
     */
    protected $accessible = [
        'classes', 'style', 'pagination', 'validation',
    ];

    /**
     * The methods that are callable.
     *
     * @var array
     */
    protected $callable = [
        'classes', 'validation',
    ];

    /**
     * @var array
     */
    protected $classes;

    /**
     * @var string
     */
    protected $style;

    /**
     * @var array
     */
    protected $pagination;

    /**
     * @var array
     */
    protected $validation;

    public function __call($method, $args)
    {
        $property = strtolower(Str::removePrefix($method, 'default'));
        if (!in_array($property, $this->callable)) {
            return;
        }
        $key = Arr::get($args, 0);
        if (str_starts_with($method, 'default')) {
            $className = Helper::buildClassName(['style', $property, 'defaults'], 'Defaults');
            return glsr()->args(glsr($className)->defaults())->$key;
        }
        return glsr()->args($this->__get($property))->$key;
    }

    public function __get($property)
    {
        if (!in_array($property, $this->accessible)) {
            return;
        }
        if (!isset($this->$property)) {
            $style = glsr_get_option('general.style', 'default');
            $config = shortcode_atts(array_fill_keys(['classes', 'pagination', 'validation'], []),
                glsr()->config("styles/{$style}")
            );
            $this->classes = glsr(StyleClassesDefaults::class)->restrict($config['classes']);
            $this->pagination = glsr(PaginationDefaults::class)->restrict($config['pagination']);
            $this->style = $style;
            $this->validation = glsr(StyleValidationDefaults::class)->restrict($config['validation']);
        }
        return $this->$property;
    }

    public function fieldClass(FieldContract $field): string
    {
        if (!array_key_exists($field->tag(), $this->__get('classes'))) {
            return $field->class;
        }
        $key = "{$field->tag()}_{$field->type}";
        $fallback = Arr::get($this->classes, $field->tag());
        $class = Arr::getAs('string', $this->classes, $key, $fallback);
        return trim("{$class} {$field->class}");
    }

    /**
     * This allows us to override the pagination config in /config/styles instead of using a filter hook.
     */
    public function paginationArgs(array $args): array
    {
        return wp_parse_args($args, $this->__get('pagination'));
    }

    public function styleClasses(): string
    {
        $style = $this->__get('style');
        $classes = glsr()->filterString('style', "glsr glsr-{$style}");
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    public function stylesheetUrl(?string $suffix = ''): string
    {
        if ($suffix) {
            $string = 'assets/styles/%1$s/%2$s-%1$s.css';
            $path = sprintf($string, $suffix, $this->__get('style'));
            return file_exists(glsr()->path($path))
                ? glsr()->url($path)
                : glsr()->url(sprintf($string, $suffix, 'default'));
        }
        $string = 'assets/styles/%s.css';
        $path = sprintf($string, $this->__get('style'));
        return file_exists(glsr()->path($path))
            ? glsr()->url($path)
            : glsr()->url(sprintf($string, 'default'));
    }

    public function view(string $view): string
    {
        $templates = [
            'templates/form/field',
            'templates/form/response',
            'templates/form/submit-button',
            'templates/form/type-checkbox',
            'templates/form/type-radio',
            'templates/form/type-toggle',
            'templates/load-more-button',
            'templates/pagination',
            'templates/reviews-form',
        ];
        $templates = glsr()->filterArray('style/templates', $templates);
        if (!preg_match('('.implode('|', $templates).')', $view)) {
            return $view;
        }
        $views = $this->generatePossibleViews($view);
        foreach ($views as $possibleView) {
            if (file_exists(glsr()->file($possibleView))) {
                return Str::removePrefix($possibleView, 'views/');
            }
        }
        return $view;
    }

    protected function generatePossibleViews(string $view): array
    {
        $basename = basename($view);
        $basepath = rtrim($view, $basename);
        $style = $this->__get('style');
        $customPath = "views/styles/{$style}/";
        $parts = explode('_', $basename);
        $views = [
            $customPath.$basename, // styled view
            $customPath.$parts[0], // styled view (base)
            $view, // default view
            $basepath.$parts[0], // default view (base)
        ];
        $views = glsr()->filterArray('style/views', $views, $view);
        return array_filter($views);
    }
}
