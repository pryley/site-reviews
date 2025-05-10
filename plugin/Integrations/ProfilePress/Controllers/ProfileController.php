<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers;

use GeminiLabs\SiteReviews\Contracts\TagContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class ProfileController extends AbstractController
{
    public const REVIEWS_TAB = 'member_reviews';

    /**
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterInlineScript(string $javascript): string
    {
        if (!$this->hasVisibilityPermission()) {
            return $javascript;
        }
        return $javascript.'document.addEventListener("DOMContentLoaded", () => {'.
            'GLSR.Event.on("site-reviews/form/handle", (response, form) => {'.
                'if (true !== response.success || "undefined" === typeof response.html) return;'.
                'form.classList.add("glsr-hide-form");'.
                'form.insertAdjacentHTML("afterend", "<p class=\"glsr-no-margins glsr-form-success\">"+response.message+"</p>");'.
                'form.remove()'.
            '})'.
        '})';
    }

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $css): string
    {
        $css .= '.pp-reviews:has(.glsr-reviews) {border-top: 3px solid #eee; padding-top:var(--glsr-gap-xl);}';
        $css .= '.pp-reviews-form:has(div) {border-top: 3px solid #eee; padding:var(--glsr-gap-xl) 0;}';
        $css .= '.pp-reviews-summary .glsr-summary-text {--glsr-summary-text: var(--glsr-text-base);}';
        $css .= '.pp-reviews-summary {padding-top:var(--glsr-gap-lg); padding-bottom:var(--glsr-gap-xl);}';
        return $css;
    }

    /**
     * @filter ppress_profile_tabs
     */
    public function filterProfileTabs(array $tabs): array
    {
        if (!$this->hasVisibilityPermission()) {
            return $tabs;
        }
        $tabs[static::REVIEWS_TAB] = [
            'icon' => 'star',
            'title' => __('Reviews', 'site-reviews'),
        ];
        return $tabs;
    }

    /**
     * @see $this->shortcodeReviews()
     *
     * @filter site-reviews/review/value/author
     */
    public function filterReviewAuthorValue(string $value, TagContract $tag): string
    {
        if ($user = $tag->review->user()) { // @phpstan-ignore-line
            if ($url = ppress_get_frontend_profile_url($user->ID)) {
                return sprintf('<a href="%s">%s</a>', $url, $value);
            }
        }
        return $value;
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
        $value = get_current_user_id() === $this->currentProfileId()
            ? __('You have not received any reviews.', 'site-reviews')
            : __('This person has not received any reviews.', 'site-reviews');
        return glsr(Builder::class)->p([
            'class' => 'glsr-no-margins',
            'text' => $value,
        ]);
    }

    /**
     * @filter ppress_dpf_saved_tabs
     */
    public function filterSavedTabs(array $tabs): array
    {
        if (!$this->hasVisibilityPermission()) {
            return $tabs;
        }
        $tabs[] = static::REVIEWS_TAB;
        return array_values(array_unique($tabs));
    }

    /**
     * @filter site-reviews/summary/value/percentages
     */
    public function filterSummaryPercentagesValue(string $value, TagContract $tag): string
    {
        if (!empty(array_sum($tag->ratings))) { // @phpstan-ignore-line
            return $value;
        }
        return '';
    }

    /**
     * @filter site-reviews/summary/value/text
     */
    public function filterSummaryRatingValue(): string
    {
        $value = get_current_user_id() === $this->currentProfileId()
            ? __('Your rating', 'site-reviews')
            : __('User rating', 'site-reviews');
        return glsr(Builder::class)->h4($value);
    }

    /**
     * @filter site-reviews/summary/value/rating
     */
    public function filterSummaryTextValue(string $value, TagContract $tag): string
    {
        if (!empty(array_sum($tag->ratings))) { // @phpstan-ignore-line
            return $value;
        }
        if (get_current_user_id() === $this->currentProfileId()) {
            return __('No one has reviewed you yet.', 'site-reviews');
        }
        return __('No one has reviewed this person yet.', 'site-reviews');
    }

    /**
     * @action ppress_profile_tab_content_{static::REVIEWS_TAB}
     */
    public function renderReviewsTab(): void
    {
        if (!$this->hasVisibilityPermission()) {
            return;
        }
        glsr(Template::class)->render('templates/profilepress/profile-reviews', [
            'context' => [
                'form' => $this->shortcodeForm(),
                'reviews' => $this->shortcodeReviews(),
                'summary' => $this->shortcodeSummary(),
            ],
        ]);
    }

    protected function currentProfileId(): int
    {
        global $ppress_frontend_profile_user_obj;
        return ppress_var_obj($ppress_frontend_profile_user_obj, 'ID', 0);
    }

    protected function hasVisibilityPermission(): bool
    {
        if (!glsr_get_option('integrations.profilepress.display_profile_tab', false, 'bool')) {
            return false;
        }
        if (!ppress_post_content_has_shortcode('profilepress-user-profile')) {
            return false;
        }
        $roles = glsr_get_option('integrations.profilepress.profile_tab_roles');
        $visibility = glsr_get_option('integrations.profilepress.profile_tab_visibility');
        $user = wp_get_current_user();
        $userHasRole = !empty(array_intersect($roles, (array) $user->roles));
        $userIsOwner = $user->ID === $this->currentProfileId();
        if ('guest' === $visibility && $user->ID) {
            return false;
        }
        if ('member' === $visibility && !$user->ID) {
            return false;
        }
        if ('owner' === $visibility && !$userIsOwner) {
            return false;
        }
        if ('roles' === $visibility && !$userHasRole) {
            return false;
        }
        if ('owner_roles' === $visibility && !$userIsOwner && !$userHasRole) {
            return false;
        }
        return true;
    }

    protected function shortcodeForm(): string
    {
        if (!is_user_logged_in()) {
            $text = sprintf(__('You must be %s to review this person.', 'site-reviews'), glsr(SiteReviewsFormShortcode::class)->loginLink());
            return glsr(Template::class)->build('templates/login-register', [
                'context' => compact('text'),
            ]);
        }
        if (get_current_user_id() === $this->currentProfileId()) {
            return '';
        }
        $ratings = glsr_get_ratings([
            'assigned_users' => $this->currentProfileId(),
            'status' => 'all',
            'user__in' => get_current_user_id(),
        ]);
        if (0 < $ratings->reviews) {
            return '';
        }
        return do_shortcode(glsr_get_option('integrations.profilepress.profile_tab_form'));
    }

    protected function shortcodeReviews(): string
    {
        add_filter('site-reviews/review/value/author', [$this, 'filterReviewAuthorValue'], 20, 2);
        add_filter('site-reviews/reviews/fallback', [$this, 'filterReviewsFallback'], 20, 2);
        $shortcode = do_shortcode(glsr_get_option('integrations.profilepress.profile_tab_reviews'));
        remove_filter('site-reviews/review/value/author', [$this, 'filterReviewAuthorValue'], 20);
        remove_filter('site-reviews/reviews/fallback', [$this, 'filterReviewsFallback'], 20);
        return $shortcode;
    }

    protected function shortcodeSummary(): string
    {
        add_filter('site-reviews/summary/value/percentages', [$this, 'filterSummaryPercentagesValue'], 20, 2);
        add_filter('site-reviews/summary/value/rating', [$this, 'filterSummaryRatingValue'], 20);
        add_filter('site-reviews/summary/value/text', [$this, 'filterSummaryTextValue'], 20, 2);
        $shortcode = do_shortcode(glsr_get_option('integrations.profilepress.profile_tab_summary'));
        remove_filter('site-reviews/summary/value/percentages', [$this, 'filterSummaryPercentagesValue'], 20);
        remove_filter('site-reviews/summary/value/rating', [$this, 'filterSummaryRatingValue'], 20);
        remove_filter('site-reviews/summary/value/text', [$this, 'filterSummaryTextValue'], 20);
        return $shortcode;
    }
}
