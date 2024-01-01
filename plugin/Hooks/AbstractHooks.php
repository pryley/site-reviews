<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Database\OptionManager;

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
            $hook = array_pad($hook, 4, 1); // allowed args
            call_user_func($func, $hook[1], [$controller, $hook[0]], (int) $hook[2], (int) $hook[3]);
        }
    }

    /**
     * @action init:10
     */
    public function onInit(): void
    {
    }

    /**
     * @action plugins_loaded:10
     */
    public function onPluginsLoaded(): void
    {
    }

    /**
     * @param mixed $fallback
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
            add_action('plugins_loaded', [$this, 'onPluginsLoaded']);
        }
    }
}
