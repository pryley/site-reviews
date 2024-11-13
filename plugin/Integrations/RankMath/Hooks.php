<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isEnabled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterSchema', 'rank_math/schema/validated_data', 20],
        ]);
    }

    protected function isEnabled(): bool
    {
        return 'rankmath' === $this->option('schema.integration.plugin');
    }
}
