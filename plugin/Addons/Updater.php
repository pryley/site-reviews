<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Defaults\Updater\ActivateLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\DeactivateLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionDetailsDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionUpdateDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Updater
{
    public const BASE_URL = 'https://niftyplugins.com';

    public string $addonId;
    public string $apiUrl;
    public bool $force;
    public string $license;

    public function __construct(string $addonId, array $args = [])
    {
        $args = wp_parse_args($args, [
            'force' => true,
            'license' => '',
            'url' => '',
        ]);
        if (empty($args['license'])) {
            $args['license'] = glsr_get_option("licenses.{$addonId}");
        }
        $this->addonId = $addonId;
        $this->apiUrl = $this->updateUri($addonId, $args['url']);
        $this->force = wp_validate_boolean($args['force']);
        $this->license = $args['license'];
    }

    public function activateLicense(): array
    {
        $this->flushCachedVersion();
        $results = $this->request('activate_license');
        return glsr(ActivateLicenseDefaults::class)->restrict($results);
    }

    public static function baseUrl(): string
    {
        $url = glsr()->filterString('updater/base-url', static::BASE_URL);
        return untrailingslashit(glsr(Sanitizer::class)->sanitizeUrl($url)) ?: static::BASE_URL;
    }

    public function checkLicense(): array
    {
        $this->flushCachedVersion();
        $results = $this->request('check_license');
        $check = glsr(CheckLicenseDefaults::class)->restrict($results);
        if (empty($results)) {
            $check['license'] = 'unknown'; // no answer is not an invalid license
        }
        return $check;
    }

    public function deactivateLicense(): array
    {
        $this->flushCachedVersion();
        $results = $this->request('deactivate_license');
        return glsr(DeactivateLicenseDefaults::class)->restrict($results);
    }

    public function flushCachedVersion(): void
    {
        glsr(Api::class, ['url' => $this->apiUrl])->flushAll('get_version');
    }

    /**
     * An installed plugin whose Update URI header names the licence server.
     */
    public static function isAddon(string $slug): bool
    {
        static $cache = [];
        if (isset($cache[$slug])) {
            return $cache[$slug];
        }
        $cache[$slug] = false;
        if (!preg_match('/^'.glsr()->id.'-[a-z-]+$/D', $slug)) {
            return $cache[$slug];
        }
        $file = \WP_PLUGIN_DIR."/{$slug}/{$slug}.php";
        if (is_readable($file)) {
            $data = get_file_data($file, ['update_uri' => 'Update URI']);
            $cache[$slug] = trailingslashit(static::BASE_URL) === trailingslashit($data['update_uri']);
        }
        return $cache[$slug];
    }

    /**
     * An update object the plugin built names the licence server as its id:
     * WordPress copies the Update URI header there, and the transient filter sets it.
     */
    public static function isAddonUpdate(object $update): bool
    {
        $id = (string) ($update->id ?? '');
        return '' !== $id && trailingslashit($id) === trailingslashit(static::BASE_URL);
    }

    /**
     * Whether the licence server should be asked again for this addon, on the schedule
     * WordPress keeps for wordpress.org in wp_update_plugins(). A yes is recorded as the asking.
     */
    public static function isDue(string $addonId): bool
    {
        $optionKey = glsr()->prefix.'last_checked_'.$addonId;
        $lastChecked = Cast::toInt(get_site_option($optionKey, 0));
        if (static::timeout() > time() - $lastChecked) {
            return false;
        }
        update_site_option($optionKey, time());
        return true;
    }

    public function version(): array
    {
        $results = $this->request('get_version');
        return glsr(VersionDefaults::class)->restrict($results);
    }

    public function versionDetails(): array
    {
        $results = $this->request('get_version');
        return glsr(VersionDetailsDefaults::class)->restrict($results);
    }

    public function versionUpdate(): array
    {
        $results = $this->request('get_version');
        return glsr(VersionUpdateDefaults::class)->restrict($results);
    }

    /**
     * @param string $action activate_license|check_license|deactivate_license|get_version
     */
    protected function request(string $action): array
    {
        $body = [
            'edd_action' => $action,
            'item_id' => '', // we don't have access to the download ID which is why this is empty
            'item_name' => $this->addonId,
            'license' => $this->license,
            'slug' => $this->addonId,
            'url' => Url::license(),
        ];
        $response = glsr(Api::class, ['url' => $this->apiUrl])->post('/', [
            'body' => $body,
            'force' => $this->force,
            'max_retries' => 1,
            'skip_when_down' => true,
            'timeout' => 15,
            'transient_key' => $action,
        ]);
        if ($response->failed()) {
            if ($response->code > 0) {
                glsr_log()->error($response);
            }
            return [];
        }
        if (str_ends_with($action, '_license') && false === ($response->body['success'] ?? false)) {
            glsr_log()->debug($body);
        }
        return $response->body();
    }

    protected static function timeout(): int
    {
        if (doing_filter('load-update-core.php') && filter_input(\INPUT_GET, 'force-check', \FILTER_VALIDATE_INT)) {
            return 0;
        }
        $timeouts = [
            'upgrader_process_complete' => 0,
            'load-update-core.php' => \MINUTE_IN_SECONDS,
            'load-plugins.php' => \HOUR_IN_SECONDS,
            'load-update.php' => \HOUR_IN_SECONDS,
        ];
        foreach ($timeouts as $hook => $timeout) {
            if (doing_filter($hook)) {
                return $timeout;
            }
        }
        return wp_doing_cron() ? 2 * \HOUR_IN_SECONDS : 12 * \HOUR_IN_SECONDS;
    }

    protected function updateUri(string $addonId, string $url = ''): string
    {
        if (empty($url)) {
            $plugins = get_plugins();
            $plugins = array_filter($plugins, fn ($plugin) => str_contains($plugin, "/{$addonId}.php"), \ARRAY_FILTER_USE_KEY);
            $plugin = array_shift($plugins) ?? [];
            $url = ($plugin['UpdateURI'] ?? '') ?: static::BASE_URL;
        }
        if (trailingslashit($url) === trailingslashit(static::BASE_URL)) {
            return static::baseUrl();
        }
        return glsr(Sanitizer::class)->sanitizeUrl($url);
    }
}
