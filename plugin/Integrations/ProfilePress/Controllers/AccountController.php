<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class AccountController extends AbstractController
{
    /**
     * @param array $tabs
     *
     * @filter ppress_myaccount_tabs
     */
    public function filterAccountTabs($tabs): array
    {
        $tabs = Arr::consolidate($tabs);
        if (!$this->hasVisibilityPermission()) {
            return $tabs;
        }
        $tabs['reviews'] = [
            'title' => esc_html_x('My Reviews', 'admin-text', 'site-reviews'),
            'priority' => 10,
            'icon' => 'star',
            'endpoint' => 'reviews',
            'callback' => [$this, 'renderAccountReviews'],
        ];
        return $tabs;
    }

    /**
     * @see filterAccountTabs
     */
    public function renderAccountReviews(): void
    {
        if (!$this->hasVisibilityPermission()) {
            return;
        }
        glsr(Template::class)->render('templates/profilepress/account-reviews', [
            'context' => [
                'reviews' => $this->shortcodeReviews(),
            ],
        ]);
    }

    /**
     * @see $this->shortcodeReviews()
     *
     * @filter site-reviews/reviews/fallback
     */
    public function filterReviewsFallback(string $fallback, array $args): string
    {
        if ($args['fallback'] !== __('There are no reviews yet. Be the first one to write one.', 'site-reviews')) {
            return $fallback;
        }
        if (!$this->hasVisibilityPermission()) {
            return $fallback;
        }
        return glsr(Builder::class)->p([
            'class' => 'profilepress-myaccount-alert pp-alert-danger',
            'text' => esc_html__('You have not written any reviews.', 'site-reviews'),
        ]);
    }

    protected function hasVisibilityPermission(): bool
    {
        if (!glsr_get_option('integrations.profilepress.display_account_tab', false, 'bool')) {
            return false;
        }
        if (!is_user_logged_in()) {
            return false;
        }
        $roles = glsr_get_option('integrations.profilepress.account_tab_roles');
        $visibility = glsr_get_option('integrations.profilepress.account_tab_visibility');
        $user = wp_get_current_user();
        $userHasRole = !empty(array_intersect($roles, (array) $user->roles));
        if ('roles' === $visibility && !$userHasRole) {
            return false;
        }
        return true;
    }

    protected function shortcodeReviews(): string
    {
        if (!$this->hasVisibilityPermission()) {
            return '';
        }
        add_filter('site-reviews/reviews/fallback', [$this, 'filterReviewsFallback'], 20, 2);
        $shortcode = do_shortcode(glsr_get_option('integrations.profilepress.account_tab_reviews'));
        remove_filter('site-reviews/reviews/fallback', [$this, 'filterReviewsFallback'], 20);
        return $shortcode;
    }
}
