<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Settings;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;
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
			$menu[$key][0] .= ' '.$awaitingModeration;
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
			'addons' => __( 'Addons', 'site-reviews' ),
			'documentation' => __( 'Documentation', 'site-reviews' ),
		]);
		foreach( $pages as $slug => $title ) {
			$method = glsr( Helper::class )->buildMethodName( 'render-'.$slug.'-menu' );
			$callback = apply_filters( 'site-reviews/addon/submenu/callback', [$this, $method], $slug );
			if( !is_callable( $callback ))continue;
			add_submenu_page( 'edit.php?post_type='.Application::POST_TYPE, $title, $title, glsr()->constant( 'CAPABILITY' ), $slug, $callback );
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
			'addons' => __( 'Addons', 'site-reviews' ),
		]);
		$addons = apply_filters( 'site-reviews/addon/documentation', [] );
		ksort( $addons );
		if( empty( $addons )) {
			unset( $tabs['addons'] );
		}
		$this->renderPage( 'documentation', [
			'addons' => $addons,
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
			'addons' => __( 'Addons', 'site-reviews' ),
			'licenses' => __( 'Licenses', 'site-reviews' ),
		]);
		if( empty( glsr( Helper::class )->getPathValue( 'settings.addons', glsr()->defaults ))) {
			unset( $tabs['addons'] );
		}
		if( empty( glsr( Helper::class )->getPathValue( 'settings.licenses', glsr()->defaults ))) {
			unset( $tabs['licenses'] );
		}
		$this->renderPage( 'settings', [
			'notices' => $this->getNotices(),
			'settings' => glsr( Settings::class ),
			'tabs' => $tabs,
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
			'general' => __( 'General', 'site-reviews' ),
			'sync' => __( 'Sync Reviews', 'site-reviews' ),
			'console' => __( 'Console', 'site-reviews' ),
			'system-info' => __( 'System Info', 'site-reviews' ),
		]);
		if( !apply_filters( 'site-reviews/addon/sync/enable', false )) {
			unset( $tabs['sync'] );
		}
		$this->renderPage( 'tools', [
			'data' => [
				'context' => [
					'base_url' => admin_url( 'edit.php?post_type='.Application::POST_TYPE ),
					'console' => strval( glsr( Console::class )),
					'id' => Application::ID,
					'system' => strval( glsr( System::class )),
				],
				'services' => apply_filters( 'site-reviews/addon/sync/services', [] ),
			],
			'notices' => $this->getNotices(),
			'tabs' => $tabs,
			'template' => glsr( Template::class ),
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
	 * @return string
	 */
	protected function getNotices()
	{
		return glsr( Builder::class )->div( glsr( Notice::class )->get(), [
			'id' => 'glsr-notices',
		]);
	}

	/**
	 * @param string $hookSuffix
	 * @return array
	 */
	protected function parseWithFilter( $hookSuffix, array $args = [] )
	{
		return apply_filters( 'site-reviews/addon/'.$hookSuffix, $args );
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
