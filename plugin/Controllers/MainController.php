<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Compat;
use GeminiLabs\SiteReviews\Commands\RegisterPostMeta;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Install;

class MainController extends AbstractController
{
    /**
     * Core drops the site tables in wp_uninitialize_site() at priority 10.
     * A foreign key on a custom table would make MySQL refuse to drop the table it points at.
     *
     * @action wp_uninitialize_site:5
     */
    public function dropSiteForeignConstraints(\WP_Site $site): void
    {
        switch_to_blog((int) $site->blog_id);
        try {
            glsr(Tables::class)->dropForeignConstraints();
        } finally {
            restore_current_blog(); // core deletes the site in the context it started in
        }
    }

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
        // Tables are dropped in array order.
        // The other custom tables point at the ratings table, and the ratings table points at the posts table.
        $customTables = [];
        $ratingsTable = [];
        foreach (glsr(Tables::class)->tables() as $classname) {
            $table = glsr($classname);
            if ($table instanceof TableRatings) {
                $ratingsTable[$table->name($prefixName = true)] = $table->tablename;
                continue;
            }
            $customTables[$table->name($prefixName = true)] = $table->tablename;
        }
        return $customTables + $ratingsTable + $tables;
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
     * @param ?string $data We are not enforcing the type because the "wp_footer" hook does not have a parameter
     *
     * @action admin_footer
     * @action wp_footer
     */
    public function logOnce($data = ''): void
    {
        if ('update.php' !== $data) {
            glsr_log()->logOnce();
        }
    }

    /**
     * Initialize the Application settings config and defaults.
     *
     * @action init:5
     */
    public function onInit(): void
    {
        $defaults = glsr()->defaults();
        glsr(OptionManager::class)->mergeDefaults($defaults);
        glsr(OptionManager::class)->updateVersion();
    }

    /**
     * @action site-reviews/migration/end
     */
    public function onMigrationEnd(): void
    {
        // This method persists what it reads. The raw migration writes can
        // leave the options cache stale; a stale read overwrites the settings.
        OptionManager::flushSettingsCache();
        $settings = glsr(OptionManager::class)->reset(); // fresh composed view
        $settings = glsr(OptionManager::class)->clean($settings);
        glsr(OptionManager::class)->replace($settings); // persists addon settings to their own options
    }

    /**
     * @action parse_query
     */
    public function parseAssignedPostTypesInQuery(\WP_Query $query): void
    {
        if (glsr()->prefix.'assigned_posts' !== $query->get('post_type')) {
            return;
        }
        $postTypes = get_post_types([
            '_builtin' => false,
            'public' => true,
            'show_in_rest' => true,
            'show_ui' => true,
        ]);
        $postTypes[] = 'post';
        $postTypes[] = 'page';
        $query->is_archive = false;
        $query->is_post_type_archive = false;
        $query->set('post_type', array_map('sanitize_key', array_values($postTypes)));
    }

    /**
     * @action plugins_loaded:-50
     */
    public function registerAddons(): void
    {
        glsr()->action('addon/register', glsr(Compat::class)); // @compat
        glsr()->action('premium/register', glsr());
    }

    /**
     * Languages are loaded before "init" because the setting config uses translated strings.
     *
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
     * @action init:5
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
        if (glsr()->filterBool('register/widgets', true)) {
            $this->execute(new RegisterWidgets());
        }
    }
}
