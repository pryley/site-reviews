<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\DependencyDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Gatekeeper
{
    /**
     * @var array
     */
    public $errors;

    /**
     * @var array
     */
    public $dependencies;

    public function __construct(array $dependencies)
    {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        $this->errors = [];
        $this->parseDependencies($dependencies);
    }

    public function allows(): bool
    {
        foreach ($this->dependencies as $plugin => $data) {
            if (!$this->isPluginInstalled($plugin)) {
                continue;
            }
            if (!$this->isPluginVersionTested($plugin)) {
                continue;
            }
            if (!$this->isPluginVersionSupported($plugin)) {
                continue;
            }
            $this->isPluginActivated($plugin);
        }
        if ($this->hasErrors()) {
            set_transient(glsr()->prefix.'gatekeeper', $this->errors, 30);
            return false;
        }
        return true;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function isPluginActivated(string $plugin): bool
    {
        $isActive = is_plugin_active($plugin) || array_key_exists($plugin, $this->muPlugins());
        return $this->catchError($plugin, $isActive, 'not_activated');
    }

    public function isPluginInstalled(string $plugin): bool
    {
        $isInstalled = array_key_exists($plugin, $this->plugins());
        return $this->catchError($plugin, $isInstalled, 'not_installed');
    }

    public function isPluginVersionSupported(string $plugin): bool
    {
        $requiredVersion = $this->dependencies[$plugin]['Version'];
        $installedVersion = $this->pluginValue($plugin, 'Version');
        $isVersionValid = version_compare($installedVersion, $requiredVersion, '>=');
        return $this->catchError($plugin, $isVersionValid, 'not_supported');
    }

    public function isPluginVersionTested(string $plugin): bool
    {
        $untestedVersion = $this->dependencies[$plugin]['UntestedVersion'];
        $installedVersion = $this->pluginValue($plugin, 'Version');
        $isVersionValid = version_compare($installedVersion, $untestedVersion, '<');
        return $this->catchError($plugin, $isVersionValid, 'not_tested');
    }

    protected function catchError(string $plugin, bool $isValid, string $errorType): bool
    {
        if (!$isValid) {
            $this->errors[$plugin] = wp_parse_args($this->pluginData($plugin), [
                'error' => $errorType,
            ]);
        }
        return $isValid;
    }

    protected function muPlugins(): array
    {
        $plugins = get_mu_plugins();
        if (in_array('Bedrock Autoloader', array_column($plugins, 'Name'))) {
            $autoloaded = get_site_option('bedrock_autoloader');
            if (!empty($autoloaded['plugins'])) {
                return array_merge($plugins, $autoloaded['plugins']);
            }
        }
        return $plugins;
    }

    protected function parseDependencies(array $dependencies): void
    {
        $results = [];
        foreach ($dependencies as $plugin => $data) {
            $data = glsr(DependencyDefaults::class)->restrict($data);
            $data = array_filter($data);
            if (4 === count($data)) {
                $results[$plugin] = [
                    'Name' => $data['name'],
                    'PluginURI' => $data['plugin_uri'],
                    'UntestedVersion' => $data['untested_version'],
                    'Version' => $data['minimum_version'],
                ];
            }
        }
        $this->dependencies = $results;
    }

    protected function pluginData($plugin): array
    {
        $plugins = $this->isPluginInstalled($plugin)
            ? $this->plugins()
            : $this->dependencies;
        if (!empty($plugins[$plugin])) {
            $data = $plugins[$plugin];
            $data['plugin'] = $plugin;
            $data['slug'] = substr($plugin, 0, strrpos($plugin, '/'));
            return array_change_key_case($data, CASE_LOWER);
        }
        glsr_log()->error(sprintf('Plugin information not found for: %s', $plugin));
        return [];
    }

    protected function plugins(): array
    {
        return array_merge(get_plugins(), $this->muPlugins());
    }

    protected function pluginValue(string $plugin, string $key): string
    {
        $plugins = $this->plugins();
        if (array_key_exists($plugin, $plugins)) {
            return Arr::getAs('string', $plugins[$plugin], $key);
        }
        return '';
    }
}
