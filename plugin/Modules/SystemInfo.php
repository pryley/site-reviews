<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class SystemInfo implements \Stringable
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
        $sections = [ // order is intentional
            'plugin' => 'Plugin',
            'addon' => 'Addon',
            'reviews' => 'Reviews',
            'action-scheduler' => 'Action Scheduler',
            'database' => 'Database',
            'server' => 'Server',
            'wordpress' => 'WordPress',
            'drop-ins' => 'Drop-ins',
            'mu-plugins' => 'Must-Use Plugins',
            'active-plugins' => 'Active Plugins',
            'inactive-plugins' => 'Inactive Plugins',
            'settings' => 'Plugin Settings',
        ];
        $results = [];
        foreach ($sections as $sectionKey => $sectionTitle) {
            $method = Helper::buildMethodName('section', $sectionKey);
            if (!method_exists($this, $method)) {
                continue;
            }
            $values = call_user_func([$this, $method]);
            $values = glsr()->filterArray("system-info/section/{$sectionKey}", $values, $this->data);
            if (empty($values)) {
                continue;
            }
            $results[] = $this->implode($sectionTitle, $values);
        }
        return implode('', $results);
    }

    public function sectionActionScheduler(): array
    {
        $counts = glsr(Queue::class)->actionCounts();
        $counts = shortcode_atts(['complete' => [], 'pending' => [], 'failed' => []], $counts);
        $result = [];
        foreach ($counts as $status => $data) {
            $data = wp_parse_args($data, ['count' => 0, 'latest' => '', 'oldest' => '']);
            $label = "Actions ({$status})";
            if (0 === $data['count']) {
                $result[$label] = $data['count'];
                continue;
            }
            if (1 === $data['count']) {
                $result[$label] = sprintf('%s (latest: %s)', $data['count'], $data['latest']);
                continue;
            }
            $result[$label] = sprintf('%s (latest: %s, oldest: %s)',
                $data['count'],
                $data['latest'],
                $data['oldest']
            );
        }
        $result['Data Store'] = get_class(\ActionScheduler_Store::instance());
        $result['Version'] = \ActionScheduler_Versions::instance()->latest_version();
        return $result;
    }

    public function sectionActivePlugins(): array
    {
        return $this->plugins($this->group('wp-plugins-active'));
    }

    public function sectionAddons(): array
    {
        $details = [];
        foreach (array_keys(glsr()->retrieveAs('array', 'addons')) as $addonId) {
            if ($addon = glsr($addonId)) {
                $details[$addon->name] = $addon->version;
            }
        }
        return $details;
    }

    public function sectionDatabase(): array
    {
        if (glsr(Tables::class)->isSqlite()) {
            return [
                'Database Engine' => $this->value('wp-database.db_engine'),
                'Database Version' => $this->value('wp-database.database_version'),
            ];
        }
        $engines = glsr(Tables::class)->tableEngines($removePrefix = true);
        foreach ($engines as $engine => $tables) {
            $engines[$engine] = sprintf('%s (%s)', $engine, implode('|', $tables));
        }
        return [
            'Charset' => $this->value('wp-database.database_charset'),
            'Collation' => $this->value('wp-database.database_collate'),
            'Extension' => $this->value('wp-database.extension'),
            'Table Engines' => implode(', ', $engines),
            'Version (client)' => $this->value('wp-database.client_version'),
            'Version (server)' => $this->value('wp-database.server_version'),
        ];
    }

    public function sectionDropIns(): array
    {
        return $this->group('wp-dropins');
    }

    public function sectionInactivePlugins(): array
    {
        return $this->plugins($this->group('wp-plugins-inactive'));
    }

    public function sectionMuPlugins()
    {
        return $this->plugins($this->group('wp-mu-plugins'));
    }

    public function sectionPlugin(): array
    {
        $merged = array_keys(array_filter([
            'css' => glsr()->filterBool('optimize/css', false),
            'js' => glsr()->filterBool('optimize/js', false),
        ]));
        return [
            'Console Level' => glsr(Console::class)->humanLevel(),
            'Console Size' => glsr(Console::class)->humanSize(),
            'Database Version' => (string) get_option(glsr()->prefix.'db_version'),
            'Last Migration Run' => glsr(Date::class)->localized(glsr(Migrate::class)->lastRun(), 'unknown'),
            'Merged Assets' => implode('/', Helper::ifEmpty($merged, ['No'])),
            'Network Activated' => Helper::ifTrue(is_plugin_active_for_network(glsr()->basename), 'Yes', 'No'),
            'Version' => sprintf('%s (%s)', glsr()->version, glsr(OptionManager::class)->get('version_upgraded_from')),
        ];
    }

    public function sectionReviews(): array
    {
        return array_merge($this->ratingCounts(), $this->reviewCounts());
    }

    public function sectionServer(): array
    {
        return [
            'cURL Version' => $this->value('wp-server.curl_version'),
            'Display Errors' => $this->ini('display_errors', 'No'),
            'File Uploads' => $this->value('wp-media.file_uploads'),
            'GD version' => $this->value('wp-media.gd_version'),
            'Ghostscript Version' => $this->value('wp-media.ghostscript_version'),
            'Hosting Provider' => $this->hostingProvider(),
            'ImageMagick Version' => $this->value('wp-media.imagemagick_version'),
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
            'Server IP Address' => Helper::serverIp(),
            'Server Software' => $this->value('wp-server.httpd_software'),
            'SUHOSIN Installed' => $this->value('wp-server.suhosin'),
            'Upload Max Filesize' => $this->value('wp-server.upload_max_filesize'),
        ];
    }

    public function sectionSettings(): array
    {
        $settings = glsr(OptionManager::class)->getArray('settings');
        $settings = Arr::flatten($settings, true);
        $settings = $this->purgeSensitiveData($settings);
        $details = [];
        foreach ($settings as $key => $value) {
            if (str_starts_with($key, 'strings') && str_ends_with($key, 'id')) {
                continue;
            }
            $details[$key] = trim(preg_replace('/\s\s+/u', '\\n', $value));
        }
        return $details;
    }

    public function sectionWordpress(): array
    {
        return [
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
        ];
    }

    protected function data(): array
    {
        return $this->data ??= array_map(
            fn ($section) => array_combine(
                array_keys($fields = Arr::consolidate($section['fields'] ?? [])),
                wp_list_pluck($fields, 'value')
            ),
            glsr(Cache::class)->getSystemInfo()
        );
    }

    protected function group(string $key): array
    {
        return Arr::getAs('array', $this->data(), $key);
    }

    protected function hostingProvider(): string
    {
        if (Helper::isLocalServer()) {
            return 'localhost';
        }
        $domain = parse_url($this->value('wp-core.home_url'), \PHP_URL_HOST);
        $response = glsr(Geolocation::class)->lookup($domain, true);
        $location = $response->body();
        if ($response->successful() && 'success' === ($location['status'] ?? '')) {
            $isp = $location['isp'] ?? '';
            $ip = $location['query'] ?? '';
            return "$isp ($ip)";
        }
        return 'unknown';
    }

    protected function implode(string $title, array $details): string
    {
        $strings = ['['.strtoupper($title).']'];
        $padding = max(static::PAD, ...array_map(
            fn ($key) => mb_strlen(html_entity_decode($key, ENT_HTML5), 'UTF-8'),
            array_keys($details)
        ));
        ksort($details);
        foreach ($details as $key => $value) {
            $key = html_entity_decode((string) $key, ENT_HTML5);
            $pad = $padding - (mb_strlen($key, 'UTF-8') - strlen($key)); // handle unicode character lengths
            $label = str_pad($key, $pad, '.');
            $strings[] = "{$label} : {$value}";
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
