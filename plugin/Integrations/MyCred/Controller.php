<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\MyCred\MyCredHook;
use GeminiLabs\SiteReviews\Integrations\MyCred\MyCredHookWooReviews;

class Controller extends BaseController
{
    /**
     * @param array $installed
     * @return array
     * @filter mycred_setup_hooks
     */
    public function filterHooks($installed)
    {
        $installed = Arr::consolidate($installed);
        $installed[glsr()->id] = [
            'callback' => [MyCredHook::class],
            'description' => __('Awards %_plural% for reviews. Supports awarding %_plural% to authors of assigned posts, assigned users, and users submitting a review.', 'site-reviews'),
            'title' => glsr()->name,
        ];
        return $installed;
    }

    /**
     * @param array $references
     * @return array
     * @filter mycred_all_references
     */
    public function filterReferences($references)
    {
        $references['site_reviews'] = _x('Review (Site Reviews)', 'admin-text', 'site-reviews');
        return $references;
    }

    /**
     * @param array $installed
     * @return array
     * @filter mycred_setup_hooks
     */
    public function filterWooreviewHook($installed)
    {
        if (!class_exists('myCRED_Hook_WooCommerce_Reviews')) {
            return $installed;
        }
        if (!isset($installed['wooreview']['callback'])) {
            return $installed;
        }
        if (!glsr_get_option('addons.woocommerce.enabled', false, 'bool')) {
            return $installed;
        }
        $installed['wooreview']['callback'] = [MyCredHookWooReviews::class];
        return $installed;
    }
}
