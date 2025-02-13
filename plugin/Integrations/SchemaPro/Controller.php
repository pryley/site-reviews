<?php

namespace GeminiLabs\SiteReviews\Integrations\SchemaPro;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @param array $data
     *
     * @filter wp_schema_pro_schema_article
     * @filter wp_schema_pro_schema_book
     * @filter wp_schema_pro_schema_course
     * @filter wp_schema_pro_schema_event
     * @filter wp_schema_pro_schema_faq
     * @filter wp_schema_pro_schema_how_to
     * @filter wp_schema_pro_schema_image_license
     * @filter wp_schema_pro_schema_local_business
     * @filter wp_schema_pro_schema_product
     * @filter wp_schema_pro_schema_recipe
     * @filter wp_schema_pro_schema_service
     * @filter wp_schema_pro_schema_software_application
     * @filter wp_schema_pro_schema_video_object
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        if (empty($data) || !empty($data['custom-markup'])) {
            return $data; // don't integrate with the custom markup option
        }
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $data;
        }
        if ($rating = Arr::get($schema, 'aggregateRating')) {
            $data['aggregateRating'] = $rating;
        }
        if ($review = Arr::get($schema, 'review')) {
            $data['review'] = $review;
        }
        return $data;
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review): void
    {
        foreach ($review->assigned_posts as $postId) {
            delete_post_meta($postId, BSF_AIOSRS_PRO_CACHE_KEY);
        }
    }

    /**
     * @action site-reviews/settings/updated
     */
    public function onSettingsUpdated(array $settings): void
    {
        $newIntegration = Arr::get($settings, 'settings.schema.integration.plugin');
        $oldIntegration = glsr_get_option('schema.integration.plugin');
        if ('schema_pro' !== $newIntegration) {
            return;
        }
        if ($oldIntegration !== $newIntegration) {
            global $wpdb;
            $wpdb->delete($wpdb->postmeta, ['meta_key' => BSF_AIOSRS_PRO_CACHE_KEY]);
        }
    }
}
