<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Cache
{
    public function delete(string $key, string $group): void
    {
        global $_wp_suspend_cache_invalidation;
        if (empty($_wp_suspend_cache_invalidation)) {
            $group = glsr()->prefix.$group;
            wp_cache_delete($key, $group);
        }
    }

    public function get(string $key, string $group, ?\Closure $callback = null, int $expire = 0)
    {
        $group = glsr()->prefix.$group;
        $value = wp_cache_get($key, $group);
        if (false === $value && $callback instanceof \Closure) {
            if ($value = $callback()) {
                wp_cache_add($key, $value, $group, $expire);
            }
        }
        return $value;
    }

    public function getPluginVersions(): array
    {
        $versions = get_transient(glsr()->prefix.'rollback_versions');
        if (!empty($versions)) {
            return Cast::toArray($versions);
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
            glsr_log()->error($response);
            return [];
        }
        $versions = Arr::consolidate(Arr::get($response, 'versions'));
        ksort($versions, \SORT_NATURAL);
        unset($versions['trunk']);
        $versions = array_keys(array_reverse($versions));
        $prevMajorVersion = Cast::toInt(glsr()->version('major')) - 1;
        $prevVersions = preg_grep("/^{$prevMajorVersion}\./", $versions);
        $prevVersion = array_shift($prevVersions);
        $index = array_search(glsr()->version, $versions);
        $startIndex = (false === $index) ? 0 : ++$index;
        $versions = array_slice($versions, $startIndex, 10);
        if (!in_array($prevVersion, $versions)) {
            $versions[] = $prevVersion;
        }
        set_transient(glsr()->prefix.'rollback_versions', $versions, HOUR_IN_SECONDS);
        return $versions;
    }

    public function getRemotePostTest(): string
    {
        if (false === ($test = get_transient(glsr()->prefix.'remote_post_test'))) {
            $response = wp_remote_post('https://api.wordpress.org/stats/php/1.0/');
            $test = !is_wp_error($response) && in_array($response['response']['code'], range(200, 299))
                ? 'Works'
                : 'Does not work';
            set_transient(glsr()->prefix.'remote_post_test', $test, WEEK_IN_SECONDS);
        }
        return Cast::toString($test);
    }

    public function getSystemInfo(): array
    {
        if (false === ($data = get_transient(glsr()->prefix.'system_info'))) {
            $callback = fn ($translation, $single) => $single;
            add_filter('gettext_default', $callback, 10, 2);
            try { // prevent badly made migration plugins from breaking Site Reviews...
                $data = \WP_Debug_Data::debug_data(); // get the WordPress debug data in English
                set_transient(glsr()->prefix.'system_info', $data, 12 * HOUR_IN_SECONDS);
            } catch (\TypeError $error) {
                $data = [];
            }
            remove_filter('gettext_default', $callback, 10);
        }
        return Arr::consolidate($data);
    }

    public function store(string $key, string $group, $value, int $expire = 0): void
    {
        $group = glsr()->prefix.$group;
        wp_cache_set($key, $value, $group, $expire);
    }
}
