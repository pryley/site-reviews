<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $update_url
 */
abstract class Addon
{
    const ID = '';
    const NAME = '';
    const SLUG = '';
    const UPDATE_URL = '';

    public $file;
    public $languages;
    public $testedTo;
    public $updater;
    public $version;

    public function __construct()
    {
        $this->file = str_replace('plugin/Application', static::ID, (new ReflectionClass($this))->getFileName());
        $plugin = get_file_data($this->file, [
            'languages' => 'Domain Path',
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
     * @return void|string
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        $constant = 'static::'.strtoupper($property);
        if (defined($constant)) {
            return constant($constant);
        }
    }

    public function make($class)
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class);
    }

    /**
     * @return void
     */
    public function init()
    {
        $reflection = new ReflectionClass($this);
        $className = Str::replaceLast($reflection->getShortname(), 'Hooks', $reflection->getName());
        if (class_exists($className)) {
            (new $className())->run();
        } else {
            glsr_log()->error('The '.static::NAME.' add-on is missing a Hooks class');
        }
    }

    /**
     * @param string $file
     * @return string
     */
    public function path($file = '')
    {
        return plugin_dir_path($this->file).ltrim(trim($file), '/');
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->updater = new Updater(static::UPDATE_URL, $this->file, [
            'license' => glsr_get_option('settings.licenses.'.static::ID),
            'testedTo' => $this->testedTo,
        ]);
        $this->updater->init();
    }

    /**
     * @param string $path
     * @return string
     */
    public function url($path = '')
    {
        return esc_url(plugin_dir_url($this->file).ltrim(trim($path), '/'));
    }
}
