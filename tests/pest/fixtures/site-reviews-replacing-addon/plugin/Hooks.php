<?php

namespace GeminiLabs\SiteReviews\ReplacingAddon;

use GeminiLabs\SiteReviews\Addons\Hooks as AddonHooks;
use GeminiLabs\SiteReviews\Contracts\PluginContract;

class Hooks extends AddonHooks
{
    public function app(): PluginContract
    {
        return glsr(Application::class);
    }

    public function run(): void
    {
        // The fixture wires nothing; init() only requires that this class exists.
    }
}
