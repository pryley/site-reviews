<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $testedTo
 * @property string $version
 */
Trait Plugin
{
    protected $file;
    protected $languages;
    protected $testedTo;
    protected $version;

    public function __construct()
    {
        $this->file = str_replace('plugin/Application', $this->id, (new ReflectionClass($this))->getFileName());
        $plugin = get_file_data($this->file, [
            'languages' => 'Domain Path',
            'name' => 'Plugin Name',
            'testedTo' => 'Tested up to',
            'version' => 'Version',
        ], 'plugin');
        array_walk($plugin, function ($value, $key) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        });
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        $constant = 'static::'.strtoupper(Str::snakeCase($property));
        if (defined($constant)) {
            return constant($constant);
        }
    }

    /**
     * @param mixed $args
     * @return Arguments
     */
    public function args($args)
    {
        return new Arguments($args);
    }

    /**
     * @param string $file
     * @return string
     */
    public function path($file = '', $realpath = true)
    {
        $path = plugin_dir_path($this->file);
        if (!$realpath) {
            $path = trailingslashit(WP_PLUGIN_DIR).basename(dirname($this->file));
        }
        $path = trailingslashit($path).ltrim(trim($file), '/');
        return apply_filters($this->id.'/path', $path, $file);
    }

    /**
     * @param string $path
     * @return string
     */
    public function url($path = '')
    {
        $url = esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
        return apply_filters($this->id.'/url', $url, $path);
    }

    /**
     * @param string $versionLevel
     * @return string
     */
    public function version($versionLevel = '')
    {
        $pattern = '/^v?(\d{1,5})(\.\d++)?(\.\d++)?(.+)?$/i';
        preg_match($pattern, $this->version, $matches);
        switch ($versionLevel) {
            case 'major':
                $version = Arr::get($matches, 1);
                break;
            case 'minor':
                $version = Arr::get($matches, 1).Arr::get($matches, 2);
                break;
            case 'patch':
                $version = Arr::get($matches, 1).Arr::get($matches, 2).Arr::get($matches, 3);
                break;
        }
        return empty($version)
            ? $this->version
            : $version;
    }
}
