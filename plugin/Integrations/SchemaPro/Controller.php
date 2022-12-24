<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @action admin_head
     */
    public function displaySettingNotice()
    {
        if (!$this->isReviewAdminPage()) {
            return;
        }
        $settings = \BSF_AIOSRS_Pro_Helper::$settings['aiosrs-pro-settings']; // @phpstan-ignore-line
        if ('footer' === Arr::get($settings, 'schema-location')) {
            return;
        }
        $message = sprintf(_x('Please go to the %sSchema Pro plugin settings%s page and change the "%s" option to "%s".', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s" target="_blank">', admin_url('options-general.php?page=aiosrs_pro_admin_menu_page&action=wpsp-advanced-settings')),
            '</a>',
            sprintf('<strong>%s</strong>', esc_html__('Add Schema Code In', 'wp-schema-pro')),
            sprintf('<strong>%s</strong>', esc_html__('Footer', 'wp-schema-pro'))
        );
        glsr(Notice::class)->addError($message, [
            _x('The Schema Pro integration with Site Reviews will only work if the schema is loaded in the Footer location.', 'admin-text', 'site-reviews'),
        ]);
    }

    /**
     * @filter wp_schema_pro_schema_{type}
     */
    public function filterSchema(array $schema): array
    {
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
        if (empty($schemas)) {
            return $schema;
        }
        if ($rating = Arr::get($schemas, '0.aggregateRating')) {
            $schema['aggregateRating'] = $rating;
        }
        if ($review = Arr::get($schemas, '0.review')) {
            $schema['review'] = $review;
        }
        return $schema;
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsSanitize(array $options, array $input): array
    {
        $key = 'settings.schema.integration.types';
        $options = Arr::set($options, $key, Arr::get($input, $key, []));
        return $options;
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review)
    {
        foreach ($review->assigned_posts as $postId) {
            delete_post_meta($postId, BSF_AIOSRS_PRO_CACHE_KEY);
        }
    }

    /**
     * @action site-reviews/settings/updated
     */
    public function onSettingsUpdated(array $settings)
    {
        $newIntegration = Arr::get($settings, 'settings.schema.integration.plugin');
        $oldIntegration = glsr_get_option('schema.integration.plugin');
        $newTypes = Arr::consolidate(Arr::get($settings, 'settings.schema.integration.types'));
        $oldTypes = Arr::consolidate(glsr_get_option('schema.integration.types'));
        if ('schema_pro' !== $newIntegration) {
            return;
        }
        sort($oldTypes);
        sort($newTypes);
        if ($oldIntegration !== $newIntegration || $oldTypes !== $newTypes) {
            global  $wpdb;
            $wpdb->delete($wpdb->postmeta, ['meta_key' => BSF_AIOSRS_PRO_CACHE_KEY]);
        }
    }
}
