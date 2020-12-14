<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Plugin;
use ReflectionClass;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $slug
 * @property string $testedTo
 * @property string $update_url
 * @property Updater $updater
 * @property string $version
 */
abstract class Addon
{
    use Plugin;

    const ID = '';
    const NAME = '';
    const SLUG = '';
    const UPDATE_URL = '';

    protected $updater;

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

    public function make($class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * @return void
     */
    public function update()
    {
        $doingCron = defined('DOING_CRON') && DOING_CRON;
        if (!current_user_can('manage_options') && !$doingCron) {
            return;
        }
        $this->updater = new Updater(static::UPDATE_URL, $this->file, [
            'license' => glsr_get_option('licenses.'.static::ID),
        ]);
        $this->updater->init();
    }
}
