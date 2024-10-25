<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Queue;

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
 * @property array     $settings
 * @property Arguments $storage
 * @property string    $taxonomy
 * @property string    $version
 * @property string    $testedTo;
 */
final class Application extends Container implements PluginContract
{
    use Plugin;
    use Session;
    use Storage;

    public const DB_VERSION = '1.4';
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
     * This is first triggered on "init:5" in MainController::onInit.
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
     * If this is a new major version, settings are copied over here and a migration is run.
     */
    public function init(): void
    {
        $args = [];
        // Ensure the custom database tables exist, this is needed in cases
        // where the plugin has been updated instead of activated.
        if (empty(get_option(static::PREFIX.'db_version'))) {
            $this->make(Install::class)->run();
        }
        // If this is a new major version, copy over the previous version settings
        if (empty(get_option(OptionManager::databaseKey()))) {
            $previous = $this->make(OptionManager::class)->previous();
            if (!empty($previous)) {
                update_option(OptionManager::databaseKey(), $previous, true);
                $args['settings'] = true;
            }
        }
        // Force a plugin migration on database version upgrades
        if (static::DB_VERSION !== get_option(static::PREFIX.'db_version')) {
            $args['database'] = true;
        }
        if (!empty($args)) {
            add_action('init', function () use ($args) {
                glsr(Queue::class)->once(time() + 15, 'queue/migration', $args, true);
            });
        }
        $this->make(Hooks::class)->run();
    }

    public function isAdmin(): bool
    {
        $isAdminScreen = is_admin() || is_network_admin();
        return $isAdminScreen && !wp_doing_ajax();
    }

    /**
     * This is triggered on init:5 by $this->settings().
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
     * This is triggered on "plugins_loaded" by "site-reviews/addon/register".
     */
    public function register(string $addon, bool $isAuthorized = true): void
    {
        $retired = [ // @compat these addons have been retired
            'site-reviews-gamipress',
            'site-reviews-woocommerce',
        ];
        $premium = glsr()->filterArray('site-reviews-premium', []);
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
        } catch (\ReflectionException $e) {
            glsr_log()->error("Attempted to register an invalid addon [$addon]");
            return;
        }
        $addonId = $reflection->getConstant('ID');
        $file = dirname(dirname($reflection->getFileName()));
        $file = trailingslashit($file).$addonId.'.php';
        if (!file_exists($file)) {
            glsr_log()->error("Attempted to register an invalid addon [$addonId].");
            return;
        }
        if (in_array($addonId, $retired)) {
            $this->append('retired', $addon);
            return;
        }
        if (in_array($addonId, $premium)
            && !str_starts_with($reflection->getNamespaceName(), 'GeminiLabs\SiteReviews\Premium')) {
            $this->append('site-reviews-premium', $addon);
            return;
        }
        $pluginData = get_file_data($file, ['update_url' => 'Update URI'], 'plugin');
        if (empty($pluginData['update_url'])) {
            $this->append('compat', $file, $addonId); // this addon needs updating in compatibility mode.
        }
        if (true === $reflection->getConstant('LICENSED')) {
            $this->append('licensed', $addon, $addonId);
        }
        if (true === $isAuthorized) {
            $this->addons[$addonId] = $addon;
            $this->singleton($addon); // this goes first!
            $this->alias($addonId, $this->make($addon)); // @todo for some reason we have to link an alias to an instantiated class
            $instance = $this->make($addon)->init();
            $this->append('addons', $instance->version, $instance->id);
        }
    }

    /**
     * The plugin settings configuration.
     * This is first triggered on "init:5" by MainController::onInit.
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
            $this->settings = $settings; // do this before adding license settings!
            $licensedAddons = $this->retrieveAs('array', 'licensed', []);
            array_walk($licensedAddons, fn ($addon) => $this->license($addon));
        }
        if (empty($path)) {
            return $this->settings;
        }
        $settings = Arr::unflatten($this->settings);
        return Arr::get($settings, $path);
    }
}
