<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\Sinergi\BrowserDetector\Browser;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

class SystemInfo
{
    public const PAD = 40;

    protected $data;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
    }

    public function __toString()
    {
        return $this->get();
    }

    public function get(): string
    {
        $keys = [ // order is intentional
            'plugin',
            'addon',
            'reviews',
            'browser',
            'database',
            'action-scheduler',
            'server',
            'wordpress',
            'drop-ins',
            'mu-plugins',
            'active-plugins',
            'inactive-plugins',
            'settings',
        ];
        return trim(array_reduce($keys, function ($carry, $key) {
            $method = Helper::buildMethodName('get', $key);
            if (!method_exists($this, $method)) {
                return $carry;
            }
            $details = call_user_func([$this, $method]);
            if (empty(Arr::get($details, 'values'))) {
                return $carry;
            }
            $section = Str::dashCase($key);
            $title = strtoupper(Arr::get($details, 'title'));
            $values = Arr::get($details, 'values');
            $values = glsr()->filterArray("system-info/section/{$section}", $values);
            return $carry.$this->implode($title, $values);
        }));
    }

    public function getActionScheduler(): array
    {
        $counts = glsr(Queue::class)->actionCounts();
        $counts = shortcode_atts(array_fill_keys(['complete', 'pending', 'failed'], []), $counts);
        $values = [];
        foreach ($counts as $key => $value) {
            $label = sprintf('Actions (%s)', $key);
            $value = wp_parse_args($value, array_fill_keys(['count', 'latest', 'oldest'], 0));
            if ($value['count'] > 1) {
                $values[$label] = sprintf('%s (latest: %s, oldest: %s)',
                    $value['count'],
                    $value['latest'],
                    $value['oldest']
                );
            } elseif (!empty($value['latest'])) {
                $values[$label] = sprintf('%s (latest: %s)',
                    $value['count'],
                    $value['latest']
                );
            } else {
                $values[$label] = $value['count'];
            }
        }
        $values['Data Store'] = get_class(\ActionScheduler_Store::instance());
        $values['Version'] = \ActionScheduler_Versions::instance()->latest_version();
        return [
            'title' => 'Action Scheduler',
            'values' => $values,
        ];
    }

    public function getActivePlugins(): array
    {
        return [
            'title' => 'Active Plugins',
            'values' => $this->plugins($this->group('wp-plugins-active')),
        ];
    }

    public function getAddon(): array
    {
        $details = [];
        foreach (glsr()->retrieveAs('array', 'addons') as $id => $version) {
            if ($addon = glsr($id)) {
                $details[$addon->name] = $addon->version;
            }
        }
        ksort($details);
        return [
            'title' => 'Addon Details',
            'values' => $details,
        ];
    }

    public function getBrowser(): array
    {
        $browser = new Browser();
        $name = esc_attr($browser->getName());
        $userAgent = esc_attr($browser->getUserAgent()->getUserAgentString());
        $version = esc_attr($browser->getVersion());
        return [
            'title' => 'Browser Details',
            'values' => [
                'Browser Name' => sprintf('%s %s', $name, $version),
                'Browser UA' => $userAgent,
            ],
        ];
    }

    public function getDatabase(): array
    {
        if (glsr(Tables::class)->isSqlite()) {
            $values = [
                'Database Engine' => $this->value('wp-database.db_engine'),
                'Database Version' => $this->value('wp-database.database_version'),
            ];
        } else {
            $engines = glsr(Tables::class)->tableEngines($removePrefix = true);
            foreach ($engines as $engine => $tables) {
                $engines[$engine] = sprintf('%s (%s)', $engine, implode('|', $tables));
            }
            $values = [
                'Charset' => $this->value('wp-database.database_charset'),
                'Collation' => $this->value('wp-database.database_collate'),
                'Extension' => $this->value('wp-database.extension'),
                'Table Engines' => implode(', ', $engines),
                'Version (client)' => $this->value('wp-database.client_version'),
                'Version (server)' => $this->value('wp-database.server_version'),
            ];
        }
        return [
            'title' => 'Database Details',
            'values' => $values,
        ];
    }

    public function getDropIns(): array
    {
        return [
            'title' => 'Drop-ins',
            'values' => $this->group('wp-dropins'),
        ];
    }

    public function getInactivePlugins(): array
    {
        return [
            'title' => 'Inactive Plugins',
            'values' => $this->plugins($this->group('wp-plugins-inactive')),
        ];
    }

    public function getMuPlugins()
    {
        return [
            'title' => 'Must-Use Plugins',
            'values' => $this->plugins($this->group('wp-mu-plugins')),
        ];
    }

    public function getPlugin(): array
    {
        $merged = array_keys(array_filter([
            'css' => glsr()->filterBool('optimize/css', false),
            'js' => glsr()->filterBool('optimize/js', false),
        ]));
        return [
            'title' => 'Plugin Details',
            'values' => [
                'Console Level' => glsr(Console::class)->humanLevel(),
                'Console Size' => glsr(Console::class)->humanSize(),
                'Database Version' => (string) get_option(glsr()->prefix.'db_version'),
                'Last Migration Run' => glsr(Date::class)->localized(glsr(Migrate::class)->lastRun(), 'unknown'),
                'Merged Assets' => implode('/', Helper::ifEmpty($merged, ['No'])),
                'Network Activated' => Helper::ifTrue(is_plugin_active_for_network(glsr()->basename), 'Yes', 'No'),
                'Version' => sprintf('%s (%s)', glsr()->version, glsr(OptionManager::class)->get('version_upgraded_from')),
            ],
        ];
    }

    public function getReviews(): array
    {
        $values = array_merge($this->ratingCounts(), $this->reviewCounts());
        ksort($values);
        return [
            'title' => 'Review Details',
            'values' => $values,
        ];
    }

    public function getServer(): array
    {
        return [
            'title' => 'Server Details',
            'values' => [
                'cURL Version' => $this->value('wp-server.curl_version'),
                'Display Errors' => $this->ini('display_errors', 'No'),
                'File Uploads' => $this->value('wp-media.file_uploads'),
                'GD version' => $this->value('wp-media.gd_version'),
                'Ghostscript version' => $this->value('wp-media.ghostscript_version'),
                'Host Name' => $this->hostname(),
                'ImageMagick version' => $this->value('wp-media.imagemagick_version'),
                'Intl' => Helper::ifEmpty(phpversion('intl'), 'No'),
                'IPv6' => var_export(defined('AF_INET6'), true),
                'Max Effective File Size' => $this->value('wp-media.max_effective_size'),
                'Max Execution Time' => $this->value('wp-server.time_limit'),
                'Max File Uploads' => $this->value('wp-media.max_file_uploads'),
                'Max Input Time' => $this->value('wp-server.max_input_time'),
                'Max Input Variables' => $this->value('wp-server.max_input_variables'),
                'Memory Limit' => $this->value('wp-server.memory_limit'),
                'Multibyte' => Helper::ifEmpty(phpversion('mbstring'), 'No'),
                'Permalinks Supported' => $this->value('wp-server.pretty_permalinks'),
                'PHP Version' => $this->value('wp-server.php_version'),
                'Post Max Size' => $this->value('wp-server.php_post_max_size'),
                'SAPI' => $this->value('wp-server.php_sapi'),
                'Sendmail' => $this->ini('sendmail_path'),
                'Server Architecture' => $this->value('wp-server.server_architecture'),
                'Server Software' => $this->value('wp-server.httpd_software'),
                'SUHOSIN Installed' => $this->value('wp-server.suhosin'),
                'Upload Max Filesize' => $this->value('wp-server.upload_max_filesize'),
            ],
        ];
    }

    public function getSettings(): array
    {
        $settings = glsr(OptionManager::class)->getArray('settings');
        $settings = Arr::flatten($settings, true);
        $settings = $this->purgeSensitiveData($settings);
        ksort($settings);
        $details = [];
        foreach ($settings as $key => $value) {
            if (str_starts_with($key, 'strings') && str_ends_with($key, 'id')) {
                continue;
            }
            $value = htmlspecialchars(trim(preg_replace('/\s\s+/u', '\\n', $value)), ENT_QUOTES, 'UTF-8');
            $details[$key] = $value;
        }
        return [
            'title' => 'Plugin Settings',
            'values' => $details,
        ];
    }

    public function getWordpress(): array
    {
        return [
            'title' => 'WordPress Configuration',
            'values' => [
                'Email Domain' => substr(strrchr((string) get_option('admin_email'), '@'), 1),
                'Environment' => $this->value('wp-core.environment_type'),
                'Hidden From Search Engines' => $this->value('wp-core.blog_public'),
                'Home URL' => $this->value('wp-core.home_url'),
                'HTTPS' => $this->value('wp-core.https_status'),
                'Language (site)' => $this->value('wp-core.site_language'),
                'Language (user)' => $this->value('wp-core.user_language'),
                'Multisite' => $this->value('wp-core.multisite'),
                'Page For Posts ID' => (string) get_option('page_for_posts'),
                'Page On Front ID' => (string) get_option('page_on_front'),
                'Permalink Structure' => $this->value('wp-core.permalink'),
                'Post Stati' => implode(', ', get_post_stati()), // @phpstan-ignore-line
                'Remote Post' => glsr(Cache::class)->getRemotePostTest(),
                'SCRIPT_DEBUG' => $this->value('wp-constants.SCRIPT_DEBUG'),
                'Show On Front' => (string) get_option('show_on_front'),
                'Site URL' => $this->value('wp-core.site_url'),
                'Theme (active)' => sprintf('%s v%s by %s', $this->value('wp-active-theme.name'), $this->value('wp-active-theme.version'), $this->value('wp-active-theme.author')),
                'Theme (parent)' => $this->value('wp-parent-theme.name', 'No'),
                'Timezone' => $this->value('wp-core.timezone'),
                'User Count' => $this->value('wp-core.user_count'),
                'Version' => $this->value('wp-core.version'),
                'WP_CACHE' => $this->value('wp-constants.WP_CACHE'),
                'WP_DEBUG' => $this->value('wp-constants.WP_DEBUG'),
                'WP_DEBUG_DISPLAY' => $this->value('wp-constants.WP_DEBUG_DISPLAY'),
                'WP_DEBUG_LOG' => $this->value('wp-constants.WP_DEBUG_LOG'),
                'WP_MAX_MEMORY_LIMIT' => $this->value('wp-constants.WP_MAX_MEMORY_LIMIT'),
            ],
        ];
    }

    protected function data(): array
    {
        if (empty($this->data)) {
            $this->data = glsr(Cache::class)->getSystemInfo();
            array_walk($this->data, function (&$section) {
                $fields = Arr::consolidate(Arr::get($section, 'fields'));
                array_walk($fields, function (&$values) {
                    $values = Arr::get($values, 'value');
                });
                $section = $fields;
            });
        }
        return $this->data;
    }

    protected function group(string $key): array
    {
        return Arr::getAs('array', $this->data(), $key);
    }

    protected function hostname(): string
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
        $webhost = implode(',', array_filter([DB_HOST, filter_input(INPUT_SERVER, 'SERVER_NAME')]));
        foreach ($checks as $key => $value) {
            if ($this->isWebhost($key)) {
                $webhost = $value;
                break;
            }
        }
        return sprintf('%s (%s)', $webhost, Helper::getIpAddress());
    }

    protected function implode(string $title, array $details): string
    {
        $strings = ["[{$title}]"];
        $padding = max(static::PAD, ...array_map(function ($key) {
            return mb_strlen(html_entity_decode($key, ENT_HTML5), 'UTF-8');
        }, array_keys($details)));
        foreach ($details as $key => $value) {
            $key = html_entity_decode((string) $key, ENT_HTML5);
            $pad = $padding - (mb_strlen($key, 'UTF-8') - strlen($key)); // handle unicode character lengths
            $strings[] = sprintf('%s : %s', str_pad($key, $pad, '.'), $value);
        }
        return implode(PHP_EOL, $strings).PHP_EOL.PHP_EOL;
    }

    protected function ini(string $name, string $fallback = ''): string
    {
        if (function_exists('ini_get')) {
            return Helper::ifEmpty(ini_get($name), $fallback);
        }
        return 'ini_get() is disabled.';
    }

    protected function isWebhost(string $key): bool
    {
        return defined($key)
            || filter_input(INPUT_SERVER, $key)
            || str_contains((string) filter_input(INPUT_SERVER, 'SERVER_NAME'), $key)
            || str_contains(DB_HOST, $key)
            || (function_exists('php_uname') && str_contains(php_uname(), $key))
            || ('WPE_APIKEY' === $key && function_exists('is_wpe')); // WP Engine
    }

    protected function plugins(array $plugins): array
    {
        return array_map(function ($value) {
            $patterns = ['/^(Version )/', '/( \| Auto-updates (en|dis)abled)$/'];
            return preg_replace($patterns, ['v', ''], $value);
        }, $plugins);
    }

    protected function purgeSensitiveData(array $settings): array
    {
        $config = glsr()->settings();
        $config = array_filter($config, function ($field, $key) {
            return str_starts_with($key, 'settings.licenses.') || str_ends_with($key, 'api_key') || 'secret' === ($field['type'] ?? '');
        }, ARRAY_FILTER_USE_BOTH);
        $keys = array_keys($config);
        $keys = array_map(fn ($key) => Str::removePrefix($key, 'settings.'), $keys);
        foreach ($settings as $key => &$value) {
            if (in_array($key, $keys)) {
                $value = Str::mask($value, 0, 8, 24);
            }
        }
        return $settings;
    }

    protected function ratingCounts(): array
    {
        $ratings = glsr(Query::class)->ratings();
        $results = [];
        foreach ($ratings as $type => $counts) {
            if (is_array($counts)) {
                $label = sprintf('Type: %s', $type);
                $results[$label] = array_sum($counts).' ('.implode(', ', $counts).')';
                continue;
            }
            glsr_log()->error('$ratings is not an array, possibly due to incorrectly imported reviews.')
                ->debug(compact('counts', 'ratings'));
        }
        if (empty($results)) {
            return ['Type: local' => 'No reviews'];
        }
        return $results;
    }

    protected function reviewCounts(): array
    {
        $reviews = array_filter((array) wp_count_posts(glsr()->post_type));
        $results = array_sum($reviews);
        if (0 < $results) {
            foreach ($reviews as $status => &$num) {
                $num = sprintf('%s: %d', $status, $num);
            }
            $details = implode(', ', $reviews);
            $results = "{$results} ({$details})";
        }
        return ['Reviews' => $results];
    }

    protected function value(string $path = '', string $fallback = ''): string
    {
        return Arr::getAs('string', $this->data(), $path, $fallback);
    }
}
