<?php

namespace GeminiLabs\SiteReviews\Integrations\Flywheel;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['renderNotice', 'toplevel_page_flywheel', 20],
        ]);
    }
}
