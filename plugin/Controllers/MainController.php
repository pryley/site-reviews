<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Install;

class MainController extends Controller
{
    /**
     * switch_to_blog() was run before this hook was triggered.
     * @param array $tables
     * @return array
     * @see http://developer.wordpress.org/reference/functions/wp_uninitialize_site/
     * @filter wpmu_drop_tables
     */
    public function filterDropTables($tables)
    {
        $customTables = [ // order is intentional
            glsr()->prefix.'ratings' => glsr(Query::class)->table('ratings'),
            glsr()->prefix.'assigned_posts' => glsr(Query::class)->table('assigned_posts'),
            glsr()->prefix.'assigned_terms' => glsr(Query::class)->table('assigned_terms'),
            glsr()->prefix.'assigned_users' => glsr(Query::class)->table('assigned_users'),
        ];
        foreach ($customTables as $key => $table) {
            $tables = Arr::prepend($tables, $table, $key); // Custom tables have foreign indexes so they must be removed first!
        }
        return $tables;
    }

    /**
     * @return void
     * @action init
     */
    public function initDefaults()
    {
        // This cannot be done before plugins_loaded as it uses the gettext functions
        glsr()->storeDefaults();
    }

    /**
     * @param \WP_Site $site
     * @return void
     * @action wp_insert_site
     */
    public function installOnNewSite($site)
    {
        if (is_plugin_active_for_network(plugin_basename(glsr()->file))) {
            glsr(Install::class)->runOnSite($site->blog_id);
        }
    }

    /**
     * @return void
     * @action admin_footer
     * @action wp_footer
     */
    public function logOnce()
    {
        glsr_log()->logOnce();
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerAddons()
    {
        glsr()->action('addon/register', glsr());
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerLanguages()
    {
        load_plugin_textdomain(glsr()->id, false,
            trailingslashit(plugin_basename(glsr()->path()).'/'.glsr()->languages)
        );
    }

    /**
     * @return void
     * @action init
     */
    public function registerPostType()
    {
        $this->execute(new RegisterPostType());
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function registerReviewTypes()
    {
        $types = glsr()->filterArray('addon/types', []);
        $types = wp_parse_args($types, [
            'local' => _x('Local Review', 'admin-text', 'site-reviews'),
        ]);
        glsr()->store('review_types', $types);
    }

    /**
     * @return void
     * @action init
     */
    public function registerShortcodes()
    {
        $this->execute(new RegisterShortcodes([
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ]));
    }

    /**
     * @return void
     * @action init
     */
    public function registerTaxonomy()
    {
        $this->execute(new RegisterTaxonomy([
            'hierarchical' => true,
            'meta_box_cb' => [glsr(MetaboxController::class), 'renderTaxonomyMetabox'],
            'public' => false,
            'rest_controller_class' => RestCategoryController::class,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'show_ui' => true,
        ]));
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
        $this->execute(new RegisterWidgets([
            'site-reviews' => [
                'description' => _x('Site Reviews: Display your recent reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Recent Reviews', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-form' => [
                'description' => _x('Site Reviews: Display a form to submit reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Submit a Review', 'admin-text', 'site-reviews'),
            ],
            'site-reviews-summary' => [
                'description' => _x('Site Reviews: Display a summary of your reviews.', 'admin-text', 'site-reviews'),
                'name' => _x('Summary of Reviews', 'admin-text', 'site-reviews'),
            ],
        ]));
    }
}
