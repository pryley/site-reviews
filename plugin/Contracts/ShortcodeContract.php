<?php

namespace GeminiLabs\SiteReviews\Contracts;

/**
 * @property array  $args
 * @property string $debug
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

    public function getDisplayOptions(): array;

    public function getHideOptions(): array;

    /**
     * @return static
     */
    public function normalize(array $args, string $type = '');
}
