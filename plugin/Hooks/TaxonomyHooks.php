<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TaxonomyController;

class TaxonomyHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(TaxonomyController::class, [
            ['filterRowActions', "{$this->taxonomy}_row_actions", 10, 2],
        ]);
    }
}
