<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\UpdateNotice;
use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Modules\UpdateLock;

class UpdateController extends AbstractController
{
    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueLockedRowStyle(string $hook): void
    {
        if ('plugins.php' !== $hook) {
            return;
        }
        wp_add_inline_style('list-tables',
            '.plugins tr:has(+ .plugin-locked-tr),.plugins tr:has(+ .plugin-locked-tr) > *{box-shadow:none}'.
            '@media screen and (max-width: 782px){#wpbody-content .wp-list-table.plugins .plugin-locked-tr td{padding-block:0}}'
        );
    }

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
     * Remove the update of a locked plugin.
     *
     * @param mixed $updates
     *
     * @return mixed
     *
     * @filter site_transient_update_plugins:60
     */
    public function filterLockedPluginUpdates($updates)
    {
        if (!is_object($updates)) {
            return $updates;
        }
        foreach (array_keys(glsr(UpdateLock::class)->locked()) as $basename) {
            unset($updates->response[$basename]);
        }
        return $updates;
    }

    /**
     * Refuse the update of a locked plugin.
     *
     * @param mixed $response
     *
     * @return mixed
     *
     * @filter upgrader_pre_install:9
     */
    public function filterLockedPreInstall($response, array $hookExtra)
    {
        if (is_wp_error($response)) {
            return $response;
        }
        $locked = glsr(UpdateLock::class)->locked();
        $basename = (string) ($hookExtra['plugin'] ?? '');
        if (array_key_exists($basename, $locked)) {
            return $this->lockedError($locked[$basename]);
        }
        return $response;
    }

    /**
     * Refuse an uploaded zip of a locked plugin.
     *
     * @param mixed $source
     * @param mixed $remoteSource
     * @param mixed $upgrader
     *
     * @return mixed
     *
     * @filter upgrader_source_selection
     */
    public function filterLockedSourceSelection($source, $remoteSource, $upgrader, array $hookExtra)
    {
        if (is_wp_error($source) || isset($hookExtra['plugin']) || 'plugin' !== ($hookExtra['type'] ?? '')) {
            return $source;
        }
        $folder = basename(untrailingslashit((string) $source));
        foreach (glsr(UpdateLock::class)->locked() as $basename => $name) {
            if (dirname($basename) === $folder) {
                return $this->lockedError($name);
            }
        }
        return $source;
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
     * @action after_plugin_row_{$basename}
     */
    public function renderLockedPluginRow(string $file, array $pluginData): void
    {
        $table = _get_list_table('WP_Plugins_List_Table', ['screen' => get_current_screen()]);
        $classname = is_plugin_active($file) ? 'active' : '';
        printf('<tr class="plugin-update-tr plugin-locked-tr %s"><td colspan="%s" class="plugin-update colspanchange">%s</td></tr>',
            $classname,
            esc_attr((string) $table->get_column_count()),
            wp_get_admin_notice(
                /* translators: %s: plugin name */
                sprintf(_x('Updates are turned off for %s because its folder is a git repository.', 'admin-text', 'site-reviews'), $pluginData['Name'] ?? dirname($file)),
                [
                    'type' => 'error',
                    'additional_classes' => ['notice-alt', 'inline', 'update-message'],
                ]
            )
        );
    }

    /**
     * @param array  $pluginData
     * @param object $response
     *
     * @action in_plugin_update_message-{$addonId}/{$addonId}.php
     */
    public function renderPluginUpdateMessage($pluginData, $response): void
    {
        if (!empty($response->package)) {
            return;
        }
        $notice = UpdateNotice::fromUpdate($response, (string) ($pluginData['PluginURI'] ?? ''));
        echo ' '.$notice->html();
    }

    protected function lockedError(string $name): \WP_Error
    {
        return new \WP_Error('glsr_git_repository', sprintf(
            /* translators: %s: plugin name */
            _x('%s was not updated because its folder is a git repository.', 'admin-text', 'site-reviews'),
            $name
        ));
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
        $notice = UpdateNotice::fromUpdate($item);
        $name = html_entity_decode((string) ($failed->name ?? $item->plugin));
        return sprintf('- %s: %s %s', $name, $notice->text(), $notice->url());
    }
}
