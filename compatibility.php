<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Compatibility;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Paginate;
use GeminiLabs\SiteReviews\Review;

defined('ABSPATH') || exit;

/**
 * This fixes the Site Reviews settings when the User Activity Log plugin is enabled.
 * 
 * @see https://wordpress.org/support/topic/this-breaks-the-wordpress-color-picker/
 */
add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    if (str_starts_with($screen->post_type, glsr()->post_type)) {
        wp_dequeue_script('chats-js');
    }
}, 20);

/**
 * This fixes the button classes in themes using the Avia framework.
 *
 * @see https://kriesi.at/themes/enfold-overview/
 */
function glsr_filter_avia_button_classes(array $defaults): array
{
    $defaults['button'] = 'glsr-button avia-button avia-color-theme-color';
    return $defaults;
}
add_action('avia_action_before_framework_init', function () {
    add_filter('site-reviews/defaults/style-classes/defaults', 'glsr_filter_avia_button_classes');
});

/**
 * This fixes the Duplicate option provided by the Enfold theme.
 *
 * @see https://kriesi.at/themes/enfold-overview/
 */
add_action('avf_duplicate_post_added', function ($newPostId, $post) {
    if (!Review::isReview($post)) {
        return;
    }
    $review = glsr_get_review($post->ID);
    if ($review->isValid()) {
        glsr(ReviewManager::class)->createFromPost((int) $newPostId, $review->toArray());
    }
}, 10, 2);

/**
 * Classic Editor.
 *
 * @return array
 *
 * @see https://wordpress.org/plugins/classic-editor/
 */
add_action('edit_form_top', function ($post) {
    if (class_exists('Classic_Editor') && glsr()->post_type === get_post_type($post)) {
        remove_action('edit_form_top', ['Classic_Editor', 'remember_classic_editor']);
    }
}, 0);

/**
 * Bootstrap pagination.
 *
 * @filter site-reviews/paginate_link
 */
function glsr_filter_bootstrap_pagination_link(array $link, array $args, BuilderContract $builder): array
{
    $args['class'] = 'page-link';
    if ('current' === $link['type']) {
        $class = 'page-item active';
        $text = $builder->span($args);
    }
    if ('dots' === $link['type']) {
        $text = $builder->span($args);
    }
    $link['link'] = $builder->li([
        'text' => $text ?? $builder->a($args),
        'class' => $class ?? 'page-item',
    ]);
    return $link;
}
add_filter('site-reviews/paginate_links', function (string $links, array $args): string {
    if ('bootstrap' !== glsr_get_option('general.style')) {
        return $links;
    }
    $args = wp_parse_args(['mid_size' => 1], $args);
    add_filter('site-reviews/paginate_link', 'glsr_filter_bootstrap_pagination_link', 10, 3);
    $links = (new Paginate($args))->links();
    remove_filter('site-reviews/paginate_link', 'glsr_filter_bootstrap_pagination_link');
    $links = wp_list_pluck($links, 'link');
    return implode("\n", $links);
}, 10, 2);

/**
 * @param array $editors
 * @param string $postType
 *
 * @return array
 *
 * @see https://wordpress.org/plugins/classic-editor/
 */
add_filter('classic_editor_enabled_editors_for_post_type', function ($editors, $postType) {
    return glsr()->post_type === $postType
        ? ['block_editor' => false, 'classic_editor' => false]
        : $editors;
}, 10, 2);

/**
 * Add human-readable capability names.
 *
 * @return void
 *
 * @see https://wordpress.org/plugins/members/
 */
add_action('members_register_caps', function () {
    $labels = [
        'create_site-reviews' => _x('Create Reviews', 'admin-text', 'site-reviews'),
        'delete_others_site-reviews' => _x("Delete Others' Reviews", 'admin-text', 'site-reviews'),
        'delete_site-reviews' => _x('Delete Reviews', 'admin-text', 'site-reviews'),
        'delete_private_site-reviews' => _x('Delete Private Reviews', 'admin-text', 'site-reviews'),
        'delete_published_site-reviews' => _x('Delete Approved Reviews', 'admin-text', 'site-reviews'),
        'edit_others_site-reviews' => _x("Edit Others' Reviews", 'admin-text', 'site-reviews'),
        'edit_site-reviews' => _x('Edit Reviews', 'admin-text', 'site-reviews'),
        'edit_private_site-reviews' => _x('Edit Private Reviews', 'admin-text', 'site-reviews'),
        'edit_published_site-reviews' => _x('Edit Approved Reviews', 'admin-text', 'site-reviews'),
        'publish_site-reviews' => _x('Approve Reviews', 'admin-text', 'site-reviews'),
        'read_private_site-reviews' => _x('Read Private Reviews', 'admin-text', 'site-reviews'),
        'respond_to_site-reviews' => _x('Respond To Reviews', 'admin-text', 'site-reviews'),
        'respond_to_others_site-reviews' => _x("Respond To Others' Reviews", 'admin-text', 'site-reviews'),
        'assign_site-review_terms' => _x('Assign Review Categories', 'admin-text', 'site-reviews'),
        'delete_site-review_terms' => _x('Delete Review Categories', 'admin-text', 'site-reviews'),
        'edit_site-review_terms' => _x('Edit Review Categories', 'admin-text', 'site-reviews'),
        'manage_site-review_terms' => _x('Manage Review Categories', 'admin-text', 'site-reviews'),
    ];
    array_walk($labels, function ($label, $capability) {
        members_register_cap($capability, ['label' => $label]);
    });
});

/**
 * Remove Oxygen Builder metabox from reviews.
 *
 * @see https://oxygenbuilder.com
 */
add_action('plugins_loaded', function () {
    global $ct_ignore_post_types;
    if (!empty($ct_ignore_post_types) && is_array($ct_ignore_post_types)) {
        $ct_ignore_post_types[] = Application::POST_TYPE;
        add_filter('pre_option_oxygen_vsb_ignore_post_type_'.Application::POST_TYPE, '__return_true');
    }
});

/**
 * Fix to display all reviews when sorting by rank.
 *
 * @param array $query
 *
 * @return array
 *
 * @see https://searchandfilter.com/
 */
add_filter('sf_edit_query_args', function ($query) {
    if (!empty($query['meta_key']) && '_glsr_ranking' === $query['meta_key']) {
        unset($query['meta_key']);
        $query['meta_query'] = [
            'relation' => 'OR',
            ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
        ];
    }
    return $query;
}, 20);

/**
 * Prevent SG Optimizer from breaking the Site Reviews javascript file.
 *
 * @param array $excluded
 *
 * @return array
 *
 * @see https://wordpress.org/plugins/sg-cachepress/
 */
add_filter('sgo_js_minify_exclude', function ($excluded) {
    if (is_array($excluded) && !in_array(glsr()->id, $excluded)) {
        $excluded[] = glsr()->id;
    }
    return $excluded;
});

/**
 * Load the Ninja Forms (v3) CSS if the plugin style is selected.
 *
 * @see https://ninjaforms.com/
 */
function glsr_is_ninja_forms_compatible(): bool
{
    return class_exists('Ninja_Forms')
        && class_exists('NF_Display_Render')
        && method_exists('Ninja_Forms', 'get_setting')
        && method_exists('NF_Display_Render', 'enqueue_styles_display');
}
function glsr_load_ninja_forms_css(): void
{
    if ('ninja_forms' === glsr_get_option('general.style') && glsr_is_ninja_forms_compatible()) {
        NF_Display_Render::enqueue_styles_display(Ninja_Forms::$url.'assets/css/');
    }
}
add_action('enqueue_block_editor_assets', 'glsr_load_ninja_forms_css');
add_action('wp_enqueue_scripts', 'glsr_load_ninja_forms_css');
add_filter('site-reviews/config/styles/ninja_forms', function (array $config): array {
    if (glsr_is_ninja_forms_compatible()) {
        $formClass = 'nf-style-'.Ninja_Forms()->get_setting('opinionated_styles');
        $config = glsr_set($config, 'classes.form', $formClass);
    }
    return $config;
});

/**
 * Load the WPForms stylesheet when using the WPForms plugin style.
 *
 * @see https://wordpress.org/plugins/wpforms-lite/
 */
add_filter('site-reviews/build/template/reviews-form', function (string $template): string {
    if ('wpforms' === glsr_get_option('general.style')) {
        add_filter('wpforms_frontend_missing_assets_error_js_disable', '__return_true', PHP_INT_MAX);
        add_filter('wpforms_global_assets', '__return_true');
    }
    return $template;
});

/**
 * Remove the "Launch Thrive Architect" button from reviews.
 *
 * @return array
 *
 * @see https://thrivethemes.com/architect/
 */
add_filter('tcb_post_types', function ($blacklist) {
    $blacklist[] = glsr()->post_type;
    return $blacklist;
});

/**
 * This disables OptimizePress v2 assets an notices on Site Reviews admin pages.
 */
function glsr_remove_optimizepress()
{
    if (!defined('OP_VERSION')) {
        return;
    }
    if (!str_starts_with(glsr_current_screen()->post_type, glsr()->post_type)) {
        return;
    }
    glsr(Compatibility::class)->removeHook('admin_enqueue_scripts', 'print_scripts', '\OptimizePress_Admin_Init');
    remove_action('admin_notices', 'checkApiKeyValidity');
    remove_action('admin_notices', 'checkEligibility');
    remove_action('admin_notices', 'compatibilityCheck');
    remove_action('admin_notices', 'goToWebinarNewAPI');
    remove_action('admin_notices', 'pluginAndThemeAreRunning');
    remove_action('admin_notices', 'update_nag_screen');
    remove_filter('use_block_editor_for_post_type', 'op2_disable_gutenberg', 100);
    remove_filter('gutenberg_can_edit_post', 'op2_disable_gutenberg');
}

add_action('load-post.php', 'glsr_remove_optimizepress');
add_action('load-post-new.php', 'glsr_remove_optimizepress');
