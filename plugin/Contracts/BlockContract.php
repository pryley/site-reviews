<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

interface BlockContract
{
    public function app(): PluginContract;

    public function register(): void;

    public function render(array $attributes): string;

    public function shortcodeInstance(): ShortcodeContract;
}
