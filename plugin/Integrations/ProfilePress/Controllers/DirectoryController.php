<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\Controllers;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\ProfilePress\RatingField;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Classes\FormRepository;

class DirectoryController extends AbstractController
{
    /**
     * @param array $shortcodes
     *
     * @filter ppress_user_profile_available_shortcodes
     */
    public function filterAvailableShortcodes($shortcodes): array
    {
        $shortcodes = Arr::consolidate($shortcodes);
        $shortcodes['profile-rating'] = [
            'description' => esc_html_x('Rating of user', 'admin-text', 'site-reviews'),
            'shortcode' => 'profile-rating',
        ];
        return $shortcodes;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-styles
     */
    public function filterInlineStyles(string $css): string
    {
        $css .= '.pp-member-directory .pp-member-rating {display:flex;justify-content:center;}';
        $css .= '.pp-member-directory .pp-member-rating * {display:inline-flex;}';
        return $css;
    }

    /**
     * @param array $args
     *
     * @filter ppress_member_directory_wp_user_args
     */
    public function filterMemberDirectoryArgs($args): array
    {
        $args = Arr::consolidate($args);
        $sortby = $args['meta_key'] ?? '';
        if (!in_array($sortby, [RatingField::LOW_RATED, RatingField::HIGH_RATED])) {
            return $args;
        }
        $order = RatingField::HIGH_RATED === $sortby ? 'DESC' : 'ASC';
        $sortKey = 'bayesian' === glsr_get_option('integrations.profilepress.directory_sorting')
            ? CountManager::META_RANKING
            : CountManager::META_AVERAGE;
        $args['meta_query'] ??= [];
        $args['meta_query'][] = [
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
        $args['orderby'] = [
            '_rating_key' => $order,
            'user_registered' => 'DESC',
        ];
        unset($args['meta_key']);
        unset($args['order']);
        return $args;
    }

    /**
     * @param string $className
     * @param string $formTheme
     * @param string $formType
     *
     * @filter ppress_register_dnd_form_class
     */
    public function filterMemberDirectoryTheme($className, $formTheme, $formType): string
    {
        if ('MemberDirectory' !== $formType) {
            return (string) $className;
        }
        $override = Helper::buildClassName((string) $formTheme, 'Integrations\ProfilePress\MemberDirectory');
        if (!class_exists($override)) {
            return (string) $className;
        }
        return $override;
    }

    /**
     * @param array $settings
     *
     * @filter ppress_form_builder_meta_box_settings
     */
    public function filterMetaBoxSettings($settings): array
    {
        $settings = Arr::consolidate($settings);
        $sorting = $settings['ppress_md_sorting'] ?? [];
        if (empty($sorting)) {
            return $settings;
        }
        foreach (['ppress_md_sort_default', 'ppress_md_sort_method_fields'] as $id) {
            $index = array_search($id, wp_list_pluck($sorting, 'id'));
            if (false === $index) {
                continue;
            }
            $group = _x('Review Fields', 'admin-text', 'site-reviews');
            $sorting[$index]['options'][$group] = [
                RatingField::HIGH_RATED => _x('Highest rated first', 'admin-text', 'site-reviews'),
                RatingField::LOW_RATED => _x('Lowest rated first', 'admin-text', 'site-reviews'),
            ];
            $settings['ppress_md_sorting'] = $sorting;
        }
        return $settings;
    }

    /**
     * @action wp_ajax_pp-builder-preview:5
     */
    public function insertPreviewCss(): void
    {
        check_ajax_referer('ppress-admin-nonce');
        if (!current_user_can('manage_options')) {
            return;
        }
        if ('pp-builder-preview' !== filter_input(INPUT_POST, 'action')) {
            return;
        }
        $css = file_get_contents(glsr()->path('assets/styles/minimal.css'));
        $css .= glsr(EnqueuePublicAssets::class)->inlineStyles();
        $_POST['builder_css'] = $_POST['builder_css'].$css;
    }

    /**
     * @action admin_init
     */
    public function registerProfileBuilderField(): void
    {
        if (!DragDropBuilder::get_instance()->is_drag_drop_page()) {
            return;
        }
        $formType = sanitize_text_field(filter_input(INPUT_GET, 'form-type'));
        if (!in_array($formType, [FormRepository::USER_PROFILE_TYPE, FormRepository::MEMBERS_DIRECTORY_TYPE])) {
            return;
        }
        new RatingField();
    }

    /**
     * @param \WP_User $user
     *
     * @action ppress_register_profile_shortcode
     */
    public function registerProfileRatingShortcode($user): void
    {
        if (!is_a($user, 'WP_User')) {
            return;
        }
        add_shortcode('profile-rating', function () use ($user) {
            $html = '';
            if (!glsr_get_option('integrations.profilepress.enabled', false, 'bool')) {
                return $html;
            }
            $total = glsr(CountManager::class)->usersReviews($user->ID);
            if (0 < $total || glsr_get_option('integrations.profilepress.directory_display_empty', false, 'bool')) {
                $rating = glsr(CountManager::class)->usersAverage($user->ID);
                $html = glsr(Builder::class)->div([
                    'class' => 'pp-member-rating',
                    'text' => glsr_star_rating($rating, $total),
                ]);
            }
            return $html;
        });
    }
}
