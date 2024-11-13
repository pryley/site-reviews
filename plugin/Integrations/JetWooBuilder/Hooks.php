<?php

namespace GeminiLabs\SiteReviews\Integrations\JetWooBuilder;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterProductRatingHtml', 'jet-woo-builder/template-functions/product-rating'],
            ['modifyWidgetControls', 'elementor/widget/jet-woo-products/skins_init'],
            ['modifyWidgetControls', 'elementor/widget/jet-woo-products-list/skins_init'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return function_exists('jet_woo_builder');
    }
}
