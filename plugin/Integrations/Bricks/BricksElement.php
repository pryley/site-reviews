<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;

abstract class BricksElement extends \Bricks\Element
{
    public $nestable = false;
    public $scripts = ['GLSR_init'];

    /**
     * @var ShortcodeContract
     */
    protected $elShortcode;

    public function __construct($element = null)
    {
        $this->category = glsr()->id;
        $this->elShortcode = static::shortcode();
        $this->icon = "ti-{$this->elShortcode->tag}";
        $this->name = $this->elShortcode->tag;
        parent::__construct($element);
    }

    public function elementConfig(): array
    {
        return $this->elShortcode->settings();
    }

    public function get_keywords()
    {
        return ['review', 'reviews', 'site reviews'];
    }

    public function get_label()
    {
        return $this->elShortcode->name;
    }

    public function load()
    {
        parent::load();
        $groups = array_map(fn ($args) => $args['group'] ?? '', $this->controls);
        $groups = array_filter($groups, fn ($value) => !str_starts_with($value, '_'));
        $groups = array_values(array_filter(array_unique($groups)));
        foreach ($this->control_groups as $key => $value) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            if (in_array($key, $groups)) {
                continue;
            }
            unset($this->control_groups[$key]);
        }
    }

    public static function registerElement(): void
    {
        $reflection = new \ReflectionClass(static::class);
        $file = $reflection->getFileName();
        $name = static::shortcode()->tag;
        $className = $reflection->getName();
        \Bricks\Elements::register_element($file, $name, $className);
    }

    public function render()
    {
        echo "<{$this->get_tag()} {$this->render_attributes('_root')}>";
        echo $this->elShortcode->build($this->settings, 'bricks');
        echo "</{$this->get_tag()}>";
    }

    /**
     * @see https://academy.bricksbuilder.io/article/filter-bricks-elements-element_name-control_groups/
     */
    public function set_control_groups()
    {
        $groups = [ // order is intentional
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'advanced' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
        ];
        foreach ($groups as $key => $title) {
            $this->control_groups[$key] = [
                'tab' => 'content',
                'title' => $title,
            ];
        }
    }

    /**
     * @see https://academy.bricksbuilder.io/article/filter-bricks-elements-element_name-controls
     */
    public function set_controls()
    {
        foreach ($this->elementConfig() as $key => $args) {
            $args = wp_parse_args($args, [
                'group' => '',
                // 'hasDynamicData' => false,
                // 'hasVariables' => true,
                'tab' => 'content',
                'type' => 'text',
            ]);
            if (!array_key_exists($args['group'], $this->control_groups)) {
                $args['group'] = '';
            }
            $method = Helper::buildMethodName('set', $args['type'], 'control');
            if (!method_exists($this, $method)) {
                $this->controls[$key] = $args;
                continue;
            }
            call_user_func([$this, $method], $key, $args);
        }
        if (count(glsr()->retrieveAs('array', 'review_types', [])) < 2) {
            unset($this->controls['type']);
        }
    }

    abstract public static function shortcode(): ShortcodeContract;

    protected function setCheckboxControl(string $key, array $args): void
    {
        if (empty($args['options'])) {
            $this->controls[$key] = $args;
            return;
        }
        foreach ($args['options'] as $value => $label) {
            $this->controls["{$key}_{$value}"] = [
                'default' => false,
                'group' => $args['group'],
                'label' => $label,
                'tab' => 'content',
                'type' => 'checkbox',
            ];
        }
    }

    protected function setNumberControl(string $key, array $args): void
    {
        $control = [
            // 'small' => true,
            'type' => 'slider',
            'units' => false,
        ];
        $this->controls[$key] = wp_parse_args($control, $args);
    }

    protected function setSelectControl(string $key, array $args): void
    {
        if (!in_array($key, ['assigned_posts', 'assigned_terms', 'assigned_users', 'post_id'])) {
            $this->controls[$key] = $args;
            return;
        }
        $control = [
            'type' => 'select',
            'searchable' => true,
            'optionsAjax' => ['action' => "bricks_glsr_{$key}"],
        ];
        if ('post_id' !== $key) {
            $control['multiple'] = true;
        }
        $this->controls[$key] = wp_parse_args($control, $args);
    }
}
