<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Request;

/**
 * @property string $basename
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $post_type
 * @property string $slug
 * @property string $version
 *
 * @method array  filterArray($hook, ...$args)
 * @method bool   filterBool($hook, ...$args)
 * @method float  filterFloat($hook, ...$args)
 * @method int    filterInt($hook, ...$args)
 * @method object filterObject($hook, ...$args)
 * @method string filterString($hook, ...$args)
 */
interface PluginContract
{
    /** @param mixed ...$args */
    public function action(string $hook, ...$args): void;

    /** @param mixed $args */
    public function args($args = []): Arguments;

    public function build(string $view, array $data = []): string;

    public function config(string $name, bool $filtered = true): array;

    /** @return mixed */
    public function constant(string $property, string $className = 'static');

    public function file(string $view): string;

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function filter(string $hook, ...$args);

    /** @param mixed ...$args */
    public function filterArrayUnique(string $hook, ...$args): array;

    /**
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function option(string $path = '', $fallback = '', string $cast = '');

    public function path(string $file = '', bool $realpath = true): string;

    public function render(string $view, array $data = []): void;

    /** @param mixed ...$args */
    public function request($args = []): Request;

    /**
     * @param mixed ...$args
     *
     * @return mixed|false
     */
    public function runIf(string $className, ...$args);

    public function themePath(string $file = ''): string;

    public function url(string $path = ''): string;

    public function version(string $versionLevel = ''): string;
}
