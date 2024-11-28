<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

/**
 * @property array  $args
 * @property string $debug
 * @property string $description
 * @property string $name
 * @property string $shortcode
 * @property string $type
 */
interface ShortcodeContract
{
    public function build(array $args = [], string $type = 'shortcode'): string;

    /**
     * @param string|array $args
     */
    public function buildBlock($args = []): string;

    /**
     * @param string|array $args
     */
    public function buildShortcode($args = []): string;

    public function buildTemplate(): string;

    public function defaults(): DefaultsAbstract;

    public function getConfig(): array;

    public function getDisplayOptions(): array;

    public function getHideOptions(): array;

    public function getTypeOptions(): array;

    public function hasVisibleFields(array $args = []): bool;

    /**
     * @return static
     */
    public function normalize(array $args, string $type = '');
}
