<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class BricksElement extends \Bricks\Element
{
    use IntegrationShortcode;

    public $nestable = false;
    public $scripts = ['GLSR_init'];

    public function __construct($element = null)
    {
        $this->category = glsr()->id;
        $this->icon = "ti-{$this->shortcodeInstance()->tag}";
        $this->name = $this->shortcodeInstance()->tag;
        parent::__construct($element);
    }

    public function add_actions()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_add_inline_style('bricks-frontend', ".brxe-{$this->name} {width: 100%}");
        });
    }

    public function designConfig(): array
    {
        return [];
    }

    public function elementConfig(): array
    {
        return $this->shortcodeInstance()->settings();
    }

    public function get_keywords()
    {
        return ['review', 'reviews', 'site reviews'];
    }

    public function get_label()
    {
        return $this->shortcodeInstance()->name;
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
        $className = $reflection->getName();
        \Bricks\Elements::register_element($file, '', $className);
    }

    public function render()
    {
        $buttonClasses = ['bricks-button'];
        if ($buttonStyle = $this->styledSetting('style_button_preset')) {
            $buttonClasses[] = "bricks-background-{$buttonStyle}";
        }
        if ($buttonSize = $this->styledSetting('style_button_size')) {
            $buttonClasses[] = $buttonSize;
        }
        $buttonClasses = implode(' ', $buttonClasses);
        $html = $this->shortcodeInstance()->build($this->settings, 'bricks');
        $html = str_replace('glsr-button', "glsr-button {$buttonClasses}", $html);
        echo "<{$this->get_tag()} {$this->render_attributes('_root')}>";
        echo $html;
        echo "</{$this->get_tag()}>";
    }

    /**
     * @see https://academy.bricksbuilder.io/article/filter-bricks-elements-element_name-control_groups/
     */
    public function set_control_groups()
    {
        $groups = [ // order is intentional
            'general' => esc_html_x('General', 'admin-text', 'site-reviews'),
            'design' => esc_html_x('Design', 'admin-text', 'site-reviews'),
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
        $this->controls ??= [];
        $this->control_groups ??= [];
        $config = array_merge($this->elementConfig(), $this->designConfig());
        foreach ($config as $key => $args) {
            $args = wp_parse_args($args, [
                'group' => 'general',
                'tab' => 'content',
                'type' => 'text',
            ]);
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

    public function styledClasses(array $classes = []): array
    {
        if ($align = $this->styledSetting('style_align')) {
            $classes[] = "items-justified-{$align}";
        }
        return $classes;
    }

    public function styledSetting(string $key): string
    {
        $value = $this->settings[$key] ?? '';
        $value = $value ?: $this->theme_styles[$key] ?? '';
        return Cast::toString($value);
    }

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
        if (isset($args['css'])) {
            $this->controls[$key] = $args;
            return;
        }
        $control = [
            // 'small' => true,
            'type' => 'slider',
            'units' => false,
        ];
        $this->controls[$key] = wp_parse_args($control, $args);
    }

    protected function setSelectControl(string $key, array $args): void
    {
        if (!in_array($key, ['assigned_posts', 'assigned_terms', 'assigned_users', 'author', 'post_id'])) {
            $this->controls[$key] = $args;
            return;
        }
        $control = [
            'type' => 'select',
            'searchable' => true,
            'optionsAjax' => ['action' => "bricks_glsr_{$key}"],
        ];
        if (in_array($key, ['assigned_posts', 'assigned_terms', 'assigned_users'])) {
            $control['multiple'] = true;
        }
        $this->controls[$key] = wp_parse_args($control, $args);
    }
}
