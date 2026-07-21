<?php

namespace GeminiLabs\SiteReviews\TestAddon\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\Block;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\TestAddon\Application;

/**
 * An addon block: assets/blocks/{block}, the one layout an addon ships.
 */
class AddonBlock extends Block
{
    public function app(): PluginContract
    {
        return glsr(Application::class);
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }
}
