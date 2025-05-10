<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers\AccountController;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers\DirectoryController;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers\ProfileController;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterSettings', 'site-reviews/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['filterSubsubsub', 'site-reviews/integration/subsubsub'],
            ['renderNotice', 'admin_init'],
            ['renderSettings', 'site-reviews/settings/profilepress'],
        ]);
        if ($this->isInstalled()) {
            $this->hook(Controller::class, [
                ['filterProfileId', 'site-reviews/assigned_users/profile_id', 5],
            ]);
            $this->hook(DirectoryController::class, [
                ['registerProfileRatingShortcode', 'ppress_register_profile_shortcode'],
            ]);
        }
        if ($this->isEnabled()) {
            $this->hook(AccountController::class, [
                ['filterAccountTabs', 'ppress_myaccount_tabs'],
            ]);
            $this->hook(DirectoryController::class, [
                ['filterAvailableShortcodes', 'ppress_user_profile_available_shortcodes'],
                ['filterInlineStyles', 'site-reviews/enqueue/public/inline-styles'],
                ['filterMemberDirectoryArgs', 'ppress_member_directory_wp_user_args'],
                ['filterMemberDirectoryTheme', 'ppress_register_dnd_form_class', 10, 3],
                ['filterMetaBoxSettings', 'ppress_form_builder_meta_box_settings'],
                ['insertPreviewCss', 'wp_ajax_pp-builder-preview', 5],
                ['registerProfileBuilderField', 'admin_init'],
            ]);
            $this->hook(ProfileController::class, [
                ['filterInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
                ['filterInlineStyles', 'site-reviews/enqueue/public/inline-styles'],
                ['filterProfileTabs', 'ppress_profile_tabs'],
                ['filterSavedTabs', 'ppress_dpf_saved_tabs'],
                ['renderReviewsTab', 'ppress_profile_tab_content_'.ProfileController::REVIEWS_TAB],
            ]);
        }
    }

    protected function isEnabled(): bool
    {
        return $this->isInstalled()
            && 'yes' === $this->option('integrations.profilepress.enabled');
    }

    protected function isInstalled(): bool
    {
        return function_exists('ppress_get_frontend_profile_url')
            && function_exists('ppress_post_content_has_shortcode')
            && function_exists('ppress_var_obj')
            && defined('PPRESS_VERSION_NUMBER');
    }
}
