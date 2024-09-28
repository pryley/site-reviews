<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

abstract class Hooks extends AbstractHooks
{
    abstract public function app(): PluginContract;

    protected function baseHooks(array $hooks = []): array
    {
        $defaults = [
            ['enqueueAdminAssets', 'admin_enqueue_scripts'],
            ['enqueueBlockAssets', 'enqueue_block_editor_assets'],
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
            ['filterSettings', 'site-reviews/settings'],
            ['filterSubsubsub', 'site-reviews/addon/subsubsub'],
            ['filterTranslationEntries', 'site-reviews/translation/entries'],
            ['filterTranslatorDomains', 'site-reviews/translator/domains'],
            ['install', "{$this->id()}/activated"],
            ['onActivation', 'admin_init'],
            ['onDeactivation', "deactivate_{$this->basename()}"],
            ['registerBlocks', 'init'],
            ['registerLanguages', 'after_setup_theme'],
            ['registerShortcodes', 'init'],
            ['registerTinymcePopups', 'admin_init'],
            ['registerWidgets', 'widgets_init'],
            ['renderSettings', "site-reviews/settings/{$this->slug()}"],
            ['runIntegrations', 'plugins_loaded', 100],
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

    protected function slug(): string
    {
        return $this->app()->slug;
    }

    protected function type(): string
    {
        return $this->app()->post_type;
    }
}
