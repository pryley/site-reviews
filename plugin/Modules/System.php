<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\Sinergi\BrowserDetector\Browser;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class System
{
    const PAD = 40;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return string
     */
    public function get()
    {
        $details = [
            'plugin' => 'Plugin Details',
            'addon' => 'Addon Details',
            'browser' => 'Browser Details',
            'server' => 'Server Details',
            'php' => 'PHP Configuration',
            'wordpress' => 'WordPress Configuration',
            'mu-plugin' => 'Must-Use Plugins',
            'multisite-plugin' => 'Network Active Plugins',
            'active-plugin' => 'Active Plugins',
            'inactive-plugin' => 'Inactive Plugins',
            'setting' => 'Plugin Settings',
            'reviews' => 'Review Counts',
        ];
        $systemInfo = array_reduce(array_keys($details), function ($carry, $key) use ($details) {
            $methodName = Helper::buildMethodName('get-'.$key.'-details');
            if (method_exists($this, $methodName) && $systemDetails = $this->$methodName()) {
                return $carry.$this->implode(
                    strtoupper($details[$key]),
                    glsr()->filterArray('system/'.$key, $systemDetails)
                );
            }
            return $carry;
        });
        return trim($systemInfo);
    }

    /**
     * @return array
     */
    public function getActivePluginDetails()
    {
        $plugins = get_plugins();
        $activePlugins = glsr(OptionManager::class)->getWP('active_plugins', [], 'array');
        $inactive = array_diff_key($plugins, array_flip($activePlugins));
        return $this->normalizePluginList(array_diff_key($plugins, $inactive));
    }

    /**
     * @return array
     */
    public function getAddonDetails()
    {
        $details = glsr()->filterArray('addon/system-info', []);
        ksort($details);
        return $details;
    }

    /**
     * @return array
     */
    public function getBrowserDetails()
    {
        $browser = new Browser();
        $name = esc_attr($browser->getName());
        $userAgent = esc_attr($browser->getUserAgent()->getUserAgentString());
        $version = esc_attr($browser->getVersion());
        return [
            'Browser Name' => sprintf('%s %s', $name, $version),
            'Browser UA' => $userAgent,
        ];
    }

    /**
     * @return array
     */
    public function getInactivePluginDetails()
    {
        $activePlugins = glsr(OptionManager::class)->getWP('active_plugins', [], 'array');
        $inactivePlugins = $this->normalizePluginList(
            array_diff_key(get_plugins(), array_flip($activePlugins))
        );
        $multisitePlugins = $this->getMultisitePluginDetails();
        return Helper::ifTrue(empty($multisitePlugins),
            $inactivePlugins,
            array_diff($inactivePlugins, $multisitePlugins)
        );
    }

    /**
     * @return array
     */
    public function getMuPluginDetails()
    {
        $plugins = get_mu_plugins();
        return Helper::ifTrue(empty($plugins), [], function () use ($plugins) {
            return $this->normalizePluginList($plugins);
        });
    }

    /**
     * @return array
     */
    public function getMultisitePluginDetails()
    {
        $activePlugins = (array) get_site_option('active_sitewide_plugins', []);
        if (!is_multisite() || empty($activePlugins)) {
            return [];
        }
        return $this->normalizePluginList(array_intersect_key(get_plugins(), $activePlugins));
    }

    /**
     * @return array
     */
    public function getPhpDetails()
    {
        $displayErrors = $this->getIni('display_errors', null);
        return [
            'cURL' => var_export(function_exists('curl_init'), true),
            'Default Charset' => $this->getIni('default_charset'),
            'Display Errors' => Helper::ifTrue(empty($displayErrors), 'N/A', 'On ('.$displayErrors.')'),
            'fsockopen' => var_export(function_exists('fsockopen'), true),
            'Intl' => Helper::ifTrue(extension_loaded('intl'), phpversion('intl'), 'false'),
            'IPv6' => var_export(defined('AF_INET6'), true),
            'Max Execution Time' => $this->getIni('max_execution_time'),
            'Max Input Nesting Level' => $this->getIni('max_input_nesting_level'),
            'Max Input Vars' => $this->getIni('max_input_vars'),
            'Memory Limit' => $this->getIni('memory_limit'),
            'Multibyte' => Helper::ifTrue(extension_loaded('mbstring'), phpversion('mbstring'), 'false'),
            'Post Max Size' => $this->getIni('post_max_size'),
            'Sendmail Path' => $this->getIni('sendmail_path'),
            'Session Cookie Path' => esc_html($this->getIni('session.cookie_path')),
            'Session Name' => esc_html($this->getIni('session.name')),
            'Session Save Path' => esc_html($this->getIni('session.save_path')),
            'Session Use Cookies' => var_export(wp_validate_boolean($this->getIni('session.use_cookies', false)), true),
            'Session Use Only Cookies' => var_export(wp_validate_boolean($this->getIni('session.use_only_cookies', false)), true),
            'Upload Max Filesize' => $this->getIni('upload_max_filesize'),
        ];
    }

    /**
     * @return array
     */
    public function getReviewsDetails()
    {
        $counts = glsr(Query::class)->ratings();
        array_walk($counts, function (&$ratings) use ($counts) {
            if (is_array($ratings)) {
                $ratings = array_sum($ratings).' ('.implode(', ', $ratings).')';
                return;
            }
            glsr_log()
                ->error('$ratings is not an array, possibly due to incorrectly imported reviews.')
                ->debug($ratings)
                ->debug($counts);
        });
        ksort($counts);
        return $counts;
    }

    /**
     * @return array
     */
    public function getServerDetails()
    {
        global $wpdb;
        return [
            'Host Name' => $this->getHostName(),
            'MySQL Version' => $wpdb->db_version(),
            'PHP Version' => PHP_VERSION,
            'Server Software' => filter_input(INPUT_SERVER, 'SERVER_SOFTWARE'),
        ];
    }

    /**
     * @return array
     */
    public function getSettingDetails()
    {
        $settings = glsr(OptionManager::class)->get('settings', []);
        $settings = Arr::flatten($settings, true);
        $settings = $this->purgeSensitiveData($settings);
        ksort($settings);
        $details = [];
        foreach ($settings as $key => $value) {
            if (Str::startsWith('strings', $key) && Str::endsWith('id', $key)) {
                continue;
            }
            $value = htmlspecialchars(trim(preg_replace('/\s\s+/u', '\\n', $value)), ENT_QUOTES, 'UTF-8');
            $details[$key] = $value;
        }
        return $details;
    }

    /**
     * @return array
     */
    public function getPluginDetails()
    {
        return [
            'Console level' => glsr(Console::class)->humanLevel(),
            'Console size' => glsr(Console::class)->humanSize(),
            'Last Migration Run' => glsr(Date::class)->localized(glsr(OptionManager::class)->get('last_migration_run'), 'unknown'),
            'Version (current)' => glsr()->version,
            'Version (previous)' => glsr(OptionManager::class)->get('version_upgraded_from'),
        ];
    }

    /**
     * @return array
     */
    public function getWordpressDetails()
    {
        global $wpdb;
        $theme = wp_get_theme();
        return [
            'Active Theme' => sprintf('%s v%s', (string) $theme->name, (string) $theme->version),
            'Email Domain' => substr(strrchr(glsr(OptionManager::class)->getWP('admin_email'), '@'), 1),
            'Home URL' => home_url(),
            'Language' => get_locale(),
            'Memory Limit' => WP_MEMORY_LIMIT,
            'Multisite' => var_export(is_multisite(), true),
            'Page For Posts ID' => glsr(OptionManager::class)->getWP('page_for_posts'),
            'Page On Front ID' => glsr(OptionManager::class)->getWP('page_on_front'),
            'Permalink Structure' => glsr(OptionManager::class)->getWP('permalink_structure', 'default'),
            'Post Stati' => implode(', ', get_post_stati()),
            'Remote Post' => glsr(Cache::class)->getRemotePostTest(),
            'Show On Front' => glsr(OptionManager::class)->getWP('show_on_front'),
            'Site URL' => site_url(),
            'Timezone' => glsr(OptionManager::class)->getWP('timezone_string', $this->getIni('date.timezone').' (PHP)'),
            'Version' => get_bloginfo('version'),
            'WP Debug' => var_export(defined('WP_DEBUG'), true),
            'WP Max Upload Size' => size_format(wp_max_upload_size()),
            'WP Memory Limit' => WP_MEMORY_LIMIT,
        ];
    }

    /**
     * @return string
     */
    protected function detectWebhostProvider()
    {
        $checks = [
            '.accountservergroup.com' => 'Site5',
            '.gridserver.com' => 'MediaTemple Grid',
            '.inmotionhosting.com' => 'InMotion Hosting',
            '.ovh.net' => 'OVH',
            '.pair.com' => 'pair Networks',
            '.stabletransit.com' => 'Rackspace Cloud',
            '.stratoserver.net' => 'STRATO',
            '.sysfix.eu' => 'SysFix.eu Power Hosting',
            'bluehost.com' => 'Bluehost',
            'DH_USER' => 'DreamHost',
            'Flywheel' => 'Flywheel',
            'ipagemysql.com' => 'iPage',
            'ipowermysql.com' => 'IPower',
            'localhost:/tmp/mysql5.sock' => 'ICDSoft',
            'mysqlv5' => 'NetworkSolutions',
            'PAGELYBIN' => 'Pagely',
            'secureserver.net' => 'GoDaddy',
            'WPE_APIKEY' => 'WP Engine',
        ];
        foreach ($checks as $key => $value) {
            if (!$this->isWebhostCheckValid($key)) {
                continue;
            }
            return $value;
        }
        return implode(',', array_filter([DB_HOST, filter_input(INPUT_SERVER, 'SERVER_NAME')]));
    }

    /**
     * @return string
     */
    protected function getHostName()
    {
        return sprintf('%s (%s)', $this->detectWebhostProvider(), Helper::getIpAddress());
    }

    protected function getIni($name, $disabledValue = 'ini_get() is disabled.')
    {
        return Helper::ifTrue(!function_exists('ini_get'), $disabledValue, function () use ($name) {
            return ini_get($name);
        });
    }

    /**
     * @return array
     */
    protected function getWordpressPlugins()
    {
        $plugins = get_plugins();
        $activePlugins = glsr(OptionManager::class)->getWP('active_plugins', [], 'array');
        $inactive = $this->normalizePluginList(array_diff_key($plugins, array_flip($activePlugins)));
        $active = $this->normalizePluginList(array_diff_key($plugins, $inactive));
        return $active + $inactive;
    }

    /**
     * @param string $title
     * @return string
     */
    protected function implode($title, array $details)
    {
        $strings = ['['.$title.']'];
        $padding = max(array_map('strlen', array_keys($details)));
        $padding = max([$padding, static::PAD]);
        foreach ($details as $key => $value) {
            $strings[] = is_string($key)
                ? sprintf('%s : %s', str_pad($key, $padding, '.'), $value)
                : ' - '.$value;
        }
        return implode(PHP_EOL, $strings).PHP_EOL.PHP_EOL;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isWebhostCheckValid($key)
    {
        return defined($key)
            || filter_input(INPUT_SERVER, $key)
            || Str::contains($key, filter_input(INPUT_SERVER, 'SERVER_NAME'))
            || Str::contains($key, DB_HOST)
            || Str::contains($key, php_uname());
    }

    /**
     * @return array
     */
    protected function normalizePluginList(array $plugins)
    {
        $plugins = array_map(function ($plugin) {
            return sprintf('%s v%s', Arr::get($plugin, 'Name'), Arr::get($plugin, 'Version'));
        }, $plugins);
        natcasesort($plugins);
        return array_flip($plugins);
    }

    /**
     * @return array
     */
    protected function purgeSensitiveData(array $settings)
    {
        $keys = glsr()->filterArray('addon/system-info/purge', [
            'licenses.',
            'submissions.recaptcha.key',
            'submissions.recaptcha.secret',
        ]);
        array_walk($settings, function (&$value, $setting) use ($keys) {
            foreach ($keys as $key) {
                if (!Str::startsWith($key, $setting) || empty($value)) {
                    continue;
                }
                $value = str_repeat('•', 13);
                return;
            }
        });
        return $settings;
    }
}
