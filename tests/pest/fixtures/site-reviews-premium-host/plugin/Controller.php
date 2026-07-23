<?php

namespace GeminiLabs\SiteReviews\Premium\Host;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

class Controller extends \GeminiLabs\SiteReviews\Addons\Controller
{
    public function app(): PluginContract
    {
        return glsr(Application::class);
    }
}
