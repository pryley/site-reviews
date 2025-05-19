<?php

namespace GeminiLabs\SiteReviews\Integrations;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;

trait IntegrationShortcode
{
    private ShortcodeContract $shortcode_instance;

    public function app(): PluginContract
    {
        return glsr();
    }

    abstract public static function shortcodeClass(): string;

    public function shortcodeInstance(): ShortcodeContract
    {
        if (!isset($this->shortcode_instance)) { // @phpstan-ignore-line
            $this->shortcode_instance = glsr(static::shortcodeClass());
        }
        return $this->shortcode_instance;
    }
}
