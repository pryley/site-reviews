<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\PaginationDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Style
{
    /**
     * @var array
     */
    public $fields;

    /**
     * @var string
     */
    public $style;

    /**
     * @var array
     */
    public $pagination;

    /**
     * @var array
     */
    public $validation;

    public function __construct()
    {
        $this->style = glsr(OptionManager::class)->get('settings.general.style', 'default');
        $this->setConfig();
    }

    /**
     * @param string $view
     * @return string
     */
    public function filterView($view)
    {
        $styledViews = [
            'templates/form/field',
            'templates/form/response',
            'templates/form/submit-button',
            'templates/reviews-form',
        ];
        if (!preg_match('('.implode('|', $styledViews).')', $view)) {
            return $view;
        }
        $views = $this->generatePossibleViews($view);
        foreach ($views as $possibleView) {
            if (!file_exists(glsr()->file($possibleView))) {
                continue;
            }
            return Str::removePrefix('views/', $possibleView);
        }
        return $view;
    }

    /**
     * @return string
     */
    public function get()
    {
        return apply_filters('site-reviews/style', $this->style);
    }

    /**
     * @return array
     */
    public function setConfig()
    {
        $config = shortcode_atts(
            array_fill_keys(['fields', 'pagination', 'validation'], []),
            glsr()->config('styles/'.$this->style)
        );
        $this->fields = glsr(StyleFieldsDefaults::class)->restrict($config['fields']);
        $this->pagination = glsr(PaginationDefaults::class)->restrict($config['pagination']);
        $this->validation = glsr(StyleValidationDefaults::class)->restrict($config['validation']);
    }

    /**
     * @return void
     */
    public function modifyField(Builder $instance)
    {
        if (!$this->isPublicInstance($instance) || empty(array_filter($this->fields))) {
            return;
        }
        call_user_func_array([$this, 'customize'], [$instance]);
    }

    /**
     * @return array
     */
    public function paginationArgs(array $args)
    {
        return wp_parse_args($args, $this->pagination);
    }

    /**
     * @return void
     */
    protected function customize(Builder $instance)
    {
        if (!array_key_exists($instance->tag, $this->fields)) {
            return;
        }
        $args = wp_parse_args($instance->args, array_fill_keys(['class', 'type'], ''));
        $key = $instance->tag.'_'.$args['type'];
        $classes = Arr::get($this->fields, $key, Arr::get($this->fields, $instance->tag));
        $instance->args['class'] = trim($args['class'].' '.$classes);
        do_action_ref_array('site-reviews/customize/'.$this->style, [$instance]);
    }

    /**
     * @param string $view
     * @return array
     */
    protected function generatePossibleViews($view)
    {
        $basename = basename($view);
        $basepath = rtrim($view, $basename);
        $customPath = 'views/partials/styles/'.$this->style.'/';
        $parts = explode('_', $basename);
        $views = [
            $customPath.$basename,
            $customPath.$parts[0],
            $view,
            $basepath.$parts[0],
        ];
        return array_filter($views);
    }

    /**
     * @return bool
     */
    protected function isPublicInstance(Builder $instance)
    {
        $args = wp_parse_args($instance->args, [
            'is_public' => false,
            'is_raw' => false,
        ]);
        if (is_admin() || !$args['is_public'] || $args['is_raw']) {
            return false;
        }
        return true;
    }
}
