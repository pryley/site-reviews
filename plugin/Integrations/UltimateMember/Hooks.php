<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers\DirectoryController;
use GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers\ProfileController;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterSettings', 'site-reviews/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['filterSubsubsub', 'site-reviews/integration/subsubsub'],
            ['renderNotice', 'admin_init'],
            ['renderSettings', 'site-reviews/settings/ultimatemember'],
        ]);
        if ($this->isInstalled()) {
            $this->hook(Controller::class, [
                ['filterAvatarUrl', 'site-reviews/avatar/generate', 10, 2],
                ['filterProfileId', 'site-reviews/assigned_users/profile_id', 5],
            ]);
        }
        if ($this->isEnabled()) {
            $this->hook(Controller::class, [
                ['filterInlineStyles', 'site-reviews/enqueue/public/inline-styles'],
            ]);
            $this->hook(DirectoryController::class, [
                ['filterAjaxMembersData', 'um_ajax_get_members_data', 50, 2],
                ['filterDirectoryProfileOptions', 'um_admin_extend_directory_options_profile', 15],
                ['filterDirectoryProfileSortOptions', 'um_members_directory_sort_fields'],
                ['filterDirectorySortBy', 'um_modify_sortby_parameter', 100, 2],
                ['modifyQuerySortby', 'um_pre_users_query', 10, 3],
                ['modifyTmpl', 'um_members_just_after_name_tmpl', 1],
                ['modifyTmpl', 'um_members_list_after_user_name_tmpl', 1],
            ]);
            $this->hook(ProfileController::class, [
                ['filterInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
                ['filterProfileTabs', 'um_user_profile_tabs', 100],
                ['renderReviewsTab', 'um_profile_content_user_reviews'],
            ]);
        }
    }

    protected function isEnabled(): bool
    {
        return 'yes' === $this->option('integrations.ultimatemember.enabled')
            && $this->isInstalled();
    }

    protected function isInstalled(): bool
    {
        return function_exists('UM')
            && function_exists('um_get_default_avatar_uri')
            && function_exists('um_get_requested_user')
            && function_exists('um_is_core_page')
            && function_exists('um_user_profile_url');
    }
}
