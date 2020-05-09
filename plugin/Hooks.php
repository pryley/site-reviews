<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\BlocksController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Controllers\NoticeController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Controllers\ReviewController;
use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Controllers\TaxonomyController;
use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Controllers\TrustalyzeController;
use GeminiLabs\SiteReviews\Controllers\WelcomeController;
use GeminiLabs\SiteReviews\Modules\Translation;

class Hooks implements HooksContract
{
    protected $admin;
    protected $basename;
    protected $blocks;
    protected $editor;
    protected $listtable;
    protected $main;
    protected $menu;
    protected $notices;
    protected $public;
    protected $review;
    protected $router;
    protected $settings;
    protected $taxonomy;
    protected $translator;
    protected $trustalyze;
    protected $welcome;

    public function __construct()
    {
        $this->admin = glsr(AdminController::class);
        $this->basename = plugin_basename(glsr()->file);
        $this->blocks = glsr(BlocksController::class);
        $this->editor = glsr(EditorController::class);
        $this->listtable = glsr(ListTableController::class);
        $this->main = glsr(MainController::class);
        $this->menu = glsr(MenuController::class);
        $this->notices = glsr(NoticeController::class);
        $this->public = glsr(PublicController::class);
        $this->review = glsr(ReviewController::class);
        $this->router = glsr(Router::class);
        $this->settings = glsr(SettingsController::class);
        $this->taxonomy = glsr(TaxonomyController::class);
        $this->translator = glsr(TranslationController::class);
        $this->trustalyze = glsr(TrustalyzeController::class);
        $this->welcome = glsr(WelcomeController::class);
    }

    /**
     * @return void
     */
    public function addActions()
    {
        add_action('plugins_loaded', [glsr(), 'getDefaultSettings'], 11);
        add_action('plugins_loaded', [glsr(), 'registerAddons']);
        add_action('plugins_loaded', [glsr(), 'registerLanguages']);
        add_action('plugins_loaded', [glsr(), 'registerReviewTypes']);
        add_action('admin_init', [glsr(), 'setDefaultSettings']);
        add_action('load-edit.php', [$this, 'translateAdminEditPage']);
        add_action('load-post.php', [$this, 'translateAdminPostPage']);
        add_action('plugins_loaded', [$this, 'translatePlugin']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueueAssets']);
        add_action('admin_init', [$this->admin, 'registerTinymcePopups']);
        add_action('media_buttons', [$this->admin, 'renderTinymceButton'], 11);
        add_action('admin_init', [$this->admin, 'runMigrations']);
        add_action('init', [$this->blocks, 'registerAssets'], 9);
        add_action('init', [$this->blocks, 'registerBlocks']);
        add_action('add_meta_boxes_'.glsr()->post_type, [$this->editor, 'registerMetaBoxes']);
        add_action('admin_print_scripts', [$this->editor, 'removeAutosave'], 999);
        add_action('admin_menu', [$this->editor, 'removeMetaBoxes']);
        add_action('current_screen', [$this->editor, 'removePostTypeSupport']);
        add_action('post_submitbox_misc_actions', [$this->editor, 'renderPinnedInPublishMetaBox']);
        add_action('admin_head', [$this->editor, 'renderReviewFields']);
        add_action('admin_action_revert', [$this->editor, 'revertReview']);
        add_action('save_post_'.glsr()->post_type, [$this->editor, 'saveMetaboxes'], 10, 3);
        add_action('admin_action_approve', [$this->listtable, 'approve']);
        add_action('bulk_edit_custom_box', [$this->listtable, 'renderBulkEditFields'], 10, 2);
        add_action('restrict_manage_posts', [$this->listtable, 'renderColumnFilters']);
        add_action('manage_'.glsr()->post_type.'_posts_custom_column', [$this->listtable, 'renderColumnValues'], 10, 2);
        add_action('save_post_'.glsr()->post_type, [$this->listtable, 'saveBulkEditFields']);
        add_action('pre_get_posts', [$this->listtable, 'setQueryForColumn']);
        add_action('admin_action_unapprove', [$this->listtable, 'unapprove']);
        add_action('admin_footer', [$this->main, 'logOnce']);
        add_action('wp_footer', [$this->main, 'logOnce']);
        add_action('init', [$this->main, 'registerPostType'], 8);
        add_action('init', [$this->main, 'registerShortcodes']);
        add_action('init', [$this->main, 'registerTaxonomy']);
        add_action('widgets_init', [$this->main, 'registerWidgets']);
        add_action('admin_menu', [$this->menu, 'registerMenuCount']);
        add_action('admin_menu', [$this->menu, 'registerSubMenus']);
        add_action('admin_init', [$this->menu, 'setCustomPermissions'], 999);
        add_action('admin_notices', [$this->notices, 'filterAdminNotices']);
        add_action('wp_enqueue_scripts', [$this->public, 'enqueueAssets'], 999);
        add_filter('site-reviews/builder', [$this->public, 'modifyBuilder']);
        add_action('wp_footer', [$this->public, 'renderSchema']);
        add_action('set_object_terms', [$this->review, 'onAfterChangeCategory'], 10, 6);
        add_action('transition_post_status', [$this->review, 'onAfterChangeStatus'], 10, 3);
        add_action('site-reviews/review/created', [$this->review, 'onAfterCreate']);
        add_action('delete_'.glsr()->taxonomy, [$this->review, 'onAfterDeleteCategory']);
        add_action('before_delete_post', [$this->review, 'onBeforeDelete']);
        add_action('update_postmeta', [$this->review, 'onBeforeUpdate'], 10, 4);
        add_action('admin_init', [$this->router, 'routeAdminPostRequest']);
        add_action('wp_ajax_'.glsr()->prefix.'action', [$this->router, 'routeAjaxRequest']);
        add_action('wp_ajax_nopriv_'.glsr()->prefix.'action', [$this->router, 'routeAjaxRequest']);
        add_action('init', [$this->router, 'routePublicPostRequest']);
        add_action('admin_init', [$this->settings, 'registerSettings']);
        add_action(glsr()->taxonomy.'_term_edit_form_top', [$this->taxonomy, 'disableParents']);
        add_action(glsr()->taxonomy.'_term_new_form_tag', [$this->taxonomy, 'disableParents']);
        add_action(glsr()->taxonomy.'_add_form_fields', [$this->taxonomy, 'enableParents']);
        add_action(glsr()->taxonomy.'_edit_form', [$this->taxonomy, 'enableParents']);
        add_action('restrict_manage_posts', [$this->taxonomy, 'renderTaxonomyFilter'], 9);
        add_action('set_object_terms', [$this->taxonomy, 'restrictTermSelection'], 9, 6);
        add_action('site-reviews/review/created', [$this->trustalyze, 'onCreated']);
        add_action('site-reviews/review/reverted', [$this->trustalyze, 'onReverted']);
        add_action('site-reviews/review/saved', [$this->trustalyze, 'onSaved']);
        add_action('updated_postmeta', [$this->trustalyze, 'onUpdatedMeta'], 10, 3);
        add_action('activated_plugin', [$this->welcome, 'redirectOnActivation'], 10, 2);
        add_action('admin_menu', [$this->welcome, 'registerPage']);
    }

    /**
     * @return void
     */
    public function addFilters()
    {
        add_filter('map_meta_cap', [$this->admin, 'filterCreateCapability'], 10, 2);
        add_filter('mce_external_plugins', [$this->admin, 'filterTinymcePlugins'], 15);
        add_filter('plugin_action_links_'.$this->basename, [$this->admin, 'filterActionLinks']);
        add_filter('dashboard_glance_items', [$this->admin, 'filterDashboardGlanceItems']);
        add_filter('block_categories', [$this->blocks, 'filterBlockCategories']);
        add_filter('classic_editor_enabled_editors_for_post_type', [$this->blocks, 'filterEnabledEditors'], 10, 2);
        add_filter('use_block_editor_for_post_type', [$this->blocks, 'filterUseBlockEditor'], 10, 2);
        add_filter('wp_editor_settings', [$this->editor, 'filterEditorSettings']);
        add_filter('the_editor', [$this->editor, 'filterEditorTextarea']);
        add_filter('is_protected_meta', [$this->editor, 'filterIsProtectedMeta'], 10, 3);
        add_filter('post_updated_messages', [$this->editor, 'filterUpdateMessages']);
        add_filter('manage_'.glsr()->post_type.'_posts_columns', [$this->listtable, 'filterColumnsForPostType']);
        add_filter('post_date_column_status', [$this->listtable, 'filterDateColumnStatus'], 10, 2);
        add_filter('default_hidden_columns', [$this->listtable, 'filterDefaultHiddenColumns'], 10, 2);
        add_filter('post_row_actions', [$this->listtable, 'filterRowActions'], 10, 2);
        add_filter('manage_edit-'.glsr()->post_type.'_sortable_columns', [$this->listtable, 'filterSortableColumns']);
        add_filter('script_loader_tag', [$this->public, 'filterEnqueuedScripts'], 10, 2);
        add_filter('site-reviews/config/forms/submission-form', [$this->public, 'filterFieldOrder'], 11);
        add_filter('site-reviews/render/view', [$this->public, 'filterRenderView']);
        add_filter('site-reviews/settings/callback', [$this->trustalyze, 'filterSettingsCallback']);
        add_filter('site-reviews/interpolate/partials/form/table-row-multiple', [$this->trustalyze, 'filterSettingsTableRow'], 10, 3);
        add_filter('plugin_action_links_'.$this->basename, [$this->welcome, 'filterActionLinks'], 9);
        add_filter('admin_title', [$this->welcome, 'filterAdminTitle']);
        add_filter('admin_footer_text', [$this->welcome, 'filterFooterText']);
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->addActions();
        $this->addFilters();
    }

    /**
     * @return void
     * @action load-edit.php
     */
    public function translateAdminEditPage()
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            add_filter('bulk_post_updated_messages', [$this->translator, 'filterBulkUpdateMessages'], 10, 2);
            add_filter('display_post_states', [$this->translator, 'filterPostStates'], 10, 2);
            add_filter('gettext', [$this->translator, 'filterPostStatusLabels'], 10, 3);
            add_filter('ngettext', [$this->translator, 'filterPostStatusText'], 10, 5);
        }
    }

    /**
     * @return void
     * @action load-post.php
     */
    public function translateAdminPostPage()
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            add_filter('gettext', [$this->translator, 'filterPostStatusLabels'], 10, 3);
            add_action('admin_print_scripts-post.php', [$this->translator, 'translatePostStatusLabels']);
        }
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function translatePlugin()
    {
        if (!empty(glsr(Translation::class)->translations())) {
            add_filter('gettext', [$this->translator, 'filterGettext'], 9, 3);
            add_filter('site-reviews/gettext/site-reviews', [$this->translator, 'filterGettextSiteReviews'], 10, 2);
            add_filter('gettext_with_context', [$this->translator, 'filterGettextWithContext'], 9, 4);
            add_filter('site-reviews/gettext_with_context/site-reviews', [$this->translator, 'filterGettextWithContextSiteReviews'], 10, 3);
            add_filter('ngettext', [$this->translator, 'filterNgettext'], 9, 5);
            add_filter('site-reviews/ngettext/site-reviews', [$this->translator, 'filterNgettextSiteReviews'], 10, 4);
            add_filter('ngettext_with_context', [$this->translator, 'filterNgettextWithContext'], 9, 6);
            add_filter('site-reviews/ngettext_with_context/site-reviews', [$this->translator, 'filterNgettextWithContextSiteReviews'], 10, 5);
        }
    }
}
