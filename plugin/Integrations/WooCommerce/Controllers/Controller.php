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
        foreach ($shortcodes as $settingKey => $shortcode) {
            $path = "settings.integrations.woocommerce.{$settingKey}";
            $value = Arr::get($input, $path);
            $pattern = get_shortcode_regex([$shortcode]);
            $normalizedValue = preg_replace_callback("/$pattern/", function ($match) use ($settingKey) {
                $atts = shortcode_parse_atts($match[3]);
                $atts['assigned_posts'] = 'post_id';
                ksort($atts);
                $attributes = [];
                foreach ($atts as $key => $val) {
                    $attributes[] = sprintf('%s="%s"', $key, esc_attr($val));
                }
                $attributes = implode(' ', $attributes);
                return "[{$match[2]} {$attributes}]";
            }, $value);
            $settings = Arr::set($settings, $path, $normalizedValue);
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
                'minimum_version' => '9.6',
                'name' => 'WooCommerce',
                'plugin_uri' => 'https://wordpress.org/plugins/woocommerce/',
                'untested_version' => '11.0',
            ],
        ]);
    }
}
