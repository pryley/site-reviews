<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Migrate;

class Controller extends AbstractController
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
     * @param mixed $value
     *
     * @return mixed
     *
     * @filter site-reviews/option/addon/woocommerce/enabled
     * @filter site-reviews/option/addon/woocommerce/style
     * @filter site-reviews/option/addon/woocommerce/summary
     * @filter site-reviews/option/addon/woocommerce/reviews
     * @filter site-reviews/option/addon/woocommerce/form
     * @filter site-reviews/option/addon/woocommerce/sorting
     * @filter site-reviews/option/addon/woocommerce/display_empty
     * @filter site-reviews/option/addon/woocommerce/wp_comments
     */
    public function filterOrphanedOptions($value, array $settings, string $path)
    {
        $pendingMigrations = glsr(Migrate::class)->pendingMigrations();
        if (!in_array('Migrate_7_2_0', $pendingMigrations)) {
            $path = str_replace('addons.', 'integrations.', $path);
            return Arr::get($settings, $path);
        }
        return $value;
    }

    /**
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge(glsr()->config('integrations/woocommerce'), $settings);
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $enabled = Arr::get($input, 'settings.integrations.woocommerce.enabled');
        if ('yes' === $enabled && !$this->gatekeeper()->allows()) { // this renders any error notices
            $settings = Arr::set($settings, 'settings.integrations.woocommerce.enabled', 'no');
        }
        $shortcodes = [
            'form' => 'site_reviews_form',
            'reviews' => 'site_reviews',
            'summary' => 'site_reviews_summary',
        ];
        foreach ($shortcodes as $key => $shortcode) {
            $path = "settings.integrations.woocommerce.{$key}";
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
     * @filter site-reviews/integration/subsubsub
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
        if (glsr_get_option('integrations.woocommerce.enabled', false, 'bool')) {
            $this->gatekeeper()->allows(); // this renders any error notices
        }
    }

    /**
     * @action site-reviews/settings/woocommerce
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
                'untested_version' => '10.0',
            ],
        ]);
    }
}
