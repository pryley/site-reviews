<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\GamiPress\Commands\AwardAchievement;
use GeminiLabs\SiteReviews\Integrations\GamiPress\Commands\TriggerEvent;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @action wp_ajax_site_reviews_gamipress/users
     */
    public function ajaxFetchUsers(): void
    {
        check_ajax_referer('gamipress_admin', 'nonce');
        global $wpdb;
        $limit = 20;
        $paged = Helper::ifEmpty(absint(filter_input(INPUT_POST, 'page')), 1, true);
        $offset = $limit * ($paged - 1);
        $searchId = '%%'.absint(filter_input(INPUT_POST, 'q')).'%%';
        $searchName = '%%'.$wpdb->esc_like(trim(filter_input(INPUT_POST, 'q'))).'%%';
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT ID, display_name, user_nicename
            FROM {$wpdb->users}
            WHERE (display_name LIKE %s OR user_nicename LIKE %s OR ID LIKE %s)
            ORDER BY display_name ASC 
            LIMIT {$offset}, {$limit}
        ", $searchName, $searchName, $searchId));
        array_walk($results, function ($user) {
            $user->display_name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
        });
        $count = absint($wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->users}
            WHERE (display_name LIKE %s OR user_nicename LIKE %s OR ID LIKE %s)
        ", $searchName, $searchName, $searchId)));
        wp_send_json_success([
            'results' => $results,
            'more_results' => $count > $limit && $count > $offset,
        ]);
    }

    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets(): void
    {
        if ($this->isGamiPressPage()) {
            $handle = glsr()->id.'/admin/gamipress';
            $url = glsr()->url('assets/scripts/gamipress.js');
            wp_enqueue_script($handle, $url, [], glsr()->version, [
                'strategy' => 'defer',
            ]);
        }
    }

    /**
     * @param string $label
     * @param int    $requirementId
     * @param array  $requirement
     *
     * @filter gamipress_activity_trigger_label
     */
    public function filterActivityTriggerLabel($label, $requirementId, $requirement): string
    {
        $requirements = glsr()->args([
            'post_id' => Arr::get($requirement, $this->requirementKey('post_id')),
            'post_type' => Arr::get($requirement, $this->requirementKey('post_type')),
            'rating' => Arr::get($requirement, $this->requirementKey('rating')),
            'rating_condition' => Arr::get($requirement, $this->requirementKey('rating_condition')),
            'user_id' => Arr::get($requirement, $this->requirementKey('user_id')),
            'user_role' => Arr::get($requirement, $this->requirementKey('user_role')),
        ]);
        $trigger = Arr::getAs('string', $requirement, 'trigger_type');
        return glsr(Triggers::class)->label($trigger, $requirements, Cast::toString($label));
    }

    /**
     * @param array $triggers
     *
     * @filter gamipress_activity_triggers
     */
    public function filterActivityTriggers($triggers): array
    {
        $triggers = Arr::consolidate($triggers);
        $triggers[glsr()->name] = glsr(Triggers::class)->labels();
        return $triggers;
    }

    /**
     * @param int    $userId
     * @param string $trigger
     * @param int    $siteId
     * @param array  $args
     *
     * @filter gamipress_log_event_trigger_meta_data
     */
    public function filterLogEventMetaData($meta, $userId, $trigger, $siteId, $args = []): array
    {
        $meta = Arr::consolidate($meta);
        $review = Arr::get($args, '0.review');
        if ($this->isGamiPressTrigger(Cast::toString($trigger)) && $review instanceof Review) {
            $meta['review_id'] = $review->ID;
            $meta['rating'] = $review->rating;
        }
        return $meta;
    }

    /**
     * @param array $fields
     * @param int   $logId
     *
     * @filter gamipress_log_extra_data_fields
     */
    public function filterLogExtraDataFields($fields, $logId): array
    {
        $fields = Arr::consolidate($fields);
        if (ct_get_object_meta($logId, '_gamipress_rating', true)) {
            $fields[] = [
                'desc' => _x('The rating of the review which triggered this event.', 'admin-text', 'site-reviews'),
                'id' => '_gamipress_rating',
                'name' => _x('Rating', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ];
        }
        if (ct_get_object_meta($logId, '_gamipress_review_id', true)) {
            $fields[] = [
                'desc' => _x('The ID of the review which triggered this event.', 'admin-text', 'site-reviews'),
                'id' => '_gamipress_review_id',
                'name' => _x('Review ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ];
        }
        return $fields;
    }

    /**
     * @param array $triggers
     *
     * @filter gamipress_post_type_triggers
     */
    public function filterPostTypeTriggers($triggers): array
    {
        $triggers = Arr::consolidate($triggers);
        $triggers = array_merge($triggers, glsr(Triggers::class)->byPostType());
        return array_values(array_unique($triggers));
    }

    /**
     * @param array $requirement
     * @param int   $requirementId
     *
     * @filter gamipress_requirement_object
     */
    public function filterRequirement($requirement, $requirementId): array
    {
        $requirement = Arr::consolidate($requirement);
        $trigger = Arr::getAs('string', $requirement, 'trigger_type');
        if (!$this->isGamiPressTrigger($trigger)) {
            return $requirement;
        }
        $requirement[$this->requirementKey('rating')] = get_post_meta($requirementId, $this->metaKey('rating'), true);
        $requirement[$this->requirementKey('rating_condition')] = get_post_meta($requirementId, $this->metaKey('rating_condition'), true);
        foreach (['post_id', 'post_type', 'user_id', 'user_role'] as $key) {
            if (str_contains($trigger, "/{$key}")) {
                $requirement[$this->requirementKey($key)] = get_post_meta($requirementId, $this->metaKey($key), true);
            }
        }
        return $requirement;
    }

    /**
     * @filter gamipress_user_role_triggers
     */
    public function filterUserRoleTriggers($triggers): array
    {
        $triggers = Arr::consolidate($triggers);
        $triggers = array_merge($triggers, glsr(Triggers::class)->byUserRole());
        return array_values(array_unique($triggers));
    }

    /**
     * These triggers use the post selector in the ui.
     *
     * @filter gamipress_specific_activity_triggers
     */
    public function filterSpecificActivityTriggers($triggers): array
    {
        $triggers = Arr::consolidate($triggers);
        $postTypes = array_values(get_post_types(['public' => true]));
        $postTypes = glsr()->filterArray('gamipress/posts/post_types', $postTypes);
        foreach (glsr(Triggers::class)->byPostid() as $trigger) {
            $triggers[$trigger] = $postTypes;
        }
        return $triggers;
    }

    /**
     * @param bool   $result        The default return value
     * @param int    $userId        The given user's ID
     * @param int    $requirementId The given requirement's post ID
     * @param string $trigger       The trigger triggered
     * @param int    $siteId        The site id
     * @param array  $args          Arguments of this trigger
     *
     * @filter user_has_access_to_achievement
     */
    public function filterUserHasAccessToAchievement($result, $userId, $requirementId, $trigger, $siteId, $args): bool
    {
        $result = Cast::toBool($result);
        $isRequirement = in_array(get_post_type($requirementId), gamipress_get_requirement_types_slugs());
        if (!$result || !$isRequirement || !$this->isGamiPressTrigger(Cast::toString($trigger))) {
            return $result;
        }
        $params = glsr()->args(Arr::consolidate($args));
        if (!$params->review instanceof Review) {
            return $result;
        }
        $requirements = glsr()->args([
            'post_id' => (int) get_post_meta($requirementId, $this->metaKey('post_id'), true),
            'post_type' => get_post_meta($requirementId, $this->metaKey('post_type'), true),
            'rating' => (int) get_post_meta($requirementId, $this->metaKey('rating'), true),
            'rating_condition' => get_post_meta($requirementId, $this->metaKey('rating_condition'), true),
            'user_id' => (int) get_post_meta($requirementId, $this->metaKey('user_id'), true),
            'user_role' => get_post_meta($requirementId, $this->metaKey('user_role'), true),
        ]);
        $command = $this->execute(new AwardAchievement(
            Cast::toString($trigger),
            Cast::toInt($userId),
            $requirements,
            $params
        ));
        return $command->successful();
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review): void
    {
        if (!is_user_logged_in()) {
            return;
        }
        $review->refresh(); // refresh the review first!
        $assignedPosts = array_filter($review->assignedPosts(),
            fn ($post) => $post->post_author !== get_current_user_id()
        );
        $assignedUsers = get_users([
            'include' => array_merge([0], array_diff($review->assigned_users, [get_current_user_id()])),
        ]);
        $this->execute(new TriggerEvent([
            'assigned_posts' => wp_list_pluck($assignedPosts, 'ID'),
            'assigned_posts_authors' => wp_list_pluck($assignedPosts, 'post_author'),
            'assigned_posts_types' => wp_list_pluck($assignedPosts, 'post_type'),
            'assigned_users' => wp_list_pluck($assignedUsers, 'ID'),
            'assigned_users_roles' => array_unique(array_merge(...wp_list_pluck($assignedUsers, 'roles'))),
            'review' => $review,
        ]));
    }

    /**
     * Using PHP_EOL fixes inline-block layout with the other gamipress fields.
     *
     * @param int $requirementId
     * @param int $postId
     *
     * @action gamipress_requirement_ui_html_after_achievement_post
     */
    public function renderRequirementFields($requirementId, $postId): void
    {
        $options = [];
        $userId = Cast::toInt(get_post_meta($requirementId, $this->metaKey('user_id'), true));
        $rating = Cast::toInt(get_post_meta($requirementId, $this->metaKey('rating'), true));
        if ($user = get_user_by('id', $userId)) {
            $name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
            $options[$user->ID] = sprintf('%s (#%d)', $name, $user->ID);
        }
        echo PHP_EOL.glsr(Builder::class)->select([
            'class' => sprintf('%1$s %1$s-%2$s', $this->requirementKey('user_id'), $requirementId),
            'options' => $options,
            'value' => $userId,
        ]);
        echo PHP_EOL.glsr(Builder::class)->select([
            'class' => $this->requirementKey('rating_condition'),
            'options' => [
                'any' => _x('Any rating', 'admin-text', 'site-reviews'),
                'exact' => _x('Exact rating of', 'admin-text', 'site-reviews'),
                'minimum' => _x('Minimum rating of', 'admin-text', 'site-reviews'),
            ],
            'value' => get_post_meta($requirementId, $this->metaKey('rating_condition'), true),
        ]);
        echo PHP_EOL.glsr(Builder::class)->input([
            'class' => $this->requirementKey('rating'),
            'min' => 0,
            'placeholder' => 0,
            'style' => 'max-width:60px;',
            'type' => 'number',
            'value' => Helper::ifEmpty($rating, 5), // default to 5 stars
        ]);
        echo PHP_EOL.glsr(Builder::class)->span([
            'class' => sprintf('%s-text', $this->requirementKey('rating')),
            'text' => _x('star(s)', 'admin-text', 'site-reviews'),
        ]);
    }

    /**
     * @param int   $requirementId
     * @param array $requirement
     *
     * @action gamipress_ajax_update_requirement
     */
    public function updateRequirement($requirementId, $requirement): void
    {
        $trigger = Arr::getAs('string', $requirement, 'trigger_type');
        if (!$this->isGamiPressTrigger($trigger)) {
            return;
        }
        update_post_meta($requirementId, $this->metaKey('rating'), Arr::get($requirement, $this->requirementKey('rating')));
        update_post_meta($requirementId, $this->metaKey('rating_condition'), Arr::get($requirement, $this->requirementKey('rating_condition')));
        foreach (['post_id', 'post_type', 'user_id', 'user_role'] as $key) {
            if (str_contains($trigger, "/{$key}")) {
                update_post_meta($requirementId, $this->metaKey($key), Arr::get($requirement, $this->requirementKey($key)));
            }
        }
    }

    protected function isGamiPressPage(): bool
    {
        global $hook_suffix, $post_type;
        $hooks = [
            'gamipress_page_gamipress_settings',
            'gamipress_page_gamipress_tools',
            'widgets.php',
        ];
        $postTypes = array_merge(['points-type', 'achievement-type', 'rank-type'],
            gamipress_get_achievement_types_slugs(),
            gamipress_get_rank_types_slugs()
        );
        return in_array($post_type, $postTypes) || in_array($hook_suffix, $hooks);
    }

    protected function isGamiPressTrigger(string $trigger): bool
    {
        return array_key_exists($trigger, glsr(Triggers::class)->triggers());
    }

    protected function metaKey(string $key): string
    {
        $keys = [
            'post_id' => '_gamipress_achievement_post',
            'post_type' => '_gamipress_post_type_required',
            'rating' => Str::snakeCase(sprintf('_%s_gamipress_%s', glsr()->id, $key)),
            'rating_condition' => Str::snakeCase(sprintf('_%s_gamipress_%s', glsr()->id, $key)),
            'user_id' => Str::snakeCase(sprintf('_%s_gamipress_%s', glsr()->id, $key)),
            'user_role' => '_gamipress_user_role_required',
        ];
        return Arr::get($keys, $key, $key);
    }

    protected function requirementKey(string $key): string
    {
        $key = Str::removePrefix($this->metaKey($key), '_gamipress');
        return Str::removePrefix($key, '_');
    }
}
