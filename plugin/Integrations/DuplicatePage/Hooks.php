<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePage;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['duplicateReview', 'admin_action_dt_duplicate_post_as_draft', 1],
        ]);
    }
}
