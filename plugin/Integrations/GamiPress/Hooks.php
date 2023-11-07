<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!defined('GAMIPRESS_VER')
            || !function_exists('ct_get_object_meta')
            || !function_exists('gamipress_get_achievement_types_slugs')
            || !function_exists('gamipress_get_rank_types_slugs')
            || !function_exists('gamipress_get_requirement_types_slugs')
            || !function_exists('gamipress_trigger_event')) {
            return;
        }
        $this->hook(Controller::class, [
            ['ajaxFetchUsers', 'wp_ajax_site_reviews_gamipress/users'],
            ['enqueueAdminAssets', 'admin_enqueue_scripts'],
            ['filterActivityTriggerLabel', 'gamipress_activity_trigger_label', 10, 3],
            ['filterActivityTriggers', 'gamipress_activity_triggers'],
            ['filterLogEventMetaData', 'gamipress_log_event_trigger_meta_data', 10, 5],
            ['filterLogExtraDataFields', 'gamipress_log_extra_data_fields', 10, 2],
            ['filterPostTypeTriggers', 'gamipress_post_type_triggers'],
            ['filterRequirement', 'gamipress_requirement_object', 10, 2],
            ['filterSpecificActivityTriggers', 'gamipress_specific_activity_triggers'],
            ['filterUserHasAccessToAchievement', 'user_has_access_to_achievement', 10, 6],
            ['filterUserRoleTriggers', 'gamipress_user_role_triggers'],
            ['onReviewCreated', 'site-reviews/review/created', 20],
            ['renderRequirementFields', 'gamipress_requirement_ui_html_after_achievement_post', 10, 2],
            ['updateRequirement', 'gamipress_ajax_update_requirement', 10, 2],
        ]);
    }
}
