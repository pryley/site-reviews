<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Actions;
use GeminiLabs\SiteReviews\Container;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Filters;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Upgrader;

final class Application extends Container
{
	const CAPABILITY = 'edit_others_pages';
	const CRON_EVENT = 'site-reviews/schedule/session/purge';
	const ID = 'site-reviews';
	const PAGED_QUERY_VAR = 'reviews-page';
	const POST_TYPE = 'site-review';
	const PREFIX = 'glsr_';
	const TAXONOMY = 'site-review-category';

	public $defaults;
	public $file;
	public $languages;
	public $mceShortcodes = []; //defined elsewhere
	public $name;
	public $reviewTypes;
	public $schemas = []; //defined elsewhere
	public $version;

	public function __construct()
	{
		static::$instance = $this;
		$this->file = realpath( dirname( __DIR__ ).'/'.static::ID.'.php' );
		$plugin = get_file_data( $this->file, [
			'languages' => 'Domain Path',
			'name' => 'Plugin Name',
			'version' => 'Version',
		], 'plugin' );
		array_walk( $plugin, function( $value, $key ) {
			$this->$key = $value;
		});
	}

	/**
	 * @return void
	 */
	public function activate()
	{
		$this->make( DefaultsManager::class )->set();
		$this->scheduleCronJob();
		$this->upgrade();
	}

	/**
	 * @return void
	 */
	public function deactivate()
	{
		$this->unscheduleCronJob();
	}

	/**
	 * @return array
	 */
	public function getDefaults()
	{
		if( empty( $this->defaults )) {
			$this->defaults = $this->make( DefaultsManager::class )->get();
			$this->upgrade();
		}
		return apply_filters( 'site-reviews/get/defaults', $this->defaults );
	}

	/**
	 * @return bool
	 */
	public function hasPermission()
	{
		$isAdmin = $this->isAdmin();
		return !$isAdmin || ( $isAdmin && current_user_can( static::CAPABILITY ));
	}

	/**
	 * @return void
	 */
	public function init()
	{
		$this->make( Actions::class )->run();
		$this->make( Filters::class )->run();
	}

	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return is_admin() && !wp_doing_ajax();
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function path( $file = '' )
	{
		return plugin_dir_path( $this->file ).ltrim( trim( $file ), '/' );
	}

	/**
	 * @return void
	 */
	public function registerAddons()
	{
		do_action( 'site-reviews/addon/register', $this );
	}

	/**
	 * @return void
	 */
	public function registerLanguages()
	{
		load_plugin_textdomain( static::ID, false,
			trailingslashit( plugin_basename( $this->path() ).'/'.$this->languages )
		);
	}

	/**
	 * @return void
	 */
	public function registerReviewTypes()
	{
		$types = apply_filters( 'site-reviews/addon/types', [] );
		$this->reviewTypes = wp_parse_args( $types, [
			'local' => __( 'Local', 'site-reviews' ),
		]);
	}

	/**
	 * @param string $view
	 * @return void
	 */
	public function render( $view, array $data = [] )
	{
		$file = '';
		if( glsr( Helper::class )->startsWith( $view, 'templates/' )) {
			$file = str_replace( 'templates/', 'site-reviews/', $view ).'.php';
			$file = get_stylesheet_directory().'/'.$file;
		}
		if( !file_exists( $file )) {
			$file = $this->path( 'views/'.$view.'.php' );
		}
		$file = apply_filters( 'site-reviews/addon/views/file', $file, $view, $data );
		if( !file_exists( $file )) {
			glsr_log()->error( 'File not found: '.$file );
			return;
		}
		$data = apply_filters( 'site-reviews/addon/views/data', $data, $view );
		extract( $data );
		include $file;
	}

	/**
	 * @return void
	 */
	public function scheduleCronJob()
	{
		if( wp_next_scheduled( static::CRON_EVENT ))return;
		wp_schedule_event( time(), 'twicedaily', static::CRON_EVENT );
	}

	/**
	 * @return void
	 */
	public function unscheduleCronJob()
	{
		wp_unschedule_event( intval( wp_next_scheduled( static::CRON_EVENT )), static::CRON_EVENT );
	}

	/**
	 * @return void
	 */
	public function upgrade()
	{
		$this->make( Upgrader::class )->run();
	}

	/**
	 * @param mixed $upgrader
	 * @return void
	 * @action upgrader_process_complete
	 */
	public function upgraded( $upgrader, array $data )
	{
		if( !array_key_exists( 'plugins', $data )
			|| !in_array( plugin_basename( $this->file ), $data['plugins'] )
			|| $data['action'] != 'update'
			|| $data['type'] != 'plugin'
		)return;
		$this->upgrade();
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function url( $path = '' )
	{
		return esc_url( plugin_dir_url( $this->file ).ltrim( trim( $path ), '/' ));
	}
}
