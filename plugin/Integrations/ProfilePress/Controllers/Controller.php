<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/assigned_users/profile_id
     */
    public function filterProfileId(int $profileId): int
    {
        if (empty($profileId)) {
            global $ppress_frontend_profile_user_obj;
            return (int) ppress_var_obj($ppress_frontend_profile_user_obj, 'ID', 0);
        }
        return $profileId;
    }

    /**
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge(glsr()->config('integrations/profilepress'), $settings);
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $enabled = Arr::get($input, 'settings.integrations.profilepress.enabled');
        if ('yes' === $enabled && !$this->gatekeeper()->allows()) { // this renders any error notices
            $settings = Arr::set($settings, 'settings.integrations.profilepress.enabled', 'no');
        }
        $shortcodes = [
            'account_tab_reviews' => 'site_reviews',
            'profile_tab_form' => 'site_reviews_form',
            'profile_tab_reviews' => 'site_reviews',
            'profile_tab_summary' => 'site_reviews_summary',
        ];
        foreach ($shortcodes as $settingKey => $shortcode) {
            $path = "settings.integrations.profilepress.{$settingKey}";
            $value = Arr::get($input, $path);
            $pattern = get_shortcode_regex([$shortcode]);
            $normalizedValue = preg_replace_callback("/$pattern/", function ($match) use ($settingKey) {
                $atts = shortcode_parse_atts($match[3]);
                if (str_starts_with($settingKey, 'account')) {
                    $atts['author'] = 'user_id';
                } elseif (str_starts_with($settingKey, 'profile')) {
                    $atts['assigned_users'] = 'profile_id';
                }
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
        foreach (['display_account_tab', 'enabled'] as $key) {
            $old = glsr_get_option("settings.integrations.profilepress.{$key}");
            $new = Arr::get($settings, "settings.integrations.profilepress.{$key}");
            if ($new !== $old) {
                // This is a simpler way to force rewrite rules to be recreated
                // instead of using flush_rewrite_rules().
                delete_option('rewrite_rules');
                break;
            }
        }
        return $settings;
    }

    /**
     * @filter site-reviews/integration/subsubsub
     */
    public function filterSubsubsub(array $subsubsub): array
    {
        $subsubsub['profilepress'] = 'ProfilePress';
        return $subsubsub;
    }

    /**
     * @action admin_init
     */
    public function renderNotice(): void
    {
        if (glsr_get_option('integrations.profilepress.enabled', false, 'bool')) {
            $this->gatekeeper()->allows(); // this renders any error notices
        }
    }

    /**
     * @action site-reviews/settings/profilepress
     */
    public function renderSettings(string $rows): void
    {
        glsr(Template::class)->render('integrations/profilepress/settings', [
            'context' => [
                'rows' => $rows,
            ],
        ]);
    }

    protected function gatekeeper(): Gatekeeper
    {
        return new Gatekeeper([
            'wp-user-avatar/wp-user-avatar.php' => [
                'minimum_version' => '4.15',
                'name' => 'ProfilePress',
                'plugin_uri' => 'https://wordpress.org/plugins/wp-user-avatar/',
                'untested_version' => '5.0',
            ],
        ]);
    }
}
