<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\DependencyDefaults;

class Gatekeeper
{
    public array $dependencies;
    public array $errors;

    public const ERROR_NOT_ACTIVATED = 'not_activated';
    public const ERROR_NOT_INSTALLED = 'not_installed';
    public const ERROR_NOT_SUPPORTED = 'not_supported';
    public const ERROR_NOT_TESTED = 'not_tested';

    /**
     * The error grades that refuse a request to enable an integration.
     * ERROR_NOT_TESTED is deliberately absent: an untested version is a
     * warning, not an enforcement (see allowsEnabling).
     */
    public const BLOCKING_ERRORS = [
        self::ERROR_NOT_ACTIVATED,
        self::ERROR_NOT_INSTALLED,
        self::ERROR_NOT_SUPPORTED,
    ];

    public function __construct(array $dependencies)
    {
        $this->errors = [];
        $this->parseDependencies($dependencies);
    }

    /**
     * Checks if all dependencies meet activation criteria.
     *
     * @return bool true if all dependencies are satisfied, false otherwise
     */
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

    /**
     * Checks if the dependencies allow an integration to be newly enabled.
     * Every failed check still stores its notice, but only a plugin that is
     * missing, inactive, or below the supported version refuses the request;
     * a version newer than the tested one warns without refusing.
     *
     * @return bool true unless a blocking-grade error was found
     */
    public function allowsEnabling(): bool
    {
        if ($this->allows()) {
            return true;
        }
        $errors = wp_list_pluck($this->errors, 'error');
        return empty(array_intersect($errors, static::BLOCKING_ERRORS));
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function isPluginActivated(string $plugin): bool
    {
        $isActive = is_plugin_active($plugin);
        return $this->catchError($plugin, $isActive, static::ERROR_NOT_ACTIVATED);
    }

    public function isPluginInstalled(string $plugin): bool
    {
        $isInstalled = $this->dependencies[$plugin]['is_installed'];
        return $this->catchError($plugin, $isInstalled, static::ERROR_NOT_INSTALLED);
    }

    public function isPluginVersionSupported(string $plugin): bool
    {
        $minimumVersion = $this->dependencies[$plugin]['minimum_version'];
        $installedVersion = $this->dependencies[$plugin]['installed_version'];
        $isVersionValid = version_compare($installedVersion, $minimumVersion, '>=');
        return $this->catchError($plugin, $isVersionValid, static::ERROR_NOT_SUPPORTED);
    }

    public function isPluginVersionTested(string $plugin): bool
    {
        $untestedVersion = $this->dependencies[$plugin]['untested_version'];
        $installedVersion = $this->dependencies[$plugin]['installed_version'];
        $isVersionValid = version_compare($installedVersion, $untestedVersion, '<');
        return $this->catchError($plugin, $isVersionValid, static::ERROR_NOT_TESTED);
    }

    protected function catchError(string $plugin, bool $isValid, string $errorType): bool
    {
        if (!$isValid) {
            $this->errors[$plugin] = [
                'error' => $errorType,
                'name' => $this->dependencies[$plugin]['name'],
                'plugin_uri' => $this->dependencies[$plugin]['plugin_uri'],
                'textdomain' => $this->dependencies[$plugin]['textdomain'],
            ];
        }
        return $isValid;
    }

    protected function parseDependencies(array $dependencies): void
    {
        $results = [];
        foreach ($dependencies as $plugin => $data) {
            $data = glsr(DependencyDefaults::class)->restrict($data);
            if (count($data) !== count(array_filter($data))) {
                continue; // incomplete data
            }
            $results[$plugin] = wp_parse_args($data, [
                'installed_version' => '',
                'is_installed' => false,
                'textdomain' => '',
            ]);
            if ($headers = $this->pluginHeaders($plugin)) {
                $data = wp_parse_args($headers, $results[$plugin]); // header values take precedence
                $data['is_installed'] = true;
                $results[$plugin] = $data;
            }
        }
        $this->dependencies = $results;
    }

    protected function pluginHeaders(string $plugin): array
    {
        if (0 !== validate_file($plugin)) {
            return [];
        }
        if (!file_exists(\WP_PLUGIN_DIR.'/'.$plugin)) {
            return [];
        }
        return get_file_data(\WP_PLUGIN_DIR.'/'.$plugin, [
            'installed_version' => 'Version',
            'name' => 'Plugin Name',
            'plugin_uri' => 'Plugin URI',
            'textdomain' => 'Text Domain',
        ]);
    }
}
