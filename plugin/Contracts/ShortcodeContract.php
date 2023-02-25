<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface ShortcodeContract
{
    public function build(array $args = [], string $type = 'shortcode'): string;
    public function buildBlock(array $args = []): string;
    public function buildShortcode(array $args = []): string;
    /**
     * @return string
     * @todo add return type hint in v7.0
     */
    public function buildTemplate(array $args = []);
    public function getDisplayOptions(): array;
    public function getHideOptions(): array;
}
