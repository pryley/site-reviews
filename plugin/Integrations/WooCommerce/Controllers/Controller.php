<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Controller extends BaseController
{
    /**
     * @action before_woocommerce_init
     */
    public function declareHposCompatibility()
    {
        if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', glsr()->file, true);
        }
    }

    /**
     * @filter site-reviews/addon/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge(glsr()->config('woocommerce'), $settings);
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $enabled = Arr::get($input, 'settings.addons.woocommerce.enabled');
        if ('yes' === $enabled && !$this->gatekeeper()->allows()) { // this renders any error notices
            $settings = Arr::set($settings, 'settings.addons.woocommerce.enabled', 'no');
        }
        $shortcodes = [
            'form' => 'site_reviews_form',
            'reviews' => 'site_reviews',
            'summary' => 'site_reviews_summary',
        ];
        foreach ($shortcodes as $key => $shortcode) {
            $path = 'settings.addons.woocommerce.'.$key;
            $value = Arr::get($input, $path);
            if (1 !== preg_match("/^\[{$shortcode}(\s[^\]]*\]|\])$/", $value)) {
                continue;
            }
            if (!str_contains($value, 'assigned_posts')) {
                $value = str_replace($shortcode, sprintf('%s assigned_posts="post_id"', $shortcode), $value);
                $settings = Arr::set($settings, $path, $value);
            }
        }
        return $settings;
    }

    /**
     * @filter site-reviews/addon/subsubsub
     */
    public function filterSubsubsub(array $subsubsub): array
    {
        $subsubsub['woocommerce'] = 'WooCommerce';
        return $subsubsub;
    }

    /**
     * @action admin_init
     */
    public function renderNotice(): void
    {
        if (glsr_get_option('addons.woocommerce.enabled', false, 'bool')) {
            $this->gatekeeper()->allows(); // this renders any error notices
        }
    }

    /**
     * @action site-reviews/addon/settings/woocommerce
     */
    public function renderSettings(string $rows): void
    {
        glsr(Template::class)->render('integrations/woocommerce/settings', [
            'context' => [
                'rows' => $rows,
            ],
        ]);
    }

    protected function gatekeeper(): Gatekeeper
    {
        return new Gatekeeper([
            'woocommerce/woocommerce.php' => [
                'minimum_version' => '6.4',
                'name' => 'WooCommerce',
                'plugin_uri' => 'https://wordpress.org/plugins/woocommerce/',
                'untested_version' => '9.0',
            ],
        ]);
    }
}
