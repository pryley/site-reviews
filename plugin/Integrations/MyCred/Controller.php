<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

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
        $installed[Str::snakeCase(glsr()->id)] = [
            'callback' => [MyCredHook::class],
            'description' => _x('Awards %_plural% for reviews. Supports awarding %_plural% to authors of assigned posts, assigned users, and users submitting a review.', 'admin-text', 'site-reviews'),
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
        $references['review_approved'] = _x('Approved Review (Site Reviews)', 'admin-text', 'site-reviews');
        $references['review_trashed'] = _x('Deleted Review (Site Reviews)', 'admin-text', 'site-reviews');
        $references['review_unapproved'] = _x('Unapproved Review (Site Reviews)', 'admin-text', 'site-reviews');
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
