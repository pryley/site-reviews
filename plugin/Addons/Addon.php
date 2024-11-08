<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Plugin;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property bool   $licensed
 * @property string $name
 * @property string $slug
 * @property string $testedTo
 * @property string $version
 */
abstract class Addon implements PluginContract
{
    use Plugin;

    public const ID = '';
    public const LICENSED = false;
    public const NAME = '';
    public const POST_TYPE = '';
    public const SLUG = '';

    /**
     * @return static
     */
    public function init()
    {
        $reflection = new \ReflectionClass($this);
        $hooks = Str::replaceLast($reflection->getShortname(), 'Hooks', $reflection->getName());
        if (class_exists($hooks)) {
            glsr()->singleton($hooks);
            glsr($hooks)->run();
        } else {
            glsr_log()->error('The '.static::NAME.' addon is missing a Hooks class');
        }
        return $this;
    }

    public function make(string $class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path = '', $fallback = '', string $cast = '')
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, 'addons.'.static::SLUG.'.');
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * You can pass a Defaults class which will be used to restrict the options.
     */
    public function options(string $defaultsClass = ''): Arguments
    {
        $options = glsr_get_option('settings.addons.'.static::SLUG, [], 'array');
        if (is_a($defaultsClass, DefaultsContract::class, true)) {
            $options = glsr($defaultsClass)->restrict($options);
        }
        return glsr()->args($options);
    }

    public function posts(int $perPage = -1, string $placeholder = ''): array
    {
        if (empty(static::POST_TYPE)) {
            return [];
        }
        $results = glsr(Database::class)->posts([
            'post_status' => 'publish',
            'post_type' => static::POST_TYPE,
            'posts_per_page' => $perPage,
        ]);
        if (!empty($placeholder)) {
            return ['' => $placeholder] + $results;
        }
        return $results;
    }
}
