<?php

namespace GeminiLabs\SiteReviews\Premium\Host;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

class Hooks extends \GeminiLabs\SiteReviews\Addons\Hooks
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
