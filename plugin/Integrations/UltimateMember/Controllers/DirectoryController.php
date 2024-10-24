<?php

namespace GeminiLabs\SiteReviews\Integrations\UltimateMember\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class DirectoryController extends AbstractController
{
    /**
     * @param int $userId
     *
     * @filter um_ajax_get_members_data
     */
    public function filterAjaxMembersData(array $data, $userId): array
    {
        $html = '';
        $userId = Cast::toInt($userId);
        $total = glsr(CountManager::class)->usersReviews($userId);
        if (0 < $total || glsr_get_option('integrations.ultimatemember.display_empty', false, 'bool')) {
            $rating = glsr(CountManager::class)->usersAverage($userId);
            $html = glsr(Builder::class)->div([
                'class' => 'um-member-rating',
                'text' => glsr_star_rating($rating, $total),
            ]);
        }
        $data['glsr_rating_html'] = $html;
        return $data;
    }

    /**
     * @filter um_admin_extend_directory_options_profile
     */
    public function filterDirectoryProfileOptions(array $fields): array
    {
        $fields[] = [
            'id' => '_um_glsr_display_user_rating',
            'type' => 'checkbox',
            'label' => _x('Site Reviews: Display User Rating', 'admin-text', 'site-reviews'),
            'value' => UM()->query()->get_meta_value('_um_glsr_display_user_rating', null, 'na'),
        ];
        return $fields;
    }

    /**
     * @filter um_members_directory_sort_fields
     */
    public function filterDirectoryProfileSortOptions(array $options): array
    {
        $options['glsr_highest_rated'] = _x('Site Reviews: Highest rated first', 'admin-text', 'site-reviews');
        $options['glsr_lowest_rated'] = _x('Site Reviews: Lowest rated first', 'admin-text', 'site-reviews');
        return $options;
    }

    /**
     * Used when the UM "Custom usermeta table" setting is disabled.
     *
     * @param string $sortby
     *
     * @filter um_modify_sortby_parameter
     */
    public function filterDirectorySortBy(array $queryArgs, $sortby): array
    {
        if (!in_array($sortby, ['glsr_highest_rated', 'glsr_lowest_rated'])) {
            return $queryArgs;
        }
        $order = 'glsr_highest_rated' === $sortby ? 'DESC' : 'ASC';
        $sortKey = 'bayesian' === glsr_get_option('integrations.ultimatemember.sorting')
            ? CountManager::META_RANKING
            : CountManager::META_AVERAGE;
        $queryArgs['meta_query'] ??= [];
        $queryArgs['meta_query'][] = [
            'relation' => 'OR',
            [
                'compare' => 'NOT EXISTS',
                'key' => $sortKey,
                'type' => 'NUMERIC',
            ],
            '_rating_key' => [
                'compare' => 'EXISTS',
                'key' => $sortKey,
                'type' => 'NUMERIC',
            ],
        ];
        $queryArgs['orderby'] = [
            '_rating_key' => $order,
            'user_registered' => 'DESC',
        ];
        unset($queryArgs['order']);
        return $queryArgs;
    }

    /**
     * Used when the UM "Custom usermeta table" setting is enabled.
     *
     * @param \um\core\Member_Directory_Meta $query
     * @param array                          $directoryData
     * @param string                         $sortby
     *
     * @action um_pre_users_query
     */
    public function modifyQuerySortby($query, $directoryData, $sortby): void
    {
        if (!in_array($sortby, ['glsr_highest_rated', 'glsr_lowest_rated'])) {
            return;
        }
        $order = esc_sql('glsr_highest_rated' === $sortby ? 'DESC' : 'ASC');
        $sortKey = 'bayesian' === glsr_get_option('integrations.ultimatemember.sorting')
            ? CountManager::META_RANKING
            : CountManager::META_AVERAGE;
        $query->joins[] = glsr(Query::class)->sql(
            "LEFT JOIN table|usermeta AS glsr_usermeta ON (glsr_usermeta.user_id = u.ID AND glsr_usermeta.meta_key = %s)",
            $sortKey
        );
        $query->sql_order = " ORDER BY CAST(glsr_usermeta.meta_value AS SIGNED) {$order}, u.user_registered DESC";
    }

    /**
     * @param array $args
     *
     * @action um_members_just_after_name_tmpl
     * @action um_members_list_after_user_name_tmpl
     */
    public function modifyTmpl($args): void
    {
        $displayRating = Arr::get($args, 'glsr_display_user_rating');
        if (empty($displayRating)) {
            return;
        }
        echo "<# if ('undefined' !== typeof user.glsr_rating_html) { #>{{{user.glsr_rating_html}}}<# } #>";
    }
}
