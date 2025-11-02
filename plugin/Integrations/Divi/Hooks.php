<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Divi');
            return;
        }
        $this->hook(Controller::class, [
            ['filterDynamicAssets', 'et_dynamic_assets_modules_atf', 10, 2],
            ['filterPaginationLinks', 'site-reviews/paginate_links', 10, 2],
        ]);
        if ($this->isNextVersion()) {
            $this->hook(Controller::class, [
                ['enqueueNextAssets', 'wp_enqueue_scripts'],
                ['enqueueNextBundledAssets', 'divi_visual_builder_assets_before_enqueue_scripts'],
                ['filterNextDynamicAssets', 'divi_frontend_assets_dynamic_assets_required_module_assets', 10, 2],
                ['filterNextDynamicAssetsListForWoo', 'divi_frontend_assets_dynamic_assets_global_assets_list', 10, 2],
                ['registerNextModules', 'divi_module_library_modules_dependency_tree'],
            ]);
        }
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

    protected function isNextVersion(): bool
    {
        $version = sanitize_text_field($this->version());
        $supportedVersion = str_starts_with($version, '5.0.0-')
            ? '5.0.0-public-alpha.22'
            : '5.0.0';
        return version_compare($version, $supportedVersion, '>=');
    }

    protected function isWooEnabled(): bool
    {
        return 'yes' === $this->option('integrations.woocommerce.enabled')
            && 'yes' === get_option('woocommerce_enable_reviews', 'yes')
            && class_exists('WooCommerce')
            && function_exists('WC');
    }

    protected function supportedVersion(): string
    {
        return '4.0';
    }

    protected function version(): string
    {
        if ($this->isInstalled()) {
            return (string) wp_get_theme(get_template())->get('Version');
        }
        return '';
    }
}
