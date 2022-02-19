<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

/**
 * @property array $addons
 * @property string $capability
 * @property string $cron_event
 * @property array $db_version
 * @property array $defaults
 * @property string $export_key
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $paged_handle
 * @property string $paged_query_var
 * @property string $post_type
 * @property string $prefix
 * @property array $session
 * @property \GeminiLabs\SiteReviews\Arguments $storage
 * @property string $taxonomy
 * @property array $updated
 * @property string $version
 * @property string $testedTo;
 */
final class Application extends Container
{
    use Plugin;
    use Session;
    use Storage;

    const DB_VERSION = '1.1';
    const EXPORT_KEY = '_glsr_export';
    const ID = 'site-reviews';
    const PAGED_HANDLE = 'pagination_request';
    const PAGED_QUERY_VAR = 'reviews-page'; // filtered
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    /**
     * @var array
     */
    protected $addons = [];

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $updated = [];

    /**
     * @param string $addonId
     * @return false|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function addon($addonId)
    {
        if (isset($this->addons[$addonId])) {
            return $this->addons[$addonId];
        }
        return false;
    }

    /**
     * @param string $capability
     * @param mixed ...$args
     * @return bool
     */
    public function can($capability, ...$args)
    {
        return $this->make(Role::class)->can($capability, ...$args);
    }

    /**
     * @param bool $networkDeactivating
     * @return void
     * @callback register_deactivation_hook
     */
    public function deactivate($networkDeactivating)
    {
        $this->make(Install::class)->deactivate($networkDeactivating);
    }

    /**
     * @param string $page
     * @param string $tab
     * @return string
     */
    public function getPermission($page = '', $tab = 'index')
    {
        $fallback = 'edit_posts';
        $permissions = $this->make(PermissionDefaults::class)->defaults();
        $permission = Arr::get($permissions, $page, $fallback);
        if (is_array($permission)) {
            $permission = Arr::get($permission, $tab, $fallback);
        }
        return empty($permission) || !is_string($permission)
            ? $fallback
            : $permission;
    }

    /**
     * @param string $page
     * @param string $tab
     * @return bool
     */
    public function hasPermission($page = '', $tab = 'index')
    {
        $isAdmin = $this->isAdmin();
        return !$isAdmin || $this->can($this->getPermission($page, $tab));
    }

    /**
     * @return void
     */
    public function init()
    {
        // Ensure the custom database tables exist, this is needed in cases
        // where the plugin has been updated instead of activated.
        $version = get_option(static::PREFIX.'db_version');
        if (empty($version)) {
            $this->make(Install::class)->run();
        } elseif ('1.1' === $version) { // @todo remove this in v5.12.0
            if (!$this->make(SqlSchema::class)->columnExists('ratings', 'terms')) {
                $this->make(Migrate::class)->reset();
                update_option(static::PREFIX.'db_version', '1.0');
            }
        }
        $this->make(Hooks::class)->run();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return (is_admin() || is_network_admin()) && !wp_doing_ajax();
    }

    /**
     * @param object|string $addon
     * @return void
     */
    public function license($addon)
    {
        try {
            $settings = $this->settings(); // populate the initial settings
            $reflection = new \ReflectionClass($addon);
            $id = $reflection->getConstant('ID');
            $licensed = $reflection->getConstant('LICENSED');
            $name = $reflection->getConstant('NAME');
            if (true !== $licensed || 2 !== count(array_filter([$id, $name]))) {
                return;
            }
            $license = [
                'settings.licenses.'.$id => [
                    'class' => 'glsr-license-key regular-text',
                    'default' => '',
                    'label' => $name,
                    'tooltip' => sprintf(_x('Make sure to activate your website domain with your license before adding it here. You can do this by visiting the %s page on your Nifty Plugins account and clicking the "Manage Sites" button.', 'admin-text', 'site-reviews'),
                        sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
                    ),
                    'type' => 'text',
                ],
            ];
            $this->settings = array_merge($license, $settings);
        } catch (\ReflectionException $e) {
            // Fail silently
        }
    }

    /**
     * @param string|object $addon
     * @return void
     */
    public function register($addon)
    {
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
            $addon = $reflection->getName();
            $this->addons[$addon::ID] = $addon;
            $this->singleton($addon); // this goes first!
            $this->alias($addon::ID, $this->make($addon)); // @todo for some reason we have to link an alias to an instantiated class
            $this->make($addon)->init();
        } catch (\ReflectionException $e) {
            glsr_log()->error('Attempted to register an invalid addon.');
        }
    }

    /**
     * @return array
     */
    public function settings()
    {
        if (empty($this->settings)) {
            $settings = $this->filterArray('addon/settings', $this->config('settings'));
            array_walk($settings, function (&$setting) {
                isset($setting['default']) ?: $setting['default'] = '';
            });
            $this->settings = $settings;
        }
        return $this->settings;
    }

    /**
     * @return void
     */
    public function storeDefaults()
    {
        if (empty($this->defaults)) {
            $defaults = $this->make(DefaultsManager::class)->get();
            $this->defaults = $this->filterArray('get/defaults', $defaults);
        }
        if (empty(get_option(OptionManager::databaseKey()))) {
            update_option(OptionManager::databaseKey(), $this->defaults);
        }
    }

    /**
     * @param object|string $addon
     * @param string $file
     * @return void
     */
    public function update($addon, $file)
    {
        if (!current_user_can('manage_options') && !(defined('DOING_CRON') && DOING_CRON)) {
            return;
        }
        if (!file_exists($file)) {
            glsr_log()->error("Add-on does not exist: $file")->debug($addon);
        }
        try {
            $reflection = new \ReflectionClass($addon);
            $addonId = $reflection->getConstant('ID');
            $updateUrl = $reflection->getConstant('UPDATE_URL');
            if ($addonId && $updateUrl && !array_key_exists($addonId, $this->updated)) {
                $this->license($addon);
                $license = glsr_get_option('licenses.'.$addonId);
                $updater = new Updater($updateUrl, $file, compact('license'));
                $updater->init();
                $this->updated[$addonId] = compact('file', 'updateUrl'); // store details for license verification in settings
            }
        } catch (\ReflectionException $e) {
            // We don't need to log an error here.
        }
    }
}
