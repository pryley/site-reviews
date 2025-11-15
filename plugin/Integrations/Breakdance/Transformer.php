<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\Breakdance\Defaults\ControlDefaults;

use function Breakdance\Elements\c;

class Transformer extends \ArrayObject
{
    public string $location;
    public string $shortcode;

    protected array $alerts;
    protected array $popouts;
    protected array $sections;

    public function __construct(string $location, array $config, string $shortcode = '')
    {
        $this->location = in_array($location, ['content', 'design', 'settings'])
            ? $location
            : 'content';
        $this->shortcode = $shortcode;
        $this->alerts = $this->controlAlerts();
        $this->popouts = $this->controlPopouts();
        $this->sections = $this->controlSections();
        $controls = $this->processConfig($config);
        parent::__construct($controls, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    public function control(array $args): array
    {
        $control = glsr(ControlDefaults::class)->restrict($args);
        return c(
            $control['slug'],
            $control['label'],
            $control['children'],
            $control['options'],
            $control['enableMediaQueries'],
            $control['enableHover'],
            $control['keywords']
        );
    }

    public function controls(): array
    {
        $items = [];
        foreach ($this as $item) {
            $items[$item['path']] = $item['control'];
        }
        $items = Arr::unflatten(array_filter($items));
        $controls = [];
        foreach ($items as $slug => $children) {
            $section = $this->section($slug);
            foreach ($children as $key => $child) {
                if (!str_starts_with($key, 'popout_')) {
                    $section['children'][] = $child;
                    continue;
                }
                $popoutSlug = Str::removePrefix($key, 'popout_');
                $section['children'][] = $this->popout($popoutSlug, array_values($child));
            }
            $controls[] = $section;
        }
        return $controls;
    }

    public function description(array $args, string $style = 'default'): array
    {
        if (empty($args['description'])) {
            return [];
        }
        $options = [
            'alertBoxOptions' => [
                'style' => $style,
                'content' => $args['description'],
            ],
            'layout' => 'vertical',
            'type' => 'alert_box',
        ];
        return $this->control([
            'options' => wp_parse_args($options, $args['options'] ?? []),
            'slug' => "{$args['slug']}_description_alert",
        ]);
    }

    public function popout(string $slug, array $children): array
    {
        return $this->control([
            'children' => $children,
            'label' => $this->popouts[$slug] ?? Str::titleCase($slug),
            'slug' => "{$slug}_popout",
            'options' => [
                'type' => 'section',
                'sectionOptions' => [
                    'type' => 'popout',
                ],
            ],
        ]);
    }

    public function section(string $slug, array $children = []): array
    {
        return $this->control([
            'children' => $children,
            'label' => $this->sections[$slug] ?? Str::titleCase($slug),
            'slug' => $slug,
            'options' => [
                'layout' => 'vertical',
                'type' => 'section',
            ],
        ]);
    }

    protected function controlAlerts(): array
    {
        $alerts = [
            'schema' => 'warning',
        ];
        return glsr()->filterArray('breakdance/controls/alerts', $alerts, $this);
    }

    protected function controlPopouts(): array
    {
        $popouts = [
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'filters' => esc_html_x('Filters', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'text' => esc_html_x('Text', 'admin-text', 'site-reviews'),
        ];
        return glsr()->filterArray('breakdance/controls/popouts', $popouts, $this);
    }

    protected function controlSections(): array
    {
        $sections = [ // order is intentional
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
        ];
        return glsr()->filterArray('breakdance/controls/sections', $sections, $this);
    }

    protected function pathPrefix(string $group): string
    {
        if (array_key_exists($group, $this->sections)) {
            return $group;
        }
        if (array_key_exists($group, $this->popouts)) {
            return "general.popout_{$group}";
        }
        return 'general';
    }

    /**
     * @return array[] Each array has a "path" and "control" key
     */
    protected function processConfig(array $config): array
    {
        $config = glsr()->filterArray('breakdance/config', $config, $this);
        $controls = [];
        foreach ($config as $slug => $args) {
            $args = wp_parse_args($args, [
                'description' => '',
                'group' => 'general',
                'label' => '',
                'slug' => $slug,
                'type' => '',
            ]);
            $path = $this->pathPrefix($args['group']);
            $method = Helper::buildMethodName('transform', ($args['type'] ?: 'unknown'));
            if (method_exists($this, $method)) {
                $control = call_user_func([$this, $method], $args);
            } else {
                if (empty($args['options']['type'])) {
                    $args['options'] = ['type' => ($args['type'] ?: 'text')];
                }
                $control = $this->control($args);
            }
            $controlId = $args['slug'] ?? $slug; // because the slug might have been changed
            $controls[$controlId] = [
                'control' => $control,
                'path' => "{$path}.{$controlId}",
            ];
            if (!empty($args['description']) && !empty($control)) {
                $descriptionControl = $this->description($args, $this->alerts[$controlId] ?? 'default');
                $descriptionId = "{$controlId}_description_alert";
                $controls[$descriptionId] = [
                    'control' => $descriptionControl,
                    'path' => "{$path}.{$descriptionId}",
                ];
            }
        }
        return glsr()->filterArray('breakdance/controls', $controls, $this);
    }

    protected function transformCheckbox(array $args): array
    {
        $control = $this->control([
            'label' => $args['label'],
            'slug' => $args['slug'],
            'options' => [
                'layout' => 'inline',
                'type' => 'toggle',
            ],
        ]);
        if (!empty($args['options'])) {
            $control['options']['type'] = 'section';
            foreach ($args['options'] as $slug => $label) {
                $control['children'][] = $this->control([
                    'label' => $label,
                    'slug' => $slug,
                    'options' => [
                        'layout' => 'inline',
                        'type' => 'toggle',
                    ],
                ]);
            }
        }
        return $control;
    }

    protected function transformNumber(array $args): array
    {
        return $this->control([
            'slug' => $args['slug'],
            'label' => $args['label'],
            'options' => [
                'layout' => 'inline',
                'type' => 'number',
                'rangeOptions' => [
                    'min' => Arr::getAs('int', $args, 'min', 0),
                    'max' => Arr::getAs('int', $args, 'max', 1),
                    'step' => Arr::getAs('int', $args, 'step', 1),
                ],
            ],
        ]);
    }

    protected function transformSelect(array $args): array
    {
        $control = $this->control([
            'label' => $args['label'],
            'slug' => $args['slug'],
            'options' => [
                'layout' => 'vertical',
                'placeholder' => $args['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews'),
                'type' => 'dropdown',
            ],
        ]);
        if (!isset($args['options'])) {
            // Unfortunately we can't use this yet because Breakdance does not support
            // AJAX searching in multiselect controls.
            // $control['options']['type'] = 'multiselect';
            // $control['options']['searchable'] = true;
            // $control['options']['multiselectOptions']['populate'] = [
            //     'fetchDataAction' => glsr()->prefix.'breakdance_'.$args['slug'],
            //     'fetchContextPath' => 'content.general.'.$args['slug'],
            // ];
            $control['options']['type'] = 'post_chooser';
            $control['options']['postChooserOptions'] = [
                'multiple' => $args['multiple'] ?? false,
                'postType' => glsr()->prefix.$args['slug'],
                'showThumbnails' => in_array($args['slug'], [
                    'assigned_posts',
                    'assigned_users',
                    'author',
                ]),
            ];
            return $control;
        }
        if (!empty($args['options'])) {
            $callback = fn ($value, $text) => compact('text', 'value');
            $items = array_map($callback, array_keys($args['options']), $args['options']);
            $control['options']['items'] = $items;
            return $control;
        }
        return [];
    }

    protected function transformText(array $args): array
    {
        $slug = $args['slug'];
        if ('id' === $slug) {
            $slug = 'attr_id';
        }
        if ('class' === $slug) {
            $slug = 'attr_class';
        }
        return $this->control([
            'label' => $args['label'],
            'slug' => $slug,
            'options' => [
                'layout' => 'vertical',
                'type' => 'text',
            ],
        ]);
    }
}
