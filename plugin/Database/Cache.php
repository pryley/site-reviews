<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Cache
{
    /**
     * @param string $key
     * @param string $group
     * @return void
     */
    public function delete($key, $group)
    {
        global $_wp_suspend_cache_invalidation;
        if (empty($_wp_suspend_cache_invalidation)) {
            $group = glsr()->prefix.$group;
            wp_cache_delete($key, $group);
        }
    }

    /**
     * @param string $key
     * @param string $group
     * @param \Closure|null $callback
     * @return mixed
     */
    public function get($key, $group, $callback = null)
    {
        $group = glsr()->prefix.$group;
        $value = wp_cache_get($key, $group);
        if (false === $value && $callback instanceof \Closure) {
            if ($value = $callback()) {
                wp_cache_add($key, $value, $group);
            }
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getCloudflareIps()
    {
        if (false !== ($ipAddresses = get_transient(glsr()->prefix.'cloudflare_ips'))) {
            return $ipAddresses;
        }
        $ipAddresses = array_fill_keys(['v4', 'v6'], []);
        foreach (array_keys($ipAddresses) as $version) {
            $url = 'https://www.cloudflare.com/ips-'.$version;
            $response = wp_remote_get($url, ['sslverify' => false]);
            if (is_wp_error($response)) {
                glsr_log()->error($response->get_error_message());
                continue;
            }
            if ('200' != ($statusCode = wp_remote_retrieve_response_code($response))) {
                glsr_log()->error(sprintf('Unable to connect to %s [%s]', $url, $statusCode));
                continue;
            }
            $ipAddresses[$version] = array_filter(
                (array) preg_split('/\R/', wp_remote_retrieve_body($response))
            );
        }
        set_transient(glsr()->prefix.'cloudflare_ips', $ipAddresses, WEEK_IN_SECONDS);
        return $ipAddresses;
    }

    /**
     * @return array
     */
    public function getPluginVersions()
    {
        $versions = get_transient(glsr()->prefix.'rollback_versions');
        if (!empty($versions)) {
            return $versions;
        }
        include_once ABSPATH.'wp-admin/includes/plugin-install.php';
        $response = plugins_api('plugin_information', [
            'slug' => glsr()->id,
            'fields' => [
                'active_installs' => false,
                'added' => false,
                'banners' => false,
                'contributors' => false,
                'donate_link' => false,
                'downloadlink' => false,
                'homepage' => false,
                'rating' => false,
                'ratings' => false,
                'screenshots' => false,
                'sections' => false,
                'tags' => false,
                'versions' => true,
            ],
        ]);
        if (is_wp_error($response)) {
            return [];
        }
        $versions = array_keys(Arr::consolidate(Arr::get($response, 'versions')));
        $versions = array_filter($versions, function ($version) {
            $minorVersion = (float) glsr()->version('minor');
            $versionLimit = sprintf('%.2f', ($minorVersion - (3 / 100)));
            $maxLimit = version_compare($version, glsr()->version, '<');
            $minLimit = version_compare($version, $versionLimit, '>=');
            return $maxLimit && $minLimit;
        });
        natsort($versions);
        $versions = array_reverse($versions);
        set_transient(glsr()->prefix.'rollback_versions', $versions, HOUR_IN_SECONDS);
        return $versions;
    }

    /**
     * @return string
     */
    public function getRemotePostTest()
    {
        if (false === ($test = get_transient(glsr()->prefix.'remote_post_test'))) {
            $response = wp_remote_post('https://api.wordpress.org/stats/php/1.0/');
            $test = !is_wp_error($response) && in_array($response['response']['code'], range(200, 299))
                ? 'Works'
                : 'Does not work';
            set_transient(glsr()->prefix.'remote_post_test', $test, WEEK_IN_SECONDS);
        }
        return $test;
    }

    /**
     * @return array
     */
    public function getSystemInfo()
    {
        if (false === ($data = get_transient(glsr()->prefix.'system_info'))) {
            add_filter('gettext_default', [glsr(TranslationController::class), 'filterEnglishTranslation'], 10, 2);
            $data = \WP_Debug_Data::debug_data(); // get the WordPress debug data in English
            remove_filter('gettext_default', [glsr(TranslationController::class), 'filterEnglishTranslation'], 10);
            set_transient(glsr()->prefix.'system_info', $data, 12 * HOUR_IN_SECONDS);
        }
        return $data;
    }

    /**
     * @param string $key
     * @param string $group
     * @return mixed
     */
    public function store($key, $group, $value)
    {
        $group = glsr()->prefix.$group;
        wp_cache_add($key, $value, $group);
        return $value;
    }
}
