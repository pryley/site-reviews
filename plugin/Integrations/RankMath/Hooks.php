<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if ('rankmath' !== glsr_get_option('schema.integration.plugin')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterSchema', 'rank_math/json_ld', 99],
            ['filterSchemaPreview', 'rank_math/schema/preview/validate', 20],
        ]);
    }
}
