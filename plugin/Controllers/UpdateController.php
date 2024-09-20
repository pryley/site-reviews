<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class UpdateController extends AbstractController
{
    /**
     * Get the update information for the plugin modal.
     *
     * @param false|object|array $data
     * @param object             $args
     *
     * @return false|object|array
     *
     * @filter plugins_api
     */
    public function filterPluginsApi($data, string $action, $args)
    {
        if ('plugin_information' !== $action) {
            return $data;
        }
        static $licensedAddons;
        if (empty($licensedAddons)) {
            $licensedAddons = glsr()->retrieveAs('array', 'licensed', []);
        }
        $addonId = Arr::get($args, 'slug');
        if (!array_key_exists($addonId, $licensedAddons)) {
            return $data;
        }
        $updater = new Updater($addonId, [
            'force' => $this->hasTimeoutExpired($addonId),
        ]);
        $details = $updater->versionDetails();
        if (empty($details['version'])) {
            return $data;
        }
        return (object) $details;
    }

    /**
     * Get the update information for supported addons.
     *
     * @param array|false $pluginUpdate
     *
     * @return array|false
     *
     * @filter update_plugins_niftyplugins.com
     */
    public function filterUpdatePlugins($pluginUpdate, array $pluginData)
    {
        $addonId = $pluginData['TextDomain'] ?? '';
        $url = $pluginData['UpdateURI'] ?? '';
        $updater = new Updater($addonId, [
            'force' => $this->hasTimeoutExpired($addonId),
            'url' => $url,
        ]);
        $update = $updater->versionUpdate();
        if (!empty($update['version'])) {
            return $update;
        }
        return $pluginUpdate;
    }

    /**
     * Get the update information for unsupported addons.
     *
     * @param mixed $updates
     *
     * @return mixed
     *
     * @filter site_transient_update_plugins
     */
    public function filterUpdatePluginsTransient($updates)
    {
        if (empty($updates)) {
            return $updates;
        }
        $addons = glsr()->retrieveAs('array', 'compat', []);
        foreach ($addons as $addonId => $file) {
            $plugin = plugin_basename($file);
            $pluginData = get_file_data($file, ['version' => 'Version'], 'plugin');
            $currentVersion = $pluginData['version'];
            $updater = new Updater($addonId, [
                'force' => $this->hasTimeoutExpired($addonId),
                'url' => Updater::DEFAULT_API_URL,
            ]);
            $update = (object) $updater->versionUpdate();
            if (empty($update->version)) {
                continue;
            }
            $update->id = Updater::DEFAULT_API_URL;
            $update->plugin = $plugin;
            $update->new_version = $update->version;
            unset($updates->no_update[$plugin], $updates->response[$plugin]);
            if (version_compare($update->version, $currentVersion, '>')) {
                $updates->response[$plugin] = $update;
            } else {
                $updates->no_update[$plugin] = $update;
            }
            $updates->checked[$plugin] = $currentVersion;
        }
        return $updates;
    }

    /**
     * Delete API transient here.
     *
     * @action delete_site_transient_update_plugins
     */
    // public function onDeleteUpdatePluginsTransient(): void
    // {
    // }

    /**
     * @action upgrader_process_complete
     */
    // public function onUpgraderProcessComplete(): void
    // {
    // }

    /**
     * @param array  $pluginData
     * @param object $response
     *
     * @action after_in_plugin_update_message-plugin_row_{$addonId}/{$addonId}.php
     */
    public function renderPluginUpdateMessage($pluginData, $response): void
    {
        if (!empty($response->package)) {
            return;
        }
        $url = $pluginData['PluginURI'] ?? Updater::DEFAULT_API_URL;
        $message = _x('A valid <a href="%s">license key</a> is required to update this plugin.', 'admin-text', 'site-reviews');
        printf(" {$message}", $url);
    }

    protected function hasTimeoutExpired(string $addonId): bool
    {
        $optionKey = glsr()->prefix.'last_checked_'.$addonId;
        $lastChecked = Cast::toInt(get_site_option($optionKey, 0));
        if (doing_filter('upgrader_process_complete')) {
            $timeout = 0;
        } elseif (doing_filter('load-update-core.php')) {
            $timeout = MINUTE_IN_SECONDS;
        } elseif (doing_filter('load-plugins.php') || doing_filter('load-update.php')) {
            $timeout = HOUR_IN_SECONDS;
        } elseif (wp_doing_cron()) {
            $timeout = 2 * HOUR_IN_SECONDS;
        } else {
            $timeout = 12 * HOUR_IN_SECONDS;
        }
        if ($timeout < (time() - $lastChecked)) {
            update_site_option($optionKey, time());
            return true;
        }
        return false;
    }
}
