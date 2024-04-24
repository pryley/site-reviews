<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

/**
 * @property array     $addons
 * @property string    $basename
 * @property string    $capability
 * @property string    $cron_event
 * @property array     $db_version
 * @property array     $defaults
 * @property string    $export_key
 * @property string    $file
 * @property string    $id
 * @property string    $languages
 * @property string    $name
 * @property string    $paged_handle
 * @property string    $paged_query_var
 * @property string    $post_type
 * @property string    $prefix
 * @property array     $session
 * @property Arguments $storage
 * @property string    $taxonomy
 * @property array     $updated
 * @property string    $version
 * @property string    $testedTo;
 */
final class Application extends Container implements PluginContract
{
    use Plugin;
    use Session;
    use Storage;

    public const DB_VERSION = '1.3';
    public const EXPORT_KEY = '_glsr_export';
    public const ID = 'site-reviews';
    public const PAGED_HANDLE = 'pagination_request';
    public const PAGED_QUERY_VAR = 'reviews-page'; // filtered
    public const POST_TYPE = 'site-review';
    public const PREFIX = 'glsr_';
    public const TAXONOMY = 'site-review-category';

    protected array $addons = [];
    protected array $defaults;
    protected string $name;
    protected array $settings;
    protected array $updated = [];

    public function addon(string $addonId)
    {
        return $this->addons[$addonId] ?? null;
    }

    /**
     * @param mixed ...$args
     */
    public function can(string $capability, ...$args): bool
    {
        return $this->make(Role::class)->can($capability, ...$args);
    }

    /**
     * The default plugin settings.
     */
    public function defaults(): array
    {
        if (empty($this->defaults)) {
            $settings = $this->settings();
            $defaults = array_combine(array_keys($settings), wp_list_pluck($settings, 'default'));
            $defaults = wp_parse_args($defaults, [
                'version' => '',
                'version_upgraded_from' => '0.0.0',
            ]);
            $defaults = Arr::unflatten($defaults);
            $defaults = $this->filterArray('settings/defaults', $defaults);
            $this->defaults = $defaults;
        }
        return $this->defaults;
    }

    public function getPermission(string $page = '', string $tab = 'index'): string
    {
        $fallback = 'edit_posts';
        $permissions = $this->make(PermissionDefaults::class)->defaults();
        $permission = Arr::get($permissions, $page, $fallback);
        if (is_array($permission)) {
            $permission = Arr::get($permission, $tab, $fallback);
        }
        $capability = empty($permission) || !is_string($permission)
            ? $fallback
            : $permission;
        return $this->make(Role::class)->capability($capability);
    }

    public function hasPermission(string $page = '', string $tab = 'index'): bool
    {
        $isAdminScreen = is_admin() || is_network_admin();
        return !$isAdminScreen || $this->can($this->getPermission($page, $tab));
    }

    /**
     * This is the entry point to the plugin, it runs before "plugins_loaded".
     * If this is a new major version, settings are copied over here if needed.
     */
    public function init(): void
    {
        $migrated = false;
        // Ensure the custom database tables exist, this is needed in cases
        // where the plugin has been updated instead of activated.
        if (empty(get_option(static::PREFIX.'db_version'))) {
            $this->make(Install::class)->run();
        }
        // If this is a new major version, copy over the previous version settings
        if (empty(get_option(OptionManager::databaseKey()))) {
            $previous = $this->make(OptionManager::class)->previous();
            $settings = array_filter(Arr::getAs('array', $previous, 'settings'));
            if (!empty($settings)) {
                update_option(OptionManager::databaseKey(), $previous);
                add_action('init', [$this->make(Migrate::class), 'run'], 1);
                $migrated = true;
            }
        }
        // Force an immediate plugin migration on database version upgrades
        if (static::DB_VERSION !== get_option(static::PREFIX.'db_version') && !$migrated) {
            add_action('init', [$this->make(Migrate::class), 'run'], 1);
        }
        $this->make(Hooks::class)->run();
    }

    public function isAdmin(): bool
    {
        $isAdminScreen = is_admin() || is_network_admin();
        return $isAdminScreen && !wp_doing_ajax();
    }

    /**
     * This is triggered by $this->update() on "wp_loaded".
     *
     * @param PluginContract|string $addon
     */
    public function license($addon): void
    {
        try {
            $settings = $this->settings();
            $reflection = new \ReflectionClass($addon);
            $id = $reflection->getConstant('ID');
            $licensed = $reflection->getConstant('LICENSED');
            $name = $reflection->getConstant('NAME');
            if (true !== $licensed || 2 !== count(array_filter([$id, $name]))) {
                return;
            }
            $license = [
                "settings.licenses.{$id}" => [
                    'default' => '',
                    'label' => $name,
                    'sanitizer' => 'text',
                    'tooltip' => sprintf(_x('Enter the license key here. Your license can be found on the %s page of your Nifty Plugins account.', 'License Keys (admin-text)', 'site-reviews'),
                        sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
                    ),
                    'type' => 'secret',
                ],
            ];
            $this->settings = array_merge($license, $settings);
        } catch (\ReflectionException $e) {
            // Fail silently
        }
    }

    /**
     * This is triggered by "site-reviews/addon/register" on "plugins_loaded".
     *
     * @param PluginContract|string $addon
     */
    public function register($addon): void
    {
        $retired = [ // @compat these addons have been retired
            'site-reviews-gamipress',
            'site-reviews-woocommerce',
        ];
        $premium = glsr()->filterArray('site-reviews-premium', []);
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
            $addon = $reflection->getName();
            if (in_array($addon::ID, $retired)) {
                $this->append('retired', $addon);
            } elseif (in_array($addon::ID, $premium)
                && !str_starts_with($reflection->getNamespaceName(), 'GeminiLabs\SiteReviews\Premium')) {
                $this->append('site-reviews-premium', $addon);
            } else {
                $this->addons[$addon::ID] = $addon;
                $this->singleton($addon); // this goes first!
                $this->alias($addon::ID, $this->make($addon)); // @todo for some reason we have to link an alias to an instantiated class
                $instance = $this->make($addon)->init();
                $this->append('addons', $instance->version, $instance->id);
            }
        } catch (\ReflectionException $e) {
            glsr_log()->error('Attempted to register an invalid addon.');
        }
    }

    /**
     * The plugin settings configuration.
     * This is first triggered on "wp_loaded" in MainController::updateAddons.
     * 
     * @return mixed
     */
    public function settings(string $path = '')
    {
        if (empty($this->settings)) {
            $settings = $this->config('settings');
            $settings = $this->filterArray('settings', $settings);
            array_walk($settings, function (&$setting) {
                $setting = wp_parse_args($setting, [
                    'default' => '',
                    'sanitizer' => 'text',
                ]);
            });
            $this->settings = $settings;
        }
        if (empty($path)) {
            return $this->settings;
        }
        $settings = Arr::unflatten($this->settings);
        return Arr::get($settings, $path);
    }

    /**
     * This is triggered on "wp_loaded" by "site-reviews/addon/update" in MainController::updateAddons.
     *
     * @param PluginContract|string $addon
     */
    public function update($addon, string $file): void
    {
        if (!current_user_can('manage_options') && !wp_doing_cron()) {
            return;
        }
        if (!file_exists($file)) {
            glsr_log()->error("Addon does not exist: $file")->debug($addon);
        }
        try {
            $reflection = new \ReflectionClass($addon);
            $addonId = $reflection->getConstant('ID');
            $licensed = $reflection->getConstant('LICENSED');
            $updateUrl = $reflection->getConstant('UPDATE_URL');
            if ($addonId && $updateUrl && !array_key_exists($addonId, $this->updated)) {
                $this->license($addon);
                $license = glsr_get_option("licenses.{$addonId}");
                $updater = new Updater($updateUrl, $file, $addonId, compact('license'));
                $updater->init();
                $this->updated[$addonId] = compact('file', 'licensed', 'updateUrl'); // store details for license verification in settings
            }
        } catch (\ReflectionException $e) {
            // We don't need to log an error here.
        }
    }
}
