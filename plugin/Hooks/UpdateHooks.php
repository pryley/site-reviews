<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\UpdateController;
use GeminiLabs\SiteReviews\Modules\UpdateLock;

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
        $locked = array_keys(glsr(UpdateLock::class)->locked());
        foreach ($locked as $basename) {
            $this->hook(UpdateController::class, [
                ['renderLockedPluginRow', "after_plugin_row_{$basename}", 10, 2],
            ]);
        }
        if (!empty($locked)) {
            $this->hook(UpdateController::class, [
                ['enqueueLockedRowStyle', 'admin_enqueue_scripts'],
            ]);
        }
    }

    public function run(): void
    {
        $this->hook(UpdateController::class, [
            ['filterAutoUpdateEmail', 'auto_plugin_theme_update_email', 10, 4],
            ['filterLockedPluginUpdates', 'site_transient_update_plugins', 60],
            ['filterLockedPreInstall', 'upgrader_pre_install', 9, 2],
            ['filterLockedSourceSelection', 'upgrader_source_selection', 10, 4],
            ['filterPluginsApi', 'plugins_api', 10, 3],
            ['filterUpdatePlugins', 'update_plugins_niftyplugins.com', 10, 2],
            ['filterUpdatePluginsTransient', 'site_transient_update_plugins', 50],
            // ['onDeleteUpdatePluginsTransient', 'delete_site_transient_update_plugins'],
            // ['onUpgraderProcessComplete', 'upgrader_process_complete'],
        ]);
    }
}
