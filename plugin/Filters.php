<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\BlocksController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Controllers\TrustalyzeController;
use GeminiLabs\SiteReviews\Controllers\WelcomeController;
use GeminiLabs\SiteReviews\Modules\Translator;

class Filters implements HooksContract
{
    protected $admin;
    protected $app;
    protected $basename;
    protected $blocks;
    protected $editor;
    protected $listtable;
    protected $public;
    protected $translator;
    protected $trustalyze;
    protected $welcome;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->admin = $app->make(AdminController::class);
        $this->basename = plugin_basename($app->file);
        $this->blocks = $app->make(BlocksController::class);
        $this->editor = $app->make(EditorController::class);
        $this->listtable = $app->make(ListTableController::class);
        $this->public = $app->make(PublicController::class);
        $this->translator = $app->make(TranslationController::class);
        $this->trustalyze = $app->make(TrustalyzeController::class);
        $this->welcome = $app->make(WelcomeController::class);
    }

    /**
     * @return void
     */
    public function run()
    {
        add_filter('map_meta_cap',                                              [$this->admin, 'filterCreateCapability'], 10, 2);
        add_filter('mce_external_plugins',                                      [$this->admin, 'filterTinymcePlugins'], 15);
        add_filter('plugin_action_links_'.$this->basename,                      [$this->admin, 'filterActionLinks']);
        add_filter('dashboard_glance_items',                                    [$this->admin, 'filterDashboardGlanceItems']);
        add_filter('block_categories',                                          [$this->blocks, 'filterBlockCategories']);
        add_filter('classic_editor_enabled_editors_for_post_type',              [$this->blocks, 'filterEnabledEditors'], 10, 2);
        add_filter('use_block_editor_for_post_type',                            [$this->blocks, 'filterUseBlockEditor'], 10, 2);
        add_filter('wp_editor_settings',                                        [$this->editor, 'filterEditorSettings']);
        add_filter('the_editor',                                                [$this->editor, 'filterEditorTextarea']);
        add_filter('is_protected_meta',                                         [$this->editor, 'filterIsProtectedMeta'], 10, 3);
        add_filter('post_updated_messages',                                     [$this->editor, 'filterUpdateMessages']);
        add_filter('manage_'.Application::POST_TYPE.'_posts_columns',           [$this->listtable, 'filterColumnsForPostType']);
        add_filter('post_date_column_status',                                   [$this->listtable, 'filterDateColumnStatus'], 10, 2);
        add_filter('default_hidden_columns',                                    [$this->listtable, 'filterDefaultHiddenColumns'], 10, 2);
        add_filter('post_row_actions',                                          [$this->listtable, 'filterRowActions'], 10, 2);
        add_filter('manage_edit-'.Application::POST_TYPE.'_sortable_columns',   [$this->listtable, 'filterSortableColumns']);
        add_filter('script_loader_tag',                                         [$this->public, 'filterEnqueuedScripts'], 10, 2);
        add_filter('site-reviews/config/forms/submission-form',                 [$this->public, 'filterFieldOrder'], 11);
        add_filter('site-reviews/render/view',                                  [$this->public, 'filterRenderView']);
        add_filter('bulk_post_updated_messages',                                [$this->translator, 'filterBulkUpdateMessages'], 10, 2);
        add_filter('display_post_states',                                       [$this->translator, 'filterPostStates'], 10, 2);
        add_filter('site-reviews/gettext/default',                              [$this->translator, 'filterPostStatusLabels'], 10, 2);
        add_filter('site-reviews/gettext_with_context/default',                 [$this->translator, 'filterPostStatusLabels'], 10, 2);
        add_filter('site-reviews/ngettext/default',                             [$this->translator, 'filterPostStatusText'], 10, 4);
        add_filter('site-reviews/settings/callback',                            [$this->trustalyze, 'filterSettingsCallback']);
        add_filter('site-reviews/interpolate/partials/form/table-row-multiple', [$this->trustalyze, 'filterSettingsTableRow'], 10, 3);
        add_filter('plugin_action_links_'.$this->basename,                      [$this->welcome, 'filterActionLinks'], 9);
        add_filter('admin_title',                                               [$this->welcome, 'filterAdminTitle']);
        add_filter('admin_footer_text',                                         [$this->welcome, 'filterFooterText']);
    }
}
