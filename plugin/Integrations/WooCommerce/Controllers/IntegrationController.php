<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\HookProxy;

class IntegrationController implements ControllerContract
{
    use HookProxy;

    /**
     * @action divi_frontend_assets_dynamic_assets_global_assets_list
     */
    public function filterDiviDynamicAssetsList(array $assets, array $args): array
    {
        if (empty($args['assets_prefix'])) {
            return $assets;
        }
        if ('divi' !== glsr_get_option('general.style')) {
            return $assets;
        }
        if (!wc_get_product(get_the_ID())) {
            return $assets; // not a product page
        }
        $suffix = is_rtl() ? '-rtl' : '';
        $assets['glsr_woocommerce_integration'] = [
            'css' => [
                "{$args['assets_prefix']}/css/contact_form{$suffix}.css",
                "{$args['assets_prefix']}/css/gallery.css",
                "{$args['assets_prefix']}/css/search.css",
            ],
        ];
        return $assets;
    }
}
