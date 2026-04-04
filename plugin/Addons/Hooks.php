<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

abstract class Hooks extends AbstractHooks
{
    abstract public function app(): PluginContract;

    public function runIntegrations(): void
    {
        $dir = $this->app()->path('plugin/Integrations');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        $namespace = (new \ReflectionClass($this->app()))->getNamespaceName();
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                continue;
            }
            try {
                $hooks = "{$namespace}\Integrations\\{$fileinfo->getBasename()}\Hooks";
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks);
                    add_action('plugins_loaded', fn () => glsr($hooks)->run(), 100); // run integrations late
                    glsr($hooks)->runDeferred();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    protected function baseHooks(array $hooks = []): array
    {
        $defaults = [
            ['enqueueAdminAssets', 'admin_enqueue_scripts'],
            ['enqueuePublicAssets', 'wp_enqueue_scripts'],
            ['filterActionLinks', "plugin_action_links_{$this->basename()}"],
            ['filterCapabilities', 'site-reviews/capabilities'],
            ['filterConfigPath', 'site-reviews/config'],
            ['filterDocumentation', 'site-reviews/addon/documentation'],
            ['filterFilePaths', 'site-reviews/path', 10, 2],
            ['filterGettext', "gettext_{$this->id()}", 10, 2],
            ['filterGettextWithContext', "gettext_with_context_{$this->id()}", 10, 3],
            ['filterLocalizedPublicVariables', 'site-reviews/enqueue/public/localize'],
            ['filterNgettext', "ngettext_{$this->id()}", 10, 4],
            ['filterNgettextWithContext', "ngettext_with_context_{$this->id()}", 10, 5],
            ['filterRenderView', "{$this->id()}/render/view"],
            ['filterRoles', 'site-reviews/roles'],
            ['filterRowMeta', 'plugin_row_meta', 10, 2],
            ['filterSettings', 'site-reviews/settings'],
            ['filterSubsubsub', 'site-reviews/addon/subsubsub'],
            ['filterTranslationEntries', 'site-reviews/translation/entries'],
            ['filterTranslatorDomains', 'site-reviews/translator/domains'],
            ['install', "{$this->id()}/activated"],
            ['onActivation', 'admin_init'],
            ['onDeactivation', "deactivate_{$this->basename()}"],
            ['registerLanguages', 'after_setup_theme'],
            ['registerShortcodes', 'init'],
            ['registerTinymcePopups', 'admin_init'],
            ['registerWidgets', 'widgets_init'],
            ['renderSettings', "site-reviews/settings/{$this->slug()}"],
        ];
        return array_merge($defaults, $hooks);
    }

    protected function basename(): string
    {
        return $this->app()->basename;
    }

    protected function id(): string
    {
        return $this->app()->id;
    }

    protected function postType(): string
    {
        return $this->app()->post_type;
    }

    protected function slug(): string
    {
        return $this->app()->slug;
    }

    protected function type(): string
    {
        return $this->postType(); // @compat
    }
}
