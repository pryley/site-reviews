<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

/**
 * Trigger order of primary WP hooks:
 * 
 * 1. plugins_loaded     Fires once activated plugins have loaded.
 * 2. load_textdomain    Fires before the MO translation file is loaded.
 * 3. after_setup_theme  Fires after the theme is loaded.
 * 4. init               Fires after WordPress has finished loading but before any headers are sent.
 * 5. wp_loaded          Fires after WordPress, all plugins, and the theme are fully loaded and instantiated.
 * 6. admin_init         Fires as an admin screen or script is being initialized.
 * 7. current_screen     Fires after the current screen has been set.
 * 8. load-{$page_hook}  Fires before a particular screen is loaded.
 */
abstract class AbstractHooks implements HooksContract
{
    protected $basename;
    protected $id;
    protected $prefix;
    protected $taxonomy;
    protected $type;

    public function __construct()
    {
        $this->basename = glsr()->basename;
        $this->id = glsr()->id;
        $this->prefix = glsr()->prefix;
        $this->taxonomy = glsr()->taxonomy;
        $this->type = glsr()->post_type;
    }

    public function hasInit(): bool
    {
        return false;
    }

    public function hasPluginsLoaded(): bool
    {
        return false;
    }

    public function hook(string $classname, array $hooks): void
    {
        glsr()->singleton($classname); // make singleton
        $controller = glsr($classname);
        foreach ($hooks as $hook) {
            if (2 > count($hook)) {
                continue;
            }
            $func = str_starts_with($hook[0], 'filter') ? 'add_filter' : 'add_action';
            $hook = array_pad($hook, 3, 10); // priority
            $hook = array_pad($hook, 4, 1); // accepted args
            $args = [ // order is intentional!
                'hook' => $hook[1],
                'callback' => [$controller, $hook[0]],
                'priority' => (int) $hook[2],
                'args' => (int) $hook[3],
            ];
            if (!str_starts_with($args['hook'], glsr()->id) && method_exists($controller, 'proxy')) {
                $args['callback'] = $controller->proxy($hook[0]);
            }
            call_user_func_array($func, array_values($args));
        }
    }

    /**
     * @action init:10
     */
    public function onInit(): void
    {
    }

    /**
     * @action plugins_loaded:0
     */
    public function onPluginsLoaded(): void
    {
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path, $fallback = '', string $cast = '')
    {
        return glsr_get_option($path, $fallback, $cast);
    }

    public function run(): void
    {
    }

    public function runDeferred(): void
    {
        if ($this->hasInit()) {
            add_action('init', [$this, 'onInit']);
        }
        if ($this->hasPluginsLoaded()) {
            add_action('plugins_loaded', [$this, 'onPluginsLoaded'], -10);
        }
    }
}
