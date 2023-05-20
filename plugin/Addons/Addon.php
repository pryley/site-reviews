<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\DefaultsContract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Plugin;
use ReflectionClass;

/**
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property bool $licensed
 * @property string $name
 * @property string $slug
 * @property string $testedTo
 * @property string $update_url
 * @property string $version
 */
abstract class Addon
{
    use Plugin;

    public const ID = '';
    public const LICENSED = false;
    public const NAME = '';
    public const POST_TYPE = '';
    public const SLUG = '';
    public const UPDATE_URL = 'https://niftyplugins.com';

    /**
     * @return static
     */
    public function init()
    {
        $reflection = new ReflectionClass($this);
        $hooks = Str::replaceLast($reflection->getShortname(), 'Hooks', $reflection->getName());
        if (class_exists($hooks)) {
            glsr()->singleton($hooks);
            glsr($hooks)->run();
        } else {
            glsr_log()->error('The '.static::NAME.' addon is missing a Hooks class');
        }
        return $this;
    }

    public function make($class, array $parameters = [])
    {
        $class = Str::camelCase($class);
        $class = ltrim(str_replace([__NAMESPACE__, 'GeminiLabs\SiteReviews'], '', $class), '\\');
        $class = __NAMESPACE__.'\\'.$class;
        return glsr($class, $parameters);
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function option($path = '', $fallback = '', $cast = '')
    {
        $path = Str::removePrefix($path, 'settings.');
        $path = Str::prefix($path, 'addons.'.static::SLUG.'.');
        $path = Str::prefix($path, 'settings.');
        return glsr_get_option($path, $fallback, $cast);
    }

    /**
     * @param string $defaultsClass  The defaults class used to restrict the options
     * @return \GeminiLabs\SiteReviews\Arguments
     */
    public function options($defaultsClass = '')
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
        $query = [
            'no_found_rows' => true, // skip counting the total rows found
            'post_type' => static::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'suppress_filters' => true,
        ];
        if ($perPage > 0) {
            $query['order'] = 'ASC';
            $query['orderby'] = 'post_title';
        }
        $posts = get_posts($query);
        $results = wp_list_pluck($posts, 'post_title', 'ID');
        foreach ($results as $id => &$title) {
            if (empty(trim($title))) {
                $title = _x('Untitled', 'admin-text', 'site-reviews');
            }
            $title = sprintf('%s (ID: %s)', $title, $id);
        }
        natcasesort($results);
        if (!empty($placeholder)) {
            return ['' => $placeholder] + $results;
        }
        return $results;
    }
}
