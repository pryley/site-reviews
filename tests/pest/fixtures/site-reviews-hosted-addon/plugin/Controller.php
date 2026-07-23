<?php

namespace GeminiLabs\SiteReviews\Premium\HostedThing;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

class Controller extends \GeminiLabs\SiteReviews\Addons\Controller
{
    public function app(): PluginContract
    {
        return glsr(Application::class);
    }
}
