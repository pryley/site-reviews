<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class AccountController extends AbstractController
{
    /**
     * @filter um_account_content_hook_reviews
     */
    public function filterAccountContent(): ?string
    {
        if (!$this->hasVisibilityPermission()) {
            return null;
        }
        return glsr(Template::class)->build('templates/ultimatemember/account-reviews', [
            'context' => [
                'reviews' => $this->shortcodeReviews(),
            ],
        ]);
    }

    /**
     * @param array $tabs
     *
     * @filter um_account_page_default_tabs_hook
     */
    public function filterAccountTabs($tabs): array
    {
        $tabs = Arr::consolidate($tabs);
        if (!$this->hasVisibilityPermission()) {
            return $tabs;
        }
        $tabs[500]['reviews'] = [
            'custom' => 'true',
            'icon' => 'um-faicon-star',
            'show_button' => false,
            'title' => esc_html_x('My Reviews', 'admin-text', 'site-reviews'),
        ];
        return $tabs;
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
        return wpautop(
            esc_html__('You have not written any reviews.', 'site-reviews')
        );
    }

    protected function hasVisibilityPermission(): bool
    {
        if (!glsr_get_option('integrations.ultimatemember.display_account_tab', false, 'bool')) {
            return false;
        }
        if (!is_user_logged_in()) {
            return false;
        }
        $roles = glsr_get_option('integrations.ultimatemember.account_tab_roles');
        $visibility = glsr_get_option('integrations.ultimatemember.account_tab_visibility');
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
        $shortcode = do_shortcode(glsr_get_option('integrations.ultimatemember.account_tab_reviews'));
        remove_filter('site-reviews/reviews/fallback', [$this, 'filterReviewsFallback'], 20);
        return $shortcode;
    }
}
