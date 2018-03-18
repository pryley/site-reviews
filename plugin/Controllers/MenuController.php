<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
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
				$menu[$key][4] .= ' current';
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
		$pages = apply_filters( 'site-reviews/addon/submenu/pages', [] );
		$pages = wp_parse_args( $pages, [
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
		$tabs = [
			'addons' => __( 'Add-Ons', 'site-reviews' ),
		];
		$this->renderMenu( 'addons', $tabs );
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderDocumentationMenu()
	{
		$tabs = apply_filters( 'site-reviews/addon/documentation/tabs', [] );
		$tabs = wp_parse_args( $tabs, [
			'support' => __( 'Support', 'site-reviews' ),
			'faq' => __( 'FAQ', 'site-reviews' ),
			'shortcodes' => __( 'Shortcodes', 'site-reviews' ),
			'hooks' => __( 'Hooks', 'site-reviews' ),
			'functions' => __( 'Functions', 'site-reviews' ),
		]);
		$this->renderMenu( 'documentation', $tabs );
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderSettingsMenu()
	{
		$tabs = apply_filters( 'site-reviews/addon/settings/tabs', [] );
		$tabs = wp_parse_args( $tabs, [
			'general' => __( 'General', 'site-reviews' ),
			'reviews' => __( 'Reviews', 'site-reviews' ),
			'submissions' => __( 'Submissions', 'site-reviews' ),
			'schema' => __( 'Schema', 'site-reviews' ),
			'translations' => __( 'Translations', 'site-reviews' ),
			'licenses' => __( 'Licenses', 'site-reviews' ),
		]);
		$this->renderMenu( 'settings', $tabs, [
			'databaseKey' => OptionManager::databaseKey(),
			'settings' => glsr()->getDefaults(),
		]);
	}

	/**
	 * @return void
	 * @see $this->registerSubMenus()
	 * @callback add_submenu_page
	 */
	public function renderToolsMenu()
	{
		$tabs = apply_filters( 'site-reviews/addon/tools/tabs', [] );
		$tabs = wp_parse_args( $tabs, [
			'import-export' => __( 'Import/Export', 'site-reviews' ),
			'logging' => __( 'Logging', 'site-reviews' ),
			'system-info' => __( 'System Info', 'site-reviews' ),
		]);
		$this->renderMenu( 'tools', $tabs, [
			'id' => Application::ID,
			'logger' => glsr( Logger::class ),
			'prefix' => Application::PREFIX,
			'system' => glsr( System::class ),
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
	 * @param string $tab
	 * @return string
	 */
	protected function getCurrentSection( array $tabs, $tab )
	{
		$currentSection = filter_input( INPUT_GET, 'section' );
		if( empty( $tabs[$tab]['sections'][$currentSection] )) {
			$currentSection = isset( $tabs[$tab]['sections'] )
				? key( $tabs[$tab]['sections'] )
				: '';
		}
		return $currentSection;
	}

	/**
	 * @return string
	 */
	protected function getCurrentTab( array $tabs )
	{
		$currentTab = filter_input( INPUT_GET, 'tab' );
		if( !array_key_exists( $currentTab, $tabs )) {
			$currentTab = key( $tabs );
		}
		return $currentTab;
	}

	/**
	 * @return array
	 */
	protected function normalizeTabs( array $tabs )
	{
		foreach( $tabs as $key => $value ) {
			if( !is_array( $value )) {
				$value = ['title' => $value];
			}
			$tabs[$key] = wp_parse_args( $value, [
				'sections' => [],
				'title' => '',
			]);
			$useLicenses = apply_filters( 'site-reviews/addon/licenses', false );
			if( !$useLicenses ) {
				unset( $tabs['licenses'] );
			}
		}
		return $tabs;
	}

	/**
	 * @param string $page
	 * @return void
	 */
	protected function renderMenu( $page, array $tabs, array $data = [] )
	{
		$tabs = $this->normalizeTabs( $tabs );
		$tab = $this->getCurrentTab( $tabs );
		$section = $this->getCurrentSection( $tabs, $tab );
		$defaults = [
			'currentSection' => $section,
			'currentTab' => $tab,
			'page' => $page,
			'tabs' => $tabs,
		];
		$data = apply_filters( 'site-reviews/addon/page/data', $data, $defaults );
		glsr()->render( 'pages/index', wp_parse_args( $data, $defaults ));
	}
}
