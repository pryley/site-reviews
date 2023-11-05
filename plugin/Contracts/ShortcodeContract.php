<?php

namespace GeminiLabs\SiteReviews\Contracts;

/**
 * @property array $args
 * @property string $debug
 * @property string $shortcode
 * @property string $type
 */
interface ShortcodeContract
{
    public function build(array $args = [], string $type = 'shortcode'): string;

    public function buildBlock(array $args = []): string;

    public function buildShortcode(array $args = []): string;

    public function buildTemplate(): string;

    public function getDisplayOptions(): array;

    public function getHideOptions(): array;

    public function normalize(array $args, string $type = ''): self;
}
