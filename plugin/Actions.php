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
use GeminiLabs\SiteReviews\Controllers\TrustalyzeController;
use GeminiLabs\SiteReviews\Controllers\ReviewController;
use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Controllers\TaxonomyController;
use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Controllers\WelcomeController;
use GeminiLabs\SiteReviews\Modules\Console;

class Actions implements HooksContract
{
    protected $about;
    protected $admin;
    protected $app;
    protected $blocks;
    protected $console;
    protected $editor;
    protected $listtable;
    protected $menu;
    protected $main;
    protected $notices;
    protected $public;
    protected $review;
    protected $router;
    protected $settings;
    protected $taxonomy;
    protected $translator;
    protected $trustalyze;
    protected $welcome;

    public function __construct(Application $app ) {
        $this->app = $app;
        $this->admin = $app->make(AdminController::class);
        $this->blocks = $app->make(BlocksController::class);
        $this->console = $app->make(Console::class);
        $this->editor = $app->make(EditorController::class);
        $this->listtable = $app->make(ListTableController::class);
        $this->main = $app->make(MainController::class);
        $this->menu = $app->make(MenuController::class);
        $this->notices = $app->make(NoticeController::class);
        $this->public = $app->make(PublicController::class);
        $this->review = $app->make(ReviewController::class);
        $this->router = $app->make(Router::class);
        $this->settings = $app->make(SettingsController::class);
        $this->taxonomy = $app->make(TaxonomyController::class);
        $this->translator = $app->make(TranslationController::class);
        $this->trustalyze = $app->make(TrustalyzeController::class);
        $this->welcome = $app->make(WelcomeController::class);
    }

    /**
     * @return void
     */
    public function run()
    {
        add_action('admin_enqueue_scripts',                                 [$this->admin, 'enqueueAssets']);
        add_action('admin_init',                                            [$this->admin, 'registerTinymcePopups']);
        add_action('media_buttons',                                         [$this->admin, 'renderTinymceButton'], 11);
        add_action('admin_init',                                            [$this->admin, 'runMigrations']);
        add_action('plugins_loaded',                                        [$this->app, 'getDefaults'], 11);
        add_action('plugins_loaded',                                        [$this->app, 'registerAddons']);
        add_action('plugins_loaded',                                        [$this->app, 'registerLanguages']);
        add_action('plugins_loaded',                                        [$this->app, 'registerReviewTypes']);
        add_action('admin_init',                                            [$this->app, 'setDefaults']);
        add_action('init',                                                  [$this->blocks, 'registerAssets'], 9);
        add_action('init',                                                  [$this->blocks, 'registerBlocks']);
        add_action('admin_footer',                                          [$this->console, 'logOnce']);
        add_action('wp_footer',                                             [$this->console, 'logOnce']);
        add_action('add_meta_boxes_'.Application::POST_TYPE,                [$this->editor, 'registerMetaBoxes']);
        add_action('admin_print_scripts',                                   [$this->editor, 'removeAutosave'], 999);
        add_action('admin_menu',                                            [$this->editor, 'removeMetaBoxes']);
        add_action('current_screen',                                        [$this->editor, 'removePostTypeSupport']);
        add_action('post_submitbox_misc_actions',                           [$this->editor, 'renderPinnedInPublishMetaBox']);
        add_action('admin_head',                                            [$this->editor, 'renderReviewFields']);
        add_action('admin_action_revert',                                   [$this->editor, 'revertReview']);
        add_action('save_post_'.Application::POST_TYPE,                     [$this->editor, 'saveMetaboxes'], 10, 3);
        add_action('admin_action_approve',                                  [$this->listtable, 'approve']);
        add_action('bulk_edit_custom_box',                                  [$this->listtable, 'renderBulkEditFields'], 10, 2);
        add_action('restrict_manage_posts',                                 [$this->listtable, 'renderColumnFilters']);
        add_action('manage_'.Application::POST_TYPE.'_posts_custom_column', [$this->listtable, 'renderColumnValues'], 10, 2);
        add_action('save_post_'.Application::POST_TYPE,                     [$this->listtable, 'saveBulkEditFields']);
        add_action('pre_get_posts',                                         [$this->listtable, 'setQueryForColumn']);
        add_action('admin_action_unapprove',                                [$this->listtable, 'unapprove']);
        add_action('init',                                                  [$this->main, 'registerPostType'], 8);
        add_action('init',                                                  [$this->main, 'registerShortcodes']);
        add_action('init',                                                  [$this->main, 'registerTaxonomy']);
        add_action('widgets_init',                                          [$this->main, 'registerWidgets']);
        add_action('admin_menu',                                            [$this->menu, 'registerMenuCount']);
        add_action('admin_menu',                                            [$this->menu, 'registerSubMenus']);
        add_action('admin_init',                                            [$this->menu, 'setCustomPermissions'], 999);
        add_action('admin_notices',                                         [$this->notices, 'filterAdminNotices']);
        add_action('wp_enqueue_scripts',                                    [$this->public, 'enqueueAssets'], 999);
        add_filter('site-reviews/builder',                                  [$this->public, 'modifyBuilder']);
        add_action('wp_footer',                                             [$this->public, 'renderSchema']);
        add_action('set_object_terms',                                      [$this->review, 'onAfterChangeCategory'], 10, 6);
        add_action('transition_post_status',                                [$this->review, 'onAfterChangeStatus'], 10, 3);
        add_action('site-reviews/review/created',                           [$this->review, 'onAfterCreate']);
        add_action('before_delete_post',                                    [$this->review, 'onBeforeDelete']);
        add_action('update_postmeta',                                       [$this->review, 'onBeforeUpdate'], 10, 4);
        add_action('admin_init',                                            [$this->router, 'routeAdminPostRequest']);
        add_action('wp_ajax_'.Application::PREFIX.'action',                 [$this->router, 'routeAjaxRequest']);
        add_action('wp_ajax_nopriv_'.Application::PREFIX.'action',          [$this->router, 'routeAjaxRequest']);
        add_action('init',                                                  [$this->router, 'routePublicPostRequest']);
        add_action('admin_init',                                            [$this->settings, 'registerSettings']);
        add_action(Application::TAXONOMY.'_term_edit_form_top',             [$this->taxonomy, 'disableParents']);
        add_action(Application::TAXONOMY.'_term_new_form_tag',              [$this->taxonomy, 'disableParents']);
        add_action(Application::TAXONOMY.'_add_form_fields',                [$this->taxonomy, 'enableParents']);
        add_action(Application::TAXONOMY.'_edit_form',                      [$this->taxonomy, 'enableParents']);
        add_action('restrict_manage_posts',                                 [$this->taxonomy, 'renderTaxonomyFilter'], 9);
        add_action('set_object_terms',                                      [$this->taxonomy, 'restrictTermSelection'], 9, 6);
        add_action('plugins_loaded',                                        [$this->translator, 'addTranslationFilters']);
        add_action('admin_enqueue_scripts',                                 [$this->translator, 'translatePostStatusLabels']);
        add_action('site-reviews/review/created',                           [$this->trustalyze, 'onCreated']);
        add_action('site-reviews/review/reverted',                          [$this->trustalyze, 'onReverted']);
        add_action('site-reviews/review/saved',                             [$this->trustalyze, 'onSaved']);
        add_action('updated_postmeta',                                      [$this->trustalyze, 'onUpdatedMeta'], 10, 3);
        add_action('activated_plugin',                                      [$this->welcome, 'redirectOnActivation'], 10, 2);
        add_action('admin_menu',                                            [$this->welcome, 'registerPage']);
    }
}
