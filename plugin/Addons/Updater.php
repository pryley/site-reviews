<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Defaults\Updater\ActivateLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\DeactivateLicenseDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionDetailsDefaults;
use GeminiLabs\SiteReviews\Defaults\Updater\VersionUpdateDefaults;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Updater
{
    public const DEFAULT_API_URL = 'https://niftyplugins.com';

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

    public function checkLicense(): array
    {
        $this->flushCachedVersion();
        $results = $this->request('check_license');
        return glsr(CheckLicenseDefaults::class)->restrict($results);
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
            'url' => Url::home(),
        ];
        $response = glsr(Api::class, ['url' => $this->apiUrl])->post('/', [
            'body' => $body,
            'force' => $this->force,
            'timeout' => 15,
            'transient_key' => $action,
        ]);
        if ($response->failed()) {
            glsr_log()->error($response);
        } elseif (str_ends_with($action, '_license') && false === ($response->body['success'] ?? false)) {
            glsr_log()->debug($body);
        }
        return $response->body();
    }

    protected function updateUri(string $addonId, string $url = ''): string
    {
        if (empty($url)) {
            $plugins = get_plugins();
            $plugins = array_filter($plugins, fn ($plugin) => str_contains($plugin, "/{$addonId}.php"), \ARRAY_FILTER_USE_KEY);
            $plugin = array_shift($plugins) ?? [];
            $url = ($plugin['UpdateURI'] ?? '') ?: static::DEFAULT_API_URL;
        }
        return glsr(Sanitizer::class)->sanitizeUrl($url);
    }
}
