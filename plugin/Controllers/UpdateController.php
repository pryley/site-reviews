<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\UpdateNotice;
use GeminiLabs\SiteReviews\Addons\Updater;

class UpdateController extends AbstractController
{
    /**
     * Say why an addon did not update when its update carried no package.
     *
     * @param mixed $email
     *
     * @return mixed
     *
     * @filter auto_plugin_theme_update_email
     */
    public function filterAutoUpdateEmail($email, string $type, array $successfulUpdates, array $failedUpdates)
    {
        if (!is_array($email) || !in_array($type, ['fail', 'mixed'], true)) {
            return $email;
        }
        $lines = array_filter(array_map(
            fn ($failed) => $this->licenseFailureLine($failed),
            $failedUpdates['plugin'] ?? []
        ));
        if (empty($lines)) {
            return $email;
        }
        $heading = _x('The following Site Reviews addons did not update because their license does not allow it:', 'admin-text', 'site-reviews');
        $body = rtrim((string) ($email['body'] ?? ''));
        $email['body'] = $body."\n\n".$heading."\n".implode("\n", $lines)."\n";
        return $email;
    }

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
        if ('plugin_information' !== $action || empty($args->slug)) {
            return $data;
        }
        if (!Updater::isAddon($args->slug)) {
            return $data;
        }
        $updater = new Updater($args->slug, [
            'force' => Updater::isDue($args->slug),
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
     * @filter update_plugins_<host>
     */
    public function filterUpdatePlugins($pluginUpdate, array $pluginData)
    {
        $addonId = $pluginData['TextDomain'] ?? '';
        $url = $pluginData['UpdateURI'] ?? '';
        $updater = new Updater($addonId, [
            'force' => Updater::isDue($addonId),
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
                'force' => Updater::isDue($addonId),
                'url' => Updater::BASE_URL,
            ]);
            $update = (object) $updater->versionUpdate();
            if (empty($update->version)) {
                continue;
            }
            $update->id = Updater::BASE_URL;
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
        $notice = $this->updateNotice($response, (string) ($pluginData['PluginURI'] ?? ''));
        echo ' '.$notice->html();
    }

    /**
     * The email line for a failed addon update that had no package; '' for any other failure.
     *
     * @param mixed $failed
     */
    protected function licenseFailureLine($failed): string
    {
        $item = $failed->item ?? null;
        if (!is_object($item) || !empty($item->package) || !Updater::isAddonUpdate($item)) {
            return '';
        }
        $notice = $this->updateNotice($item);
        $name = html_entity_decode((string) ($failed->name ?? $item->plugin));
        return sprintf('- %s: %s %s', $name, $notice->text(), $notice->url());
    }

    /**
     * The notice for an update object that has no package.
     *
     * @param mixed $update
     */
    protected function updateNotice($update, string $pluginUrl = ''): UpdateNotice
    {
        return new UpdateNotice(
            (string) ($update->license_status ?? ''),
            (string) ($update->license_renewal_url ?? ''),
            $pluginUrl
        );
    }
}
