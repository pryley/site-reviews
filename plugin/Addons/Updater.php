<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;

class Updater
{
    /**
     * @var string
     */
    protected $addonId;

    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var bool
     */
    protected $forceCheck = false;
    /**
     * @var bool
     */
    protected $isReady = false;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var string
     */
    protected $plugin;

    /**
     * @param string $apiUrl
     * @param string $file
     * @param string $addonId
     */
    public function __construct($apiUrl, $file, $addonId, array $data = [])
    {
        if (!file_exists($file)) {
            return;
        }
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.WPINC.'/plugin.php';
        }
        $this->addonId = $addonId;
        $this->apiUrl = trailingslashit(glsr()->filterString('addon/api-url', $apiUrl));
        if ($this->apiUrl === Url::home()) {
            return;
        }
        $this->data = wp_parse_args($data, get_plugin_data($file));
        $this->plugin = plugin_basename($file);
        $this->isReady = true;
        if (glsr()->filterBool('updater/force-check', false)
            || !glsr()->addon(Arr::get($this->data, 'TextDomain'))) {
            $this->forceCheck = true; // don't cache the version details if the addon is not fully active
        }
    }

    /**
     * @return object
     */
    public function activateLicense(array $data = [])
    {
        return $this->request('activate_license', $data);
    }

    /**
     * @return object
     */
    public function checkLicense(array $data = [])
    {
        return $this->request('check_license', $data);
    }

    /**
     * @return object
     */
    public function deactivateLicense(array $data = [])
    {
        return $this->request('deactivate_license', $data);
    }

    /**
     * @param false|object|array $result
     * @param string $action
     * @param object $args
     * @return mixed
     * @filter plugins_api
     */
    public function filterPluginUpdateDetails($result, $action, $args)
    {
        if ('plugin_information' != $action
            || Arr::get($this->data, 'TextDomain') != Arr::get($args, 'slug')) {
            return $result;
        }
        if ($updateInfo = $this->getPluginUpdate($this->forceCheck)) {
            return $this->modifyUpdateDetails($updateInfo);
        }
        return $result;
    }

    /**
     * @param object $transient
     * @return object
     * @filter pre_set_site_transient_update_plugins
     */
    public function filterPluginUpdates($transient)
    {
        if ($updateInfo = $this->getPluginUpdate($this->forceCheck)) {
            return $this->modifyPluginUpdates($transient, $updateInfo);
        }
        return $transient;
    }

    /**
     * @return object
     */
    public function getLatestVersion(array $data = [])
    {
        return $this->request('get_version', $data);
    }

    /**
     * @return void
     */
    public function init()
    {
        if ($this->isReady) {
            add_filter('plugins_api', [$this, 'filterPluginUpdateDetails'], 10, 3);
            add_filter('pre_set_site_transient_update_plugins', [$this, 'filterPluginUpdates'], 999);
            add_action('load-update-core.php', [$this, 'onForceUpdateCheck'], 9);
            add_action('in_plugin_update_message-'.$this->plugin, [$this, 'renderLicenseMissingLink']);
        }
    }

    /**
     * @return bool
     */
    public function isLicenseValid()
    {
        if (empty($this->status)) {
            $result = $this->checkLicense();
            $this->status = Arr::get($result, 'license');
            update_option(glsr()->prefix.$this->addonId, $this->status); // store the license status
        }
        return 'valid' === $this->status;
    }

    /**
     * @return void
     * @action load-update-core.php
     */
    public function onForceUpdateCheck()
    {
        if (!filter_input(INPUT_GET, 'force-check')) {
            return;
        }
        try {
            $this->getPluginUpdate(true);
        } catch (\Exception $e) {
            glsr_log()->error($e->getMessage());
        }
    }

    /**
     * @return void
     * @action in_plugin_update_message-{$this->plugin}
     */
    public function renderLicenseMissingLink()
    {
        if (!$this->isLicenseValid()) {
            glsr()->render('partials/addons/license-missing');
        }
    }

    /**
     * @return false|object
     */
    protected function getCachedVersion()
    {
        return get_transient($this->getTransientName());
    }

    /**
     * @param bool $force
     * @return false|object
     */
    protected function getPluginUpdate($force = false)
    {
        $version = $this->getCachedVersion();
        if (false === $version || false !== $force) {
            $version = $this->getLatestVersion();
            $this->setCachedVersion($version);
        }
        if (isset($version->error)) {
            glsr_log()->error($version->error);
            return false;
        }
        return $version;
    }

    /**
     * @return string
     */
    protected function getTransientName()
    {
        return glsr()->prefix.md5(Arr::get($this->data, 'TextDomain'));
    }

    /**
     * @param object $transient
     * @param object $updateInfo
     * @return object
     */
    protected function modifyPluginUpdates($transient, $updateInfo)
    {
        $updateInfo->id = glsr()->id.'/'.Arr::get($this->data, 'TextDomain');
        $updateInfo->plugin = $this->plugin;
        // $updateInfo->requires_php = Arr::get($this->data, 'RequiresPHP');
        // $updateInfo->tested = Arr::get($this->data, 'testedTo');
        unset($updateInfo->upgrade_notice); // @todo for some reason, this is returned as an array
        $transient->checked[$this->plugin] = Arr::get($this->data, 'Version');
        $transient->last_checked = time();
        if (Helper::isGreaterThan($updateInfo->new_version, Arr::get($this->data, 'Version'))) {
            unset($transient->no_update[$this->plugin]);
            $updateInfo->update = true;
            if (!$this->isLicenseValid()) {
                $updateInfo->upgrade_notice = _x('A valid license key is required to download this update.', 'admin-text', 'site-reviews');
            }
            $transient->response[$this->plugin] = $updateInfo;
        } else {
            unset($transient->response[$this->plugin]);
            $transient->no_update[$this->plugin] = $updateInfo;
        }
        return $transient;
    }

    /**
     * @param object $updateInfo
     * @return object
     */
    protected function modifyUpdateDetails($updateInfo)
    {
        $updateInfo->author = Arr::get($this->data, 'Author');
        $updateInfo->author_profile = Arr::get($this->data, 'AuthorURI');
        // $updateInfo->requires = Arr::get($this->data, 'RequiresWP');
        // $updateInfo->requires_php = Arr::get($this->data, 'RequiresPHP');
        // $updateInfo->tested = Arr::get($this->data, 'testedTo');
        $updateInfo->version = $updateInfo->new_version;
        unset($updateInfo->contributors); // @todo for some reason, this is not being parsed as an array
        return $updateInfo;
    }

    /**
     * @param \WP_Error|array $response
     * @return object
     */
    protected function normalizeResponse($response)
    {
        $body = wp_remote_retrieve_body($response);
        if ($data = json_decode($body)) {
            $data = array_map('maybe_unserialize', (array) $data);
            return (object) $data;
        }
        $error = is_wp_error($response)
            ? $response->get_error_message()
            : 'Update server not responding ('.Arr::get($this->data, 'TextDomain').')';
        return (object) ['error' => $error];
    }

    /**
     * @param string $action activate_license|check_license|deactivate_license|get_version
     * @return object
     */
    protected function request($action, array $data = [])
    {
        $data = wp_parse_args($data, $this->data);
        $body = [
            'edd_action' => $action,
            'item_id' => '', // we don't have access to the download ID which is why this is empty
            'item_name' => Arr::get($data, 'TextDomain'), // we are using the slug for the name
            'license' => Arr::get($data, 'license'),
            'slug' => Arr::get($data, 'TextDomain'),
            'url' => Url::home(),
        ];
        $response = wp_remote_post($this->apiUrl, [
            'body' => $body,
            'timeout' => 15,
        ]);
        if ('check_license' === $action) {
            glsr_log()->debug($body);
        }
        return $this->normalizeResponse($response);
    }

    /**
     * @param object $version
     * @return void
     */
    protected function setCachedVersion($version)
    {
        if (!isset($version->error)) {
            set_transient($this->getTransientName(), $version, 15 * MINUTE_IN_SECONDS);
        }
    }
}
