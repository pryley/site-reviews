<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractHooks implements HooksContract
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
     * The method gets an option directly from the database and is safe to use in Hook classes.
     * @param mixed $fallback
     * @return mixed
     */
    public function option(string $path, $fallback = '', string $cast = '')
    {
        $data = glsr(OptionManager::class)->wp(OptionManager::databaseKey(), [], 'array');
        $path = Str::prefix($path, 'settings.');
        $value = Arr::get($data, $path, $fallback);
        return Cast::to($cast, $value);
    }

    public function run(): void
    {
    }

    /**
     * @action init:10
     */
    public function runInit(): void
    {
    }

    /**
     * @action plugin_loaded:10
     */
    public function runPluginLoaded(): void
    {
    }
}
