<?php

namespace GeminiLabs\SiteReviews\TestAddon;

use GeminiLabs\SiteReviews\Addons\Controller as AddonController;
use GeminiLabs\SiteReviews\Contracts\PluginContract;

class Controller extends AddonController
{
    public function app(): PluginContract
    {
        return glsr(Application::class);
    }
}
