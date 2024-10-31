<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\RegisterPostMeta;
use GeminiLabs\SiteReviews\Commands\RegisterPostType;
use GeminiLabs\SiteReviews\Commands\RegisterShortcodes;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy;
use GeminiLabs\SiteReviews\Commands\RegisterWidgets;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Tables;
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
        // Custom tables have foreign indexes so they must be removed first!
        foreach (glsr(Tables::class)->tables() as $classname) {
            $table = glsr($classname);
            $tables = Arr::prepend($tables, $table->tablename, $table->name($prefixName = true));
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
        $settings = glsr(OptionManager::class)->wp(OptionManager::databaseKey(), []);
        $settings = glsr(OptionManager::class)->clean($settings);
        update_option(OptionManager::databaseKey(), $settings, true);
    }

    /**
     * @action plugins_loaded
     */
    public function registerAddons(): void
    {
        glsr()->action('addon/register', glsr());
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
}
