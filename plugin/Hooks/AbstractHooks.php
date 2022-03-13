<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractHooks
{
    protected $basename;
    protected $id;
    protected $prefix;
    protected $taxonomy;
    protected $type;

    public function __construct()
    {
        $this->basename = plugin_basename(glsr()->file);
        $this->id = glsr()->id;
        $this->prefix = glsr()->prefix;
        $this->taxonomy = glsr()->taxonomy;
        $this->type = glsr()->post_type;
    }

    /**
     * @param string $classname
     * @return void
     */
    public function hook($classname, array $hooks)
    {
        $controller = glsr($classname);
        foreach ($hooks as $hook) {
            if (2 > count($hook)) {
                continue;
            }
            $func = Str::startsWith('filter', $hook[0]) ? 'add_filter' : 'add_action';
            $hook = array_pad($hook, 3, 10); // priority
            $hook = array_pad($hook, 4, 1); // allowed args
            call_user_func($func, $hook[1], [$controller, $hook[0]], (int) $hook[2], (int) $hook[3]);
        }
    }

    /**
     * @return void
     */
    abstract public function run();
}
