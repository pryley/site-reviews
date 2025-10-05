<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @action site-reviews/review/created
     */
    public function flushAfterCreated(Review $review, CreateReview $command): void
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (!$review->is_approved) {
            return;
        }
        $postIds = array_merge($command->assigned_posts, [$command->post_id]);
        $postIds = Arr::uniqueInt($postIds);
        $this->flushCache(
            "flushed_after_review_{$review->ID}_created",
            $postIds
        );
    }

    /**
     * @action site-reviews/migration/end
     */
    public function flushAfterMigrated(): void
    {
        $this->flushCache('flushed_after_plugin_migrated');
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function flushAfterTransitioned(Review $review, string $new, string $old): void
    {
        if (did_action('site-reviews/review/updated')) {
            return;
        }
        if (!in_array('publish', [$new, $old])) {
            return;
        }
        $status = [
            'publish' => 'approved',
            'pending' => 'unapproved',
            'trash' => 'trashed',
        ][$new] ?? $new;
        $this->flushCache(
            "flushed_after_review_{$review->ID}_{$status}",
            $review->assigned_posts
        );
    }

    /**
     * @action site-reviews/review/updated
     */
    public function flushAfterUpdated(Review $review): void
    {
        if (did_action('site-reviews/review/transitioned')) {
            return;
        }
        $this->flushCache(
            "flushed_after_review_{$review->ID}_updated",
            $review->assigned_posts
        );
    }

    /**
     * @action site-reviews/cache/flush
     */
    public function flushReviewCache(Review $review): void
    {
        $this->flushCache(
            "flushed_review_{$review->ID}",
            $review->assigned_posts
        );
    }

    protected function flushCache(string $log, ?array $postIds = null): void
    {
        if ([] === $postIds && !glsr()->filterBool('cache/flush_all', true)) {
            return;
        }
        $postIds = Arr::consolidate($postIds);
        $flushed = empty($postIds) ? 'all' : implode(', ', $postIds);
        glsr_log()->debug("cache::{$log} [{$flushed}]");
        $this->purgeEnduranceCache($postIds);
        $this->purgeFlyingPressCache($postIds);
        $this->purgeHummingbirdCache($postIds);
        $this->purgeLitespeedCache($postIds);
        $this->purgeNitropackCache($postIds);
        $this->purgeSiteGroundCache($postIds);
        $this->purgeSwiftPerformanceCache($postIds);
        $this->purgeW3TotalCache($postIds);
        $this->purgeWPFastestCache($postIds);
        $this->purgeWPOptimizeCache($postIds);
        $this->purgeWPRocketCache($postIds);
        $this->purgeWPSuperCache($postIds);
    }

    /**
     * @see https://github.com/bluehost/endurance-page-cache/
     */
    protected function purgeEnduranceCache(array $postIds = []): void
    {
        // This is a sloppy plugin, the only option we have is to purge the entire cache...
        do_action('epc_purge');
    }

    /**
     * @see https://flyingpress.com/
     */
    protected function purgeFlyingPressCache(array $postIds = []): void
    {
        if (!class_exists('FlyingPress\Purge')) {
            return;
        }
        if (is_callable(['FlyingPress\Purge', 'purge_pages']) && empty($postIds)) {
            \FlyingPress\Purge::purge_pages();
        } elseif (is_callable(['FlyingPress\Purge', 'purge_url'])) {
            foreach ($postIds as $postId) {
                \FlyingPress\Purge::purge_url(get_permalink($postId));
            }
        }
    }

    /**
     * @see https://premium.wpmudev.org/docs/api-plugin-development/hummingbird-api-docs/#action-wphb_clear_page_cache
     */
    protected function purgeHummingbirdCache(array $postIds = []): void
    {
        if (empty($postIds)) {
            do_action('wphb_clear_page_cache');
        }
        foreach ($postIds as $postId) {
            do_action('wphb_clear_page_cache', $postId);
        }
    }

    /**
     * @see https://wordpress.org/plugins/litespeed-cache/
     */
    protected function purgeLitespeedCache(array $postIds = []): void
    {
        if (empty($postIds)) {
            do_action('litespeed_purge_all');
        }
        foreach ($postIds as $postId) {
            do_action('litespeed_purge_post', $postId);
        }
    }

    /**
     * @see https://nitropack.io/
     */
    protected function purgeNitropackCache(array $postIds = []): void
    {
        if (!function_exists('nitropack_invalidate') || !function_exists('nitropack_get_cacheable_object_types')) {
            return;
        }
        if (!get_option('nitropack-autoCachePurge', 1)) {
            return;
        }
        if (empty($postIds)) {
            nitropack_invalidate(null, null, 'Invalidating all pages after creating/updating/deleting one or more unassigned reviews');
            return;
        }
        foreach ($postIds as $postId) {
            $cacheableTypes = nitropack_get_cacheable_object_types();
            $post = get_post($postId);
            $postType = $post->post_type ?? 'post';
            $postTitle = $post->post_title ?? '';
            if (in_array($postType, $cacheableTypes)) {
                nitropack_invalidate(null, "single:{$postId}", sprintf('Invalidating "%s" after creating/updating/deleting an assigned review', $postTitle));
            }
        }
    }

    /**
     * @see https://wordpress.org/plugins/sg-cachepress/
     */
    protected function purgeSiteGroundCache(array $postIds = []): void
    {
        if (function_exists('sg_cachepress_purge_everything') && empty($postIds)) {
            sg_cachepress_purge_everything();
        }
        if (function_exists('sg_cachepress_purge_cache')) {
            foreach ($postIds as $postId) {
                sg_cachepress_purge_cache(get_permalink($postId));
            }
        }
    }

    /**
     * @see https://swiftperformance.io/
     */
    protected function purgeSwiftPerformanceCache(array $postIds = []): void
    {
        if (!class_exists('Swift_Performance_Cache')) {
            return;
        }
        if (empty($postIds)) {
            \Swift_Performance_Cache::clear_all_cache();
        } else {
            \Swift_Performance_Cache::clear_post_cache_array($postIds);
        }
    }

    /**
     * @see https://wordpress.org/plugins/w3-total-cache/
     */
    protected function purgeW3TotalCache(array $postIds = []): void
    {
        if (function_exists('w3tc_flush_all') && empty($postIds)) {
            w3tc_flush_all();
        }
        if (function_exists('w3tc_flush_post')) {
            foreach ($postIds as $postId) {
                w3tc_flush_post($postId);
            }
        }
    }

    /**
     * @see https://www.wpfastestcache.com/
     */
    protected function purgeWPFastestCache(array $postIds = []): void
    {
        if (empty($postIds)) {
            do_action('wpfc_clear_all_cache');
        }
        foreach ($postIds as $postId) {
            do_action('wpfc_clear_post_cache_by_id', false, $postId);
        }
    }

    /**
     * @see https://getwpo.com/documentation/#Purging-the-cache-from-an-other-plugin-or-theme
     */
    protected function purgeWPOptimizeCache(array $postIds = []): void
    {
        if (function_exists('WP_Optimize') && empty($postIds)) {
            WP_Optimize()->get_page_cache()->purge();
        }
        if (class_exists('WPO_Page_Cache')) {
            foreach ($postIds as $postId) {
                \WPO_Page_Cache::delete_single_post_cache($postId);
            }
        }
    }

    /**
     * @see https://docs.wp-rocket.me/article/93-rocketcleanpost
     */
    protected function purgeWPRocketCache(array $postIds = []): void
    {
        if (function_exists('rocket_clean_home') && empty($postIds)) {
            rocket_clean_home();
        }
        if (function_exists('rocket_clean_post')) {
            foreach ($postIds as $postId) {
                rocket_clean_post($postId);
            }
        }
    }

    /**
     * @see https://wordpress.org/plugins/wp-super-cache/
     */
    protected function purgeWPSuperCache(array $postIds = []): void
    {
        if (function_exists('wp_cache_clear_cache') && empty($postIds)) {
            wp_cache_clear_cache();
        }
        if (function_exists('wp_cache_post_change')) {
            foreach ($postIds as $postId) {
                wp_cache_post_change($postId);
            }
        }
    }
}
