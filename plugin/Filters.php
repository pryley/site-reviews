<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
// use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\MainController;
// use GeminiLabs\SiteReviews\Controllers\PublicController;
// use GeminiLabs\SiteReviews\Modules\Translator;

class Filters implements HooksContract
{
	protected $app;
	protected $admin;
	protected $basename;
	// protected $editor;
	protected $listtable;
	protected $main;
	// protected $public;
	// protected $translator;

	public function __construct( Application $app ) {
		$this->app = $app;
		$this->admin = $app->make( AdminController::class );
		$this->basename = plugin_basename( $app->file );
		// $this->editor = $app->make( EditorController::class );
		$this->listtable = $app->make( ListTableController::class );
		$this->main = $app->make( MainController::class );
		// $this->public = $app->make( PublicController::class );
		// $this->translator = $app->make( Translator::class );
	}

	/**
	 * @return void
	 */
	public function run()
	{
		add_filter( 'mce_external_plugins',                                    [$this->admin, 'filterTinymcePlugins'], 15 );
		add_filter( 'plugin_action_links_'.$this->basename,                    [$this->admin, 'filterActionLinks'] );
		add_filter( 'dashboard_glance_items',                                  [$this->admin, 'filterDashboardGlanceItems'] );
		// add_filter( 'wp_editor_settings',                                      [$this->editor, 'filterEditorSettings'] );
		// add_filter( 'the_editor',                                              [$this->editor, 'filterEditorTextarea'] );
		// add_filter( 'gettext',                                                 [$this->editor, 'filterPostStatusLabels'], 10, 3 );
		// add_filter( 'gettext_with_context',                                    [$this->editor, 'filterPostStatusLabelsWithContext'], 10, 4 );
		// add_filter( 'post_updated_messages',                                   [$this->editor, 'filterUpdateMessages'] );
		add_filter( 'bulk_post_updated_messages',                              [$this->listtable, 'filterBulkUpdateMessages'], 10, 2 );
		add_filter( 'manage_'.Application::POST_TYPE.'_posts_columns',         [$this->listtable, 'filterColumnsForPostType'] );
		add_filter( 'default_hidden_columns',                                  [$this->listtable, 'filterDefaultHiddenColumns'], 10, 2 );
		add_filter( 'post_row_actions',                                        [$this->listtable, 'filterRowActions'], 10, 2 );
		add_filter( 'manage_edit-'.Application::POST_TYPE.'_sortable_columns', [$this->listtable, 'filterSortableColumns'] );
		add_filter( 'ngettext',                                                [$this->listtable, 'filterStatusText'], 10, 5 );
		// add_filter( 'script_loader_tag',                                       [$this->public, 'filterEnqueuedScripts'], 10, 2 );
		// add_filter( 'query_vars',                                              [$this->public, 'filterQueryVars'] );
		// add_filter( 'gettext',                                                 [$this->translator, 'filterGettext'], 10, 3 );
		// add_filter( 'gettext_with_context',                                    [$this->translator, 'filterGettextWithContext'], 10, 4 );
		// add_filter( 'ngettext',                                                [$this->translator, 'filterNgettext'], 10, 5 );
		// add_filter( 'ngettext_with_context',                                   [$this->translator, 'filterNgettextWithContext'], 10, 6 );


	}
}
