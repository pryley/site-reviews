<?php

namespace GeminiLabs\SiteReviews\Integrations\Flywheel;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['renderNotice', 'toplevel_page_flywheel', 20],
        ]);
    }
}
