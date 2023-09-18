<?php

namespace GeminiLabs\SiteReviews\Contracts;

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

    /**
     * @return string
     * @todo add return type hint and remove $args in v7.0
     */
    public function buildTemplate(array $args = []);

    public function getDisplayOptions(): array;

    public function getHideOptions(): array;
}
