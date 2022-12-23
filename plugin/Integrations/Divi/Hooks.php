<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterDynamicAssets', 'et_dynamic_assets_modules_atf', 10, 2],
            ['filterPaginationLinks', 'site-reviews/paginate_links', 10, 2],
            ['registerDiviModules', 'divi_extensions_init'],
        ]);
    }
}
