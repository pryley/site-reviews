<?php

namespace GeminiLabs\SiteReviews\TestAddon\Blocks;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\Block;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\TestAddon\Application;

/**
 * An addon block in the current layout: assets/blocks/{addon-slug}/slugged.
 */
class SluggedBlock extends Block
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
