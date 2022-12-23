<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TaxonomyController;

class TaxonomyHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(TaxonomyController::class, [
            ['filterRowActions', "{$this->taxonomy}_row_actions", 10, 2],
        ]);
    }
}
