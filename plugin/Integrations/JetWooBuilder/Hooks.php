<?php

namespace GeminiLabs\SiteReviews\Integrations\JetWooBuilder;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!function_exists('jet_woo_builder')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterProductRatingHtml', 'jet-woo-builder/template-functions/product-rating'],
            ['modifyWidgetControls', 'elementor/widget/jet-woo-products/skins_init'],
            ['modifyWidgetControls', 'elementor/widget/jet-woo-products-list/skins_init'],
        ]);
    }
}
