<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Helpers\Str;

abstract class Hooks
{
    protected $addon;
    protected $basename;
    protected $controller;

    public function __construct()
    {
        $this->addon = $this->addon();
        $this->basename = plugin_basename($this->addon->file);
        $this->controller = $this->controller();
    }

    public function hook(string $classname, array $hooks): void
    {
        glsr()->singleton($classname); // make singleton
        $controller = glsr($classname);
        foreach ($hooks as $hook) {
            if (2 > count($hook)) {
                continue;
            }
            $func = Str::startsWith($hook[0], 'filter') ? 'add_filter' : 'add_action';
            $hook = array_pad($hook, 3, 10); // priority
            $hook = array_pad($hook, 4, 1); // allowed args
            call_user_func($func, $hook[1], [$controller, $hook[0]], (int) $hook[2], (int) $hook[3]);
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->runIntegrations();
        add_action('admin_enqueue_scripts', [$this->controller, 'enqueueAdminAssets']);
        add_action('enqueue_block_editor_assets', [$this->controller, 'enqueueBlockAssets']);
        add_action('wp_enqueue_scripts', [$this->controller, 'enqueuePublicAssets']);
        add_filter('plugin_action_links_'.$this->basename, [$this->controller, 'filterActionLinks']);
        add_filter('site-reviews/capabilities', [$this->controller, 'filterCapabilities']);
        add_filter('site-reviews/config', [$this->controller, 'filterConfigPath']);
        add_filter('site-reviews/addon/documentation', [$this->controller, 'filterDocumentation']);
        add_filter('gettext_'.$this->addon->id, [$this->controller, 'filterGettext'], 10, 2);
        add_filter('gettext_with_context_'.$this->addon->id, [$this->controller, 'filterGettextWithContext'], 10, 3);
        add_filter('ngettext_'.$this->addon->id, [$this->controller, 'filterNgettext'], 10, 4);
        add_filter('ngettext_with_context_'.$this->addon->id, [$this->controller, 'filterNgettextWithContext'], 10, 5);
        add_filter('site-reviews/path', [$this->controller, 'filterFilePaths'], 10, 2);
        add_filter($this->addon->id.'/render/view', [$this->controller, 'filterRenderView']);
        add_filter('site-reviews/roles', [$this->controller, 'filterRoles']);
        add_filter('site-reviews/defer-scripts', [$this->controller, 'filterScriptsDefer']);
        add_filter('site-reviews/addon/settings', [$this->controller, 'filterSettings']);
        add_filter('site-reviews/addon/subsubsub', [$this->controller, 'filterSubsubsub']);
        add_filter('site-reviews/translation/entries', [$this->controller, 'filterTranslationEntries']);
        add_filter('site-reviews/translator/domains', [$this->controller, 'filterTranslatorDomains']);
        add_filter($this->addon->id.'/activate', [$this->controller, 'install']);
        add_action('admin_init', [$this->controller, 'onActivation']);
        add_action('init', [$this->controller, 'registerBlocks'], 9);
        add_action('init', [$this->controller, 'registerLanguages'], -10);
        add_action('init', [$this->controller, 'registerShortcodes']);
        add_action('init', [$this->controller, 'registerTinymcePopups']);
        add_action('widgets_init', [$this->controller, 'registerWidgets']);
        add_action('site-reviews/addon/settings/'.$this->addon->slug, [$this->controller, 'renderSettings']);
    }

    /**
     * @return void
     */
    public function runIntegrations()
    {
        $dir = $this->addon->path('plugin/Integrations');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        $namespace = (new \ReflectionClass($this->addon))->getNamespaceName();
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                continue;
            }
            $basename = $namespace.'\Integrations\\'.$fileinfo->getBasename();
            $controller = $basename.'\Controller';
            $hooks = $basename.'\Hooks';
            if (class_exists($controller) && class_exists($hooks)) {
                glsr()->singleton($controller);
                glsr()->singleton($hooks);
                glsr($hooks)->run();
            }
        }
    }

    /**
     * @return mixed
     */
    abstract protected function addon();

    /**
     * @return mixed
     */
    abstract protected function controller();
}
