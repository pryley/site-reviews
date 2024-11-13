<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterDynamicAssets', 'et_dynamic_assets_modules_atf', 10, 2],
            ['filterPaginationLinks', 'site-reviews/paginate_links', 10, 2],
            ['registerDiviModules', 'divi_extensions_init'],
        ]);
        if ($this->isWooEnabled()) {
            $this->hook(Controller::class, [
                ['filterInlineWooStyles', 'site-reviews/enqueue/public/inline-styles'],
            ]);
        }
    }

    protected function isInstalled(): bool
    {
        return 'Divi' === wp_get_theme(get_template())->get('Name');
    }

    protected function isWooEnabled(): bool
    {
        return 'yes' === $this->option('integrations.woocommerce.enabled')
            && 'yes' === get_option('woocommerce_enable_reviews', 'yes')
            && class_exists('WooCommerce')
            && function_exists('WC');
    }
}
