<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Controllers\UpdateController;

class UpdateHooks extends AbstractHooks
{
    public function levelInit(): ?int
    {
        return 10;
    }

    /**
     * @action init:10
     */
    public function onInit(): void
    {
        $addons = glsr()->retrieveAs('array', 'licensed', []);
        foreach ($addons as $addonId => $addon) {
            $this->hook(UpdateController::class, [
                ['renderPluginUpdateMessage', "in_plugin_update_message-{$addonId}/{$addonId}.php", 10, 2],
            ]);
        }
    }

    public function run(): void
    {
        $this->hook(UpdateController::class, [
            ['filterAutoUpdateEmail', 'auto_plugin_theme_update_email', 10, 4],
            ['filterPluginsApi', 'plugins_api', 10, 3],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins', 50],
            // ['onDeleteUpdatePluginsTransient', 'delete_site_transient_update_plugins'],
            // ['onUpgraderProcessComplete', 'upgrader_process_complete'],
        ]);
        // WordPress names the hook after the host in the addon's Update URI header
        $hosts = array_unique(array_map(fn ($url) => (string) wp_parse_url($url, \PHP_URL_HOST), [Updater::BASE_URL, Updater::baseUrl()]));
        foreach ($hosts as $host) {
            $this->hook(UpdateController::class, [
                ['filterUpdatePlugins', "update_plugins_{$host}", 10, 2],
            ]);
        }
    }
}
