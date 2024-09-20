<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\UpdateController;

class UpdateHooks extends AbstractHooks
{
    public function hasInit(): bool
    {
        return true;
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
            ['filterPluginsApi', 'plugins_api', 10, 3],
            ['filterUpdatePlugins', 'update_plugins_niftyplugins.com', 10, 2],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins', 50],
            // ['onDeleteUpdatePluginsTransient', 'delete_site_transient_update_plugins'],
            // ['onUpgraderProcessComplete', 'upgrader_process_complete'],
        ]);
    }
}
