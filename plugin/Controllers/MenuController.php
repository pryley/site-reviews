<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Logger;
use GeminiLabs\SiteReviews\Modules\System;

class MenuController extends Controller
{
	/**
	 * @return void
	 * @action admin_menu
	 */
	public function registerMenuCount()
	{
		global $menu, $typenow;
		foreach( $menu as $key => $value ) {
			if( !isset( $value[2] ) || $value[2] != 'edit.php?post_type='.Application::POST_TYPE )continue;
			$postCount = wp_count_posts( Application::POST_TYPE );
			$pendingCount = glsr( Builder::class )->span( number_format_i18n( $postCount->pending ), [
				'class' => 'pending-count',
			]);
			$awaitingModeration = glsr( Builder::class )->span( $pendingCount, [
				'class' => 'awaiting-mod count-'.$postCount->pending,
			]);
			$menu[$key][0] .= $awaitingModeration;
			if( $typenow === Application::POST_TYPE ) {
				$menu[$key][4].= ' current';
			}
			break;
		}
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function registerSubMenus()
	{
		$pages = $this->parseWithFilter( 'submenu/pages', [
			'settings' => __( 'Settings', 'site-reviews' ),
			'tools' => __( 'Tools', 'site-reviews' ),
			'documentation' => __( 'Documentation', 'site-reviews' ),
			'addons' => __( 'Add-Ons', 'site-reviews' ),
		]);
		foreach( $pages as $slug => $title ) {
			$method = glsr( Helper::class )->buildMethodName( 'render-'.$slug.'-menu' );
			$callback = apply_filters( 'site-reviews/addon/submenu/callback', [$this, $method], $slug );
			if( !is_callable( $callback ))continue;
			add_submenu_page( 'edit.php?post_type='.Application::POST_TYPE, $title, $title, Application::CAPABILITY, $slug, $callback );
		}
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderAddonsMenu()
	{
		$this->renderPage( 'addons', [
			'template' => glsr( Template::class ),
		]);
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderDocumentationMenu()
	{
		$tabs = $this->parseWithFilter( 'documentation/tabs', [
			'support' => __( 'Support', 'site-reviews' ),
			'faq' => __( 'FAQ', 'site-reviews' ),
			'shortcodes' => __( 'Shortcodes', 'site-reviews' ),
			'hooks' => __( 'Hooks', 'site-reviews' ),
			'functions' => __( 'Functions', 'site-reviews' ),
		]);
		$this->renderPage( 'documentation', [
			'tabs' => $tabs,
		]);
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderSettingsMenu()
	{
		$tabs = $this->parseWithFilter( 'settings/tabs', [
			'general' => __( 'General', 'site-reviews' ),
			'reviews' => __( 'Reviews', 'site-reviews' ),
			'submissions' => __( 'Submissions', 'site-reviews' ),
			'schema' => __( 'Schema', 'site-reviews' ),
			'translations' => __( 'Translations', 'site-reviews' ),
			'licenses' => __( 'Licenses', 'site-reviews' ),
		]);
		if( !apply_filters( 'site-reviews/addon/licenses', false )) {
			unset( $tabs['licenses'] );
		}
		$this->renderPage( 'settings', [
			'tabs' => $tabs,
			'template' => glsr( Template::class ),
		]);
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderToolsMenu()
	{
		$tabs = $this->parseWithFilter( 'tools/tabs', [
			'import-export' => __( 'Import/Export', 'site-reviews' ),
			'logging' => __( 'Logging', 'site-reviews' ),
			'system-info' => __( 'System Info', 'site-reviews' ),
		]);
		$this->renderPage( 'tools', [
			'data' => [
				'id' => Application::ID,
				'logger' => glsr( Logger::class ),
				'prefix' => Application::PREFIX,
				'system' => glsr( System::class ),
			],
			'html' => glsr( Html::class ),
			'tabs' => $tabs,
		]);
	}

	/**
	 * @return void
	 * @action admin_init
	 */
	public function setCustomPermissions()
	{
		foreach( wp_roles()->roles as $role => $value ) {
			wp_roles()->remove_cap( $role, 'create_'.Application::POST_TYPE );
		}
	}

	/**
	 * @param string $hookSuffix
	 * @return array
	 */
	protected function parseWithFilter( $hookSuffix, array $args = [] )
	{
		$filteredArgs = apply_filters( 'site-reviews/addon/'.$hookSuffix, [] );
		return wp_parse_args( $filteredArgs, $args );
	}

	/**
	 * @param string $page
	 * @return void
	 */
	protected function renderPage( $page, array $data = [] )
	{
		$data['http_referer'] = (string)wp_get_referer();
		glsr()->render( 'pages/'.$page.'/index', $data );
	}
}
