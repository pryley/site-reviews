<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Updater
{
    /**
     * @var string
     */
    protected $apiUrl;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var string
     */
    protected $plugin;
    /**
     * @var string
     */
    protected $transientName;

    /**
     * @param string $apiUrl
     * @param string $file
     */
    public function __construct($apiUrl, $file, array $data = [])
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.WPINC.'/plugin.php';
        }
        $this->apiUrl = trailingslashit(apply_filters('site-reviews/addon/api-url', $apiUrl));
        $this->data = wp_parse_args($data, get_plugin_data($file));
        $this->plugin = plugin_basename($file);
        $this->transientName = Application::PREFIX.md5(Arr::get($data, 'TextDomain'));
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
        $response = $this->request('check_license', $data);
        if ('valid' === Arr::get($response, 'license')) {
            $this->getPluginUpdate(true);
        }
        return $response;
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
     */
    public function filterPluginUpdateDetails($result, $action, $args)
    {
        if ('plugin_information' != $action
            || Arr::get($this->data, 'TextDomain') != Arr::get($args, 'slug')) {
            return $result;
        }
        if ($updateInfo = $this->getPluginUpdate()) {
            return $this->modifyUpdateDetails($updateInfo);
        }
        return $result;
    }

    /**
     * @param object $transient
     * @return object
     */
    public function filterPluginUpdates($transient)
    {
        if ($updateInfo = $this->getPluginUpdate()) {
            return $this->modifyPluginUpdates($transient, $updateInfo);
        }
        return $transient;
    }

    /**
     * @return object
     */
    public function getVersion(array $data = [])
    {
        return $this->request('get_version', $data);
    }

    /**
     * @return void
     */
    public function init()
    {
        if ($this->apiUrl === trailingslashit(home_url())) {
            return;
        }
        add_filter('plugins_api',                             [$this, 'filterPluginUpdateDetails'], 10, 3);
        add_filter('pre_set_site_transient_update_plugins',   [$this, 'filterPluginUpdates'], 999);
        add_action('load-update-core.php',                    [$this, 'onForceUpdateCheck'], 9);
        add_action('in_plugin_update_message-'.$this->plugin, [$this, 'renderLicenseMissingLink']);
    }

    /**
     * @return bool
     */
    public function isLicenseValid()
    {
        $result = $this->checkLicense();
        return 'valid' === Arr::get($result, 'license');
    }

    /**
     * @return void
     */
    public function onForceUpdateCheck()
    {
        if (!filter_input(INPUT_GET, 'force-check')) {
            return;
        }
        foreach (glsr()->addons as $addon) {
            try {
                glsr($addon)->updater->getPluginUpdate(true);
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    /**
     * @return void
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
        return get_transient($this->transientName);
    }

    /**
     * @param bool $force
     * @return false|object
     */
    protected function getPluginUpdate($force = false)
    {
        $version = $this->getCachedVersion();
        if (false === $version || $force) {
            $version = $this->getVersion();
            $this->setCachedVersion($version);
        }
        if (isset($version->error)) {
            glsr_log()->error($version->error);
            return false;
        }
        return $version;
    }

    /**
     * @param object $transient
     * @param object $updateInfo
     * @return object
     */
    protected function modifyPluginUpdates($transient, $updateInfo)
    {
        $updateInfo->id = Application::ID.'/'.Arr::get($this->data, 'TextDomain');
        $updateInfo->plugin = $this->plugin;
        $updateInfo->requires_php = Arr::get($this->data, 'RequiresPHP');
        $updateInfo->tested = Arr::get($this->data, 'testedTo');
        $transient->checked[$this->plugin] = Arr::get($this->data, 'Version');
        $transient->last_checked = time();
        if (Helper::isGreaterThan($updateInfo->new_version, Arr::get($this->data, 'Version'))) {
            unset($transient->no_update[$this->plugin]);
            $updateInfo->update = true;
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
        $updateInfo->requires = Arr::get($this->data, 'RequiresWP');
        $updateInfo->requires_php = Arr::get($this->data, 'RequiresPHP');
        $updateInfo->tested = Arr::get($this->data, 'testedTo');
        $updateInfo->version = $updateInfo->new_version;
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
        $response = wp_remote_post($this->apiUrl, [
            'body' => [
                'edd_action' => $action,
                'item_id' => Arr::get($data, 'item_id'),
                'item_name' => Arr::get($data, 'Name'),
                'license' => Arr::get($data, 'license'),
                'slug' => Arr::get($data, 'TextDomain'),
                'url' => home_url(),
            ],
            'sslverify' => apply_filters('site-reviews/sslverify/post', false),
            'timeout' => 15,
        ]);
        return $this->normalizeResponse($response);
    }

    /**
     * @param object $version
     * @return void
     */
    protected function setCachedVersion($version)
    {
        if (!isset($version->error)) {
            set_transient($this->transientName, $version, 3 * HOUR_IN_SECONDS);
        }
    }
}
