<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\RegisterPostMeta;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Install;

class MainController extends AbstractController
{
    /**
     * switch_to_blog() has run before this hook is triggered.
     *
     * @see http://developer.wordpress.org/reference/functions/wp_uninitialize_site/
     *
     * @param string[] $tables
     *
     * @return string[]
     *
     * @filter wpmu_drop_tables:999
     */
    public function filterDropTables(array $tables): array
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
     * @action wp_initialize_site:999
     */
    public function installOnNewSite(\WP_Site $site): void
    {
        if (is_plugin_active_for_network(glsr()->basename)) {
            glsr(Install::class)->runOnSite($site->blog_id);
        }
    }

    /**
     * @action admin_footer
     * @action wp_footer
     */
    public function logOnce(): void
    {
        glsr_log()->logOnce();
    }

    /**
     * @action plugins_loaded
     */
    public function registerAddons(): void
    {
        glsr()->action('addon/register', glsr());
    }

    /**
     * @action after_setup_theme
     */
    public function registerLanguages(): void
    {
        load_plugin_textdomain(glsr()->id, false,
            trailingslashit(plugin_basename(glsr()->path()).'/'.glsr()->languages)
        );
    }

    /**
     * @action init
     */
    public function registerPostMeta(): void
    {
        $this->execute(new RegisterPostMeta());
    }

    /**
     * @action init
     */
    public function registerPostType(): void
    {
        $this->execute(new RegisterPostType());
    }

    /**
     * @action init
     */
    public function registerReviewTypes(): void
    {
        $types = glsr()->filterArray('review/types', []);
        $types = wp_parse_args($types, [
            'local' => _x('Local Review', 'admin-text', 'site-reviews'),
        ]);
        glsr()->store('review_types', $types);
    }

    /**
     * @action init
     */
    public function registerShortcodes(): void
    {
        $this->execute(new RegisterShortcodes());
    }

    /**
     * @action init
     */
    public function registerTaxonomy(): void
    {
        $this->execute(new RegisterTaxonomy());
    }

    /**
     * @action widgets_init
     */
    public function registerWidgets(): void
    {
        $this->execute(new RegisterWidgets());
    }

    /**
     * @action wp_loaded
     */
    public function updateAddons(): void
    {
        glsr()->action('addon/update', glsr());
    }
}
