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

    public function buildTemplate(): string;

    public function defaults(): DefaultsAbstract;

    public function description(): string;

    public function hasVisibleFields(array $args = []): bool;

    public function name(): string;

    public function normalize(array $args, string $type = ''): ShortcodeContract;

    /**
     * Returns the options for a shortcode setting. Results are filtered
     * by the "site-reviews/shortcode/options/{$options}" hook.
     */
    public function options(string $option, array $args = []): array;

    public function register(): void;

    /**
     * Returns the filtered shortcode settings configuration.
     */
    public function settings(): array;

    public function tag(): string;
}
