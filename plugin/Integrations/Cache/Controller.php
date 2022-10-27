<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @action site-reviews/migration/end
     */
    public function purgeAll()
    {
        $this->purgeEnduranceCache();
        $this->purgeHummingbirdCache();
        $this->purgeLitespeedCache();
        $this->purgeSiteGroundCache();
        $this->purgeSwiftPerformanceCache();
        $this->purgeW3TotalCache();
        $this->purgeWPFastestCache();
        $this->purgeWPOptimizeCache();
        $this->purgeWPRocketCache();
        $this->purgeWPSuperCache();
    }

    /**
     * @action site-reviews/review/created
     */
    public function purgeForPost(Review $review, CreateReview $command)
    {
        $postIds = array_merge($review->assigned_posts, [$command->post_id]);
        $postIds = array_values(array_filter(array_unique($postIds)));
        if (!empty($postIds)) {
            $this->purgeEnduranceCache($postIds);
            $this->purgeHummingbirdCache($postIds);
            $this->purgeLitespeedCache($postIds);
            $this->purgeSiteGroundCache($postIds);
            $this->purgeSwiftPerformanceCache($postIds);
            $this->purgeW3TotalCache($postIds);
            $this->purgeWPFastestCache($postIds);
            $this->purgeWPOptimizeCache($postIds);
            $this->purgeWPRocketCache($postIds);
            $this->purgeWPSuperCache($postIds);
        }
    }

    /**
     * @see https://github.com/bluehost/endurance-page-cache/
     */
    protected function purgeEnduranceCache(array $postIds = [])
    {
        // This is a sloppy plugin, the only option we have is to purge the entire cache...
        do_action('epc_purge');
    }

    /**
     * @see https://premium.wpmudev.org/docs/api-plugin-development/hummingbird-api-docs/#action-wphb_clear_page_cache
     */
    protected function purgeHummingbirdCache(array $postIds = [])
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
    protected function purgeLitespeedCache(array $postIds = [])
    {
        if (empty($postIds)) {
            do_action('litespeed_purge_all');
        }
        foreach ($postIds as $postId) {
            do_action('litespeed_purge_post', $postId);
        }
    }

    /**
     * @see https://wordpress.org/plugins/sg-cachepress/
     */
    protected function purgeSiteGroundCache(array $postIds = [])
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
    protected function purgeSwiftPerformanceCache(array $postIds = [])
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
    protected function purgeW3TotalCache(array $postIds = [])
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
    protected function purgeWPFastestCache(array $postIds = [])
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
    protected function purgeWPOptimizeCache(array $postIds = [])
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
    protected function purgeWPRocketCache(array $postIds = [])
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
    protected function purgeWPSuperCache(array $postIds = [])
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
