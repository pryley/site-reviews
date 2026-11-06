<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Licensing\Defaults\ActivateLicenseDefaults;
use GeminiLabs\SiteReviews\Licensing\Defaults\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Licensing\Defaults\DeactivateLicenseDefaults;
use GeminiLabs\SiteReviews\Licensing\Defaults\VersionDefaults;
use GeminiLabs\SiteReviews\Licensing\Defaults\VersionDetailsDefaults;
use GeminiLabs\SiteReviews\Licensing\Defaults\VersionUpdateDefaults;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * The licence server: one request per action about an addon's key, at the address the addon's Update URI header names.
 */
class LicenseServer
{
    public const BASE_URL = 'https://niftyplugins.com';

    /** @var array<string, string> addon id => the address its Update URI header names, unsanitised */
    protected array $urls = [];

    public function activate(string $addonId, string $license): array
    {
        $this->flushVersion($addonId);
        $results = $this->request('activate_license', $addonId, $license);
        return glsr(ActivateLicenseDefaults::class)->restrict($results);
    }

    /**
     * The URL a licence is activated for.
     */
    public function activationUrl(): string
    {
        if (!is_multisite()) {
            return Url::home();
        }
        $network = strtolower((string) preg_replace('|^www\.|', '', (string) get_network()->domain));
        $site = strtolower((string) get_site()->domain);
        if ($site === $network || str_ends_with($site, '.'.$network)) {
            return Url::home();
        }
        return trailingslashit(home_url());
    }

    public static function baseUrl(): string
    {
        $url = glsr()->filterString('updater/base-url', static::BASE_URL);
        return untrailingslashit(glsr(Sanitizer::class)->sanitizeUrl($url)) ?: static::BASE_URL;
    }

    /**
     * Ask the server about a key, through the day's cache unless forced.
     */
    public function check(string $addonId, string $license, bool $force = false): array
    {
        $this->flushVersion($addonId);
        $results = $this->request('check_license', $addonId, $license, $force);
        $check = glsr(CheckLicenseDefaults::class)->restrict($results);
        if (empty($results)) {
            $check['license'] = 'unknown'; // no answer is not an invalid license
        }
        return $check;
    }

    public function deactivate(string $addonId, string $license): array
    {
        $this->flushVersion($addonId);
        $results = $this->request('deactivate_license', $addonId, $license);
        return glsr(DeactivateLicenseDefaults::class)->restrict($results);
    }

    /**
     * Forget the cached check and version answers for an addon.
     */
    public function flush(string $addonId): void
    {
        glsr(Api::class, ['url' => $this->url($addonId)])->flushAll('check_license');
        $this->flushVersion($addonId);
    }

    /**
     * Whether the server should be asked again about this addon's version.
     */
    public function isDue(string $addonId): bool
    {
        $optionKey = glsr()->prefix.'last_checked_'.$addonId;
        $lastChecked = Cast::toInt(get_site_option($optionKey, 0));
        if ($this->timeout() > time() - $lastChecked) {
            return false;
        }
        update_site_option($optionKey, time());
        return true;
    }

    /**
     * The server address for an addon found in its Update URI header.
     */
    public function url(string $addonId, string $url = ''): string
    {
        if ('' !== $url) {
            return $this->sanitizeUrl($url);
        }
        if (!isset($this->urls[$addonId])) {
            $plugins = get_plugins();
            $plugins = array_filter($plugins, fn ($plugin) => str_contains($plugin, "/{$addonId}.php"), \ARRAY_FILTER_USE_KEY);
            $plugin = array_shift($plugins) ?? [];
            $this->urls[$addonId] = ($plugin['UpdateURI'] ?? '') ?: static::BASE_URL;
        }
        return $this->sanitizeUrl($this->urls[$addonId]); // sanitised on every read, because the base-url filter can change
    }

    /**
     * The version answer for the addons page.
     */
    public function version(string $addonId, bool $force = false): array
    {
        $results = $this->request('get_version', $addonId, $this->savedLicense($addonId), $force);
        return glsr(VersionDefaults::class)->restrict($results);
    }

    /**
     * The version answer shaped for WordPress's plugin-information modal.
     */
    public function versionDetails(string $addonId, bool $force = false): array
    {
        $results = $this->request('get_version', $addonId, $this->savedLicense($addonId), $force);
        return glsr(VersionDetailsDefaults::class)->restrict($results);
    }

    /**
     * The version answer shaped for WordPress's update transient.
     */
    public function versionUpdate(string $addonId, bool $force = false, string $url = ''): array
    {
        $results = $this->request('get_version', $addonId, $this->savedLicense($addonId), $force, $url);
        return glsr(VersionUpdateDefaults::class)->restrict($results);
    }

    /**
     * Flush the cached version answer.
     */
    protected function flushVersion(string $addonId): void
    {
        glsr(Api::class, ['url' => $this->url($addonId)])->flushAll('get_version');
    }

    /**
     * @param string $action activate_license|check_license|deactivate_license|get_version
     */
    protected function request(string $action, string $addonId, string $license, bool $force = true, string $url = ''): array
    {
        $body = [
            'edd_action' => $action,
            'item_id' => '', // unused
            'item_name' => $addonId,
            'license' => $license,
            'slug' => $addonId,
            'url' => $this->activationUrl(),
        ];
        $response = glsr(Api::class, ['url' => $this->url($addonId, $url)])->post('/', [
            'body' => $body,
            'force' => $force,
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

    protected function sanitizeUrl(string $url): string
    {
        if (trailingslashit($url) === trailingslashit(static::BASE_URL)) {
            return static::baseUrl();
        }
        return glsr(Sanitizer::class)->sanitizeUrl($url);
    }

    protected function savedLicense(string $addonId): string
    {
        return glsr_get_option("licenses.{$addonId}", '', 'string');
    }

    protected function timeout(): int
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
}
