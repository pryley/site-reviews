<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/avatar/generate
     */
    public function filterAvatarUrl(string $url, Review $review): string
    {
        $type = glsr_get_option('reviews.avatars_fallback');
        $defaultUrl = um_get_default_avatar_uri();
        if ('none' === $type && !$review->author_id) {
            return $defaultUrl;
        }
        if ('none' !== $type && $url === $defaultUrl) {
            return '';
        }
        return $url;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $css): string
    {
        if (glsr_get_option('integrations.ultimatemember.display_directory_ratings', false, 'bool')) {
            $css .= '.um-directory .um-members-grid .um-member-rating:has(.glsr-star-rating) {display:flex; justify-content:center;}';
            $css .= '.um-directory .um-members-list .um-member-rating:has(.glsr-star-rating) {display:flex;}';
            $css .= '.um-directory .glsr-star-rating {margin:3px 0;}';
        }
        if (glsr_get_option('integrations.ultimatemember.display_reviews_tab', false, 'bool')) {
            $css .= '.um-reviews-summary .glsr-summary-text {--glsr-summary-text: var(--glsr-text-base);}';
            $css .= '.um-reviews-summary {padding-top:var(--glsr-gap-lg); padding-bottom:var(--glsr-gap-xl);}';
            $css .= '.um-reviews-form:has(div) {border-top: 3px solid #eee; padding:var(--glsr-gap-xl) 0;}';
            $css .= '.um-reviews-reviews:has(.glsr-reviews) {border-top: 3px solid #eee; padding-top:var(--glsr-gap-xl);}';
        }
        return $css;
    }

    /**
     * @filter site-reviews/assigned_users/profile_id
     */
    public function filterProfileId(int $profileId): int
    {
        if (empty($profileId)) {
            return (int) um_get_requested_user();
        }
        return $profileId;
    }

    /**
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge(glsr()->config('integrations/ultimatemember'), $settings);
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $enabled = Arr::get($input, 'settings.integrations.ultimatemember.enabled');
        if ('yes' === $enabled && !$this->gatekeeper()->allows()) { // this renders any error notices
            $settings = Arr::set($settings, 'settings.integrations.ultimatemember.enabled', 'no');
        }
        $shortcodes = [
            'form' => 'site_reviews_form',
            'reviews' => 'site_reviews',
            'summary' => 'site_reviews_summary',
        ];
        foreach ($shortcodes as $settingKey => $shortcode) {
            $path = "settings.integrations.ultimatemember.{$settingKey}";
            $value = Arr::get($input, $path);
            $pattern = get_shortcode_regex([$shortcode]);
            $normalizedValue = preg_replace_callback("/$pattern/", function ($match) {
                $atts = shortcode_parse_atts($match[3]);
                $atts['assigned_users'] = 'profile_id';
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
        $subsubsub['ultimatemember'] = 'Ultimate Member';
        return $subsubsub;
    }

    /**
     * @action admin_init
     */
    public function renderNotice(): void
    {
        if (glsr_get_option('integrations.ultimatemember.enabled', false, 'bool')) {
            $this->gatekeeper()->allows(); // this renders any error notices
        }
    }

    /**
     * @action site-reviews/settings/ultimatemember
     */
    public function renderSettings(string $rows): void
    {
        glsr(Template::class)->render('integrations/ultimatemember/settings', [
            'context' => [
                'rows' => $rows,
            ],
        ]);
    }

    protected function gatekeeper(): Gatekeeper
    {
        return new Gatekeeper([
            'ultimate-member/ultimate-member.php' => [
                'minimum_version' => '2.5',
                'name' => 'Ultimate Member',
                'plugin_uri' => 'https://wordpress.org/plugins/ultimate-member/',
                'untested_version' => '3.0',
            ],
        ]);
    }
}
